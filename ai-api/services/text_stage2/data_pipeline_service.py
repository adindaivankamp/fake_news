from .rss_service import fetch_rss
from .search_service import cari_link, is_trusted
from .scraper_service import scrape_all, build_chunks, add_vectors


def run_pipeline(pesan, model, limit_rss=10, max_articles=5):
    articles = fetch_rss(pesan, limit_rss)

    results = []
    urls = []

    # =========================
    # 1. KUMPULKAN URL
    # =========================
    for item in articles:
        judul = item.get("judul")

        if not judul:
            continue

        print(f"Processing: {judul}")

        link = cari_link(judul)
        print(link)

        if not link:
            continue

        # optional: filter trusted
        if not is_trusted(link):
            continue

        urls.append(link)

        if len(urls) >= max_articles:
            break

    if not urls:
        return {"results": []}

    # =========================
    # 2. SCRAPE SEMUA URL
    # =========================
    scraped_articles = scrape_all(urls)

    print(urls)
    print(scraped_articles)

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

        chunks = build_chunks(content,i)
        chunks = add_vectors(chunks, model)

        results.append({
            "judul": title,
            "artikel_id": i,
            "tanggal": article.get("date"),
            "link": article.get("url"),
            "chunks": chunks
        })

    return {
        "results": results
    }