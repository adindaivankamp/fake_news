from serpapi import GoogleSearch
from config.config import Config

async def get_search_result(img_url, limit=40):
    params = {
        "engine": "google_lens",
        "url": img_url,
        "api_key": Config.SERPAPI_KEY
    }

    try:
        search = GoogleSearch(params)
        results = search.get_dict()

        visual_matches = results.get("visual_matches", [])

        data = []

        for item in visual_matches[:limit]:
            data.append({
                "title": item.get("title"),
                "link": item.get("link") or item.get("source"),
                "thumbnail": item.get("thumbnail"),
            })

        if not data:
            return [], "No visual matches found"

        return data, None

    except Exception as e:
        return [], str(e)