import re
from concurrent.futures import ThreadPoolExecutor, TimeoutError
import requests


# =========================
# GET DATE FROM LINK
# =========================
def get_date(link):
    headers_list = [
        {
            "User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/120 Safari/537.36"
        },
        {
            "User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/136 Safari/537.36"
        }
    ]

    patterns = [
        r'"datePublished"\s*:\s*"([^"]+)"',
        r'<time[^>]+datetime=["\']([^"\']+)["\']',
        r'"uploadDate"\s*:\s*"([^"]+)"',
        r'"dateCreated"\s*:\s*"([^"]+)"',
        r'<meta\s+itemprop=["\']datePublished["\']\s+content=["\']([^"\']+)["\']',
        r'<meta\s+name=["\']PubDate["\']\s+content=["\']([^"\']+)["\']',
    ]

    response = None

    for headers in headers_list:
        try:
            response = requests.get(link, headers=headers, timeout=10)
            if response.status_code == 200:
                break
        except Exception:
            continue

    if response is None or response.status_code != 200:
        return None

    # timeout safe parsing (mirip versi lama kamu)
    try:
        with ThreadPoolExecutor() as executor:
            future = executor.submit(lambda: response.text)
            try:
                html = future.result(timeout=5)
            except TimeoutError:
                return None

        for pattern in patterns:
            match = re.search(pattern, html)
            if match:
                return match.group(1)[:10]

    except Exception:
        pass

    return None


# =========================
# EXTRACT METADATA
# =========================
def extract_metadata(search_results, limit=20):
    """
    input: list[dict]
    output: list[dict]
    """

    data = []

    for item in search_results[:limit]:
        link = item.get("link")
        thumbnail = item.get("thumbnail")
        title = item.get("title")

        if not link:
            continue

        date = get_date(link)

        # filter hanya yang valid date
        if date and re.search(r"\d{4}-\d{2}-\d{2}", str(date)):
            data.append({
                "link": link,
                "thumbnail": thumbnail,
                "title": title,
                "date": date[:10]
            })

    return data