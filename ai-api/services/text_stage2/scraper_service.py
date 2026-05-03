import re
from playwright.async_api import Page
from dateutil import parser
import json
from sklearn.metrics.pairwise import cosine_similarity
import numpy as np
import asyncio
semaphore = asyncio.Semaphore(3)
# ==============================
# CLEANING
# ==============================
def clean_text(text):
    if not text:
        return None

    text = re.sub(r'Baca juga:.*', '', text, flags=re.IGNORECASE)
    text = re.sub(r'\s+', ' ', text)
    return text.strip()


def filter_paragraphs(paragraphs):
    clean_paras = []

    bad_keywords = [
        "baca juga",
        "iklan",
        "advertisement",
        "copyright",
        "ikuti kami",
        "scroll",
        "scroll to continue",
        "lihat juga",
        "video:",
        "adsbygoogle"
    ]

    for p in paragraphs:
        if not p:
            continue

        p = p.strip()
        lower = p.lower()

        # 1. length filter (lebih ketat)
        if len(p) < 50:
            continue

        # 2. noise keyword filter
        if any(x in lower for x in bad_keywords):
            continue

        # 3. garbage pattern filter
        if p.startswith('"') and p.endswith('"') and len(p) < 80:
            continue

        # 4. mostly metadata junk
        if "..." in p and len(p) < 100:
            continue

        clean_paras.append(p)

    return clean_paras


# ==============================
# EXTRACT CONTENT
# ==============================
async def extract_content(page):
    try:
        paragraphs = await page.locator("article p, main p, div.entry-content p, p").all_text_contents()
        return filter_paragraphs(paragraphs)
    except:
        return []

# ==============================
# SCRAPE 1 ARTICLE
# ==============================
async def scrape_article(page, url):
    try:
        await page.goto(url, timeout=30000, wait_until="domcontentloaded")

        # ambil metadata
        title, date = await extract_metadata(page)
        date = await normalize_date(date)

        # ambil content
        paragraphs =  await extract_content(page)
        paragraphs = [clean_text(p) for p in paragraphs]

        if not paragraphs or len(paragraphs) < 3:
            print("EMPTY ARTICLE:", url)
        return {
            "url": url,
            "title": title,
            "date": date,
            "content": paragraphs
        }

    except Exception as e:
        print("SCRAPE ERROR:", url, str(e))
        return {
            "url": url,
            "title": None,
            "date": None,
            "content": []
        }


async def scrape_one(context, url):
    print("SCRAPING:", url)
    async with semaphore:
        page = await context.new_page()
        try:
            return await scrape_article(page, url)
        finally:
            await page.close()


async def scrape_all(browser, urls):
    context = await browser.new_context(
        user_agent="Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/120 Safari/537.36"
    )

    try:
        tasks = [
            scrape_one(context, url)
            for url in urls
            if url
        ]
        
        print("TASK COUNT:", len(tasks))

        results = await asyncio.gather(*tasks, return_exceptions=True)
        return results

    finally:
        await context.close()

# ==============================
# BUILD CHUNKS 
# ==============================
def build_chunks(content,artikel_id):
    chunks = []
    for i, paragraph in enumerate(content):
        chunks.append({
            "chunk": i + 1,
            "artikel_id": artikel_id,
            "text": paragraph
        })
    return chunks

# ==============================
# ADD VECTORS
# ============================== 
def add_vectors(chunks, model):
    texts = [c["text"] for c in chunks]
    vectors = model.encode(texts)

    for i, chunk in enumerate(chunks):
        chunk["vector"] = vectors[i].tolist()

    return chunks

async def extract_metadata(page):
    try:
        title = await page.locator("h1").first.text_content()
        title = title.strip() if title else None
    except:
        title = None

    selectors = [
        "meta[property='article:published_time']",
        "meta[name='publishdate']",
        "meta[name='date']",
        "time"
    ]

    for sel in selectors:
        try:
            el = page.locator(sel).first
            if sel == "time":
                date = await el.get_attribute("datetime") or await el.text_content()
            else:
                date = await el.get_attribute("content")

            if date:
                return title, date.strip()
        except:
            continue

    return title, None

async def normalize_date(date_str):
    if not date_str:
        return None

    try:
        dt = parser.parse(date_str)
        return dt.strftime("%Y-%m-%d")
    except:
        return None
    


def semantic_chunking(text, transformer,threshold=0.80):
    model = transformer
    paragraphs = [p.strip() for p in text.split("\n\n") if p.strip()]
    if not paragraphs:
        return []

    embeddings = model.encode(paragraphs)

    chunks = []
    current_chunk = [paragraphs[0]]
    current_embs = [embeddings[0]]

    for i in range(1, len(paragraphs)):
        centroid = np.mean(current_embs, axis=0).reshape(1, -1)

        sim = cosine_similarity(
            centroid,
            embeddings[i].reshape(1, -1)
        )[0][0]

        if sim >= threshold:
            current_chunk.append(paragraphs[i])
            current_embs.append(embeddings[i])
        else:
            chunks.append("\n\n".join(current_chunk))
            current_chunk = [paragraphs[i]]
            current_embs = [embeddings[i]]

    chunks.append("\n\n".join(current_chunk))

    return chunks