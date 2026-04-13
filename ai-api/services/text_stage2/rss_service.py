import feedparser
import urllib.parse

def fetch_rss(kata_kunci, limit):
    query_encoded = urllib.parse.quote(kata_kunci)
    rss_url = f"https://news.google.com/rss/search?q={query_encoded}&hl=id&gl=ID&ceid=ID:id"

    feed = feedparser.parse(rss_url)

    results = []
    for entry in feed.entries[:limit]:
        results.append({
            "judul": entry.title
        })

    return results

