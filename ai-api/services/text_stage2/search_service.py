import urllib.parse
from config.trusted_news_websites import trusted_news_websites
import requests
headers = {"User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/120 Safari/537.36"}


def extract_domain(url):
    try:
        from urllib.parse import urlparse
        domain = urlparse(url).netloc.lower()
        return domain
    except:
        return ""


def is_trusted(url):
    domain = extract_domain(url)
    return any(trusted in domain for trusted in trusted_news_websites)

def cari_link(judul):
    try:
        res = requests.get(
            "http://localhost:8080/search",
            params={
                "q": judul,
                "language": "id",
                "safesearch": 1,
                "categories": "news",
                "format": "json"
            },
            timeout=10,
            headers=headers
        )

        data = res.json()
        results = data.get("results", [])

        for r in results:
            href = r.get("url")
            if not href:
                continue

            if is_trusted(href):
                return href

        return None

    except Exception as e:
        print(f"Error cari_link: {e}")
        return None



