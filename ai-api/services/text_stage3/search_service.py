import httpx
import pandas as pd
from urllib.parse import urlparse
from config.session_config import SEARX_URL
import re
from datetime import datetime, timedelta

def search_news(query, searx_session, headers):
    try:
        res = searx_session.get(
            SEARX_URL,
            params={
                "q": query,
                "format": "json",
                "language": "id",
                "categories": "news"
            },
            headers=headers,
            timeout=30.0
        )

        res.raise_for_status()
        data = res.json() 

        results = data.get("results", [])[:10]

        cleaned = [
            {
                "title": r.get("title", ""),
                "url": r.get("url", ""),
                "date": extract_best_date(r),
                "source": urlparse(r.get("url", "")).netloc
            }
            for r in results
        ]
        return cleaned

    except httpx.RequestError as e:
        print("Error:", e)
        return []
    
def extract_best_date(res):
    # 1. publishedDate (BEST)
    dt_str = res.get("publishedDate")
    if dt_str:
        dt = pd.to_datetime(dt_str, errors="coerce", utc=True)
        if pd.notnull(dt):
            return dt.timestamp()

    # 2. metadata / age
    meta = res.get("metadata") or res.get("age")
    if meta:
        meta_clean = meta.split("|")[0].strip()

        # 🔥 NEW: parse tanggal absolut
        try:
            dt = pd.to_datetime(meta_clean, dayfirst=True, errors="coerce")
            if pd.notnull(dt):
                return dt.timestamp()
        except:
            pass

        # fallback ke relative time
        ts = parse_relative_time(meta_clean)
        if ts:
            return ts

    return None

def parse_relative_time(text):
    if not text:
        return None

    text = text.lower()
    now = datetime.now()

    # 🔥 handle "kemarin"
    if "kemarin" in text or "yesterday" in text:
        return (now - timedelta(days=1)).timestamp()

    # 🔥 handle "hari ini"
    if "hari ini" in text or "today" in text:
        return now.timestamp()

    # 🔥 handle "baru saja"
    if "baru saja" in text or "just now" in text:
        return now.timestamp()

    # jam
    match = re.search(r'(\d+)\s*(jam|hour|hours)', text)
    if match:
        return (now - timedelta(hours=int(match.group(1)))).timestamp()

    # menit
    match = re.search(r'(\d+)\s*(menit|minute|minutes)', text)
    if match:
        return (now - timedelta(minutes=int(match.group(1)))).timestamp()

    # hari
    match = re.search(r'(\d+)\s*(hari|day|days)', text)
    if match:
        return (now - timedelta(days=int(match.group(1)))).timestamp()

    return None