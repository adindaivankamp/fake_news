import re
from playwright.sync_api import sync_playwright
from dateutil import parser
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

    for p in paragraphs:
        p = p.strip()

        if len(p) < 30:
            continue

        if any(x in p.lower() for x in [
            "baca juga", "iklan", "advertisement",
            "copyright", "ikuti kami", "SCROLL","SCROLL TO CONTINUE WITH CONTENT"
        ]):
            continue

        clean_paras.append(p)

    return clean_paras


# ==============================
# EXTRACT CONTENT
# ==============================
def extract_content(page):
    selectors = [
        "article p",
        "main p",
        "div[data-component='text-block'] p",
        "div.read__content p",
        "div.entry-content p",
        "p"
    ]

    for sel in selectors:
        elements = page.locator(sel)
        paragraphs = elements.all_text_contents()

        paragraphs = filter_paragraphs(paragraphs)

        if paragraphs:
            print(f"PAKAI: {sel}")
            return paragraphs

    return []


# ==============================
# SCRAPE 1 ARTICLE
# ==============================
def scrape_article(page, url):
    try:
        page.goto(url, timeout=30000, wait_until="domcontentloaded")
        page.wait_for_timeout(3000)

        # ambil metadata
        title, date = extract_metadata(page)
        date = normalize_date(date)

        # ambil content
        paragraphs = extract_content(page)
        paragraphs = [clean_text(p) for p in paragraphs]

        return {
            "url": url,
            "title": title,
            "date": date,
            "content": paragraphs
        }

    except:
        return {
            "url": url,
            "title": None,
            "date": None,
            "content": []
        }


# ==============================
# SCRAPE MULTIPLE URL
# ==============================
def scrape_all(urls):
    results = []

    with sync_playwright() as p:
        browser = p.chromium.launch(headless=True)

        context = browser.new_context(
            user_agent="Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/120 Safari/537.36"
        )

        page = context.new_page()

        for url in urls:
            if not url:
                results.append(None)
                continue

            article = scrape_article(page, url)
            results.append(article)

        browser.close()

    return results

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

import json

def extract_metadata(page):
    title = None
    date = None

    # ======================
    # TITLE
    # ======================
    try:
        title = page.locator("h1").first.text_content()
        if title:
            title = title.strip()
    except:
        pass

    # ======================
    # DATE (multi fallback)
    # ======================

    # 1. meta tag umum
    meta_selectors = [
        "meta[property='article:published_time']",
        "meta[name='publishdate']",
        "meta[name='date']",
        "meta[property='og:published_time']"
    ]

    for sel in meta_selectors:
        try:
            el = page.locator(sel).first
            if el.count() > 0:
                date = el.get_attribute("content")
                if date:
                    return title, date.strip()
        except:
            continue

    # 2. <time> tag
    try:
        el = page.locator("time").first
        if el.count() > 0:
            date = el.get_attribute("datetime") or el.text_content()
            if date:
                return title, date.strip()
    except:
        pass

    # 3. JSON-LD (paling akurat di banyak news site)
    try:
        scripts = page.locator("script[type='application/ld+json']")
        count = scripts.count()

        for i in range(count):
            raw = scripts.nth(i).text_content()
            if not raw:
                continue

            data = json.loads(raw)

            # bisa dict atau list
            if isinstance(data, list):
                data = data[0]

            if isinstance(data, dict) and "datePublished" in data:
                date = data["datePublished"]
                if date:
                    return title, date.strip()

    except:
        pass

    return title, None

def normalize_date(date_str):
    if not date_str:
        return None

    try:
        dt = parser.parse(date_str)
        return dt.strftime("%Y-%m-%d")
    except:
        return None