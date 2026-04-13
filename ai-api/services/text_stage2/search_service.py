import urllib.parse
from config.trusted_news_websites import trusted_news_websites
from ddgs import DDGS
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
    with DDGS() as ddgs:
        results = list(ddgs.text(judul, max_results=5))  

    for r in results:
        href = r.get("href")
        if not href:
            continue

        if is_trusted(href):
            print(f"Found trusted link: {href}")
            return href

    return None

def clean_ddg(url):
    if url and "uddg=" in url:
        url = url.split("uddg=")[1].split("&")[0]
        url = urllib.parse.unquote(url)
    return url

