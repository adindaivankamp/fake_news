from .rss_service import fetch_rss
from .search_service import cari_link, is_trusted
from .scraper_service import scrape_all, build_chunks, add_vectors, semantic_chunking


async def run_pipeline(pesan, browser, transformer, limit_rss=7, max_articles=3):
    articles = fetch_rss(pesan, limit_rss)

    results = []
    # =========================
    # 1. KUMPULKAN URL
    # =========================
    urls = []
    seen = set()

    for item in articles:
        judul = item.get("judul")

        if not judul:
            continue

        print(f"Processing: {judul}")

        link = cari_link(judul)
        print(link)

        if not link:
            continue

        # 🔥 deduplicate di sini
        if link in seen:
            continue

        seen.add(link)
        urls.append(link)

        if len(urls) >= max_articles:
            break

        if not urls:
            return {"results": []}

    # =========================
    # 2. SCRAPE SEMUA URL
    # =========================
    scraped_articles = await scrape_all(browser, urls)

    # =========================
    # 3. PROCESS PER ARTICLE
    # =========================
    for i, article in enumerate(scraped_articles):

        if not article:
            continue

        content = article.get("content", [])
        if not content:
            continue

        # fallback title dari RSS kalau scraping gagal
        title = article.get("title") or (articles[i].get("judul") if i < len(articles) else None)
        text = "\n\n".join(content)
        semantic_paragraphs = semantic_chunking(text,transformer)

        chunks = build_chunks(semantic_paragraphs, i)

        chunks = add_vectors(chunks, transformer)

        results.append({
            "judul": title,
            "artikel_id": i,
            "tanggal": article.get("date"),
            "link": article.get("url"),
            "chunks": chunks
        })

    return {
        "results": results,
        "urls": urls
    }