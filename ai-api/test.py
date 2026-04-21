# from config.chroma_config import get_chroma_collection

# collection = get_chroma_collection()
# import os

# BASE_DIR = os.path.dirname(os.path.abspath(__file__))

# IMG_DIR = os.path.abspath(os.path.join(BASE_DIR, "..", "..", "prediction_images"))
# print("IMG_DIR:", IMG_DIR)
# print("Base directory:", BASE_DIR)
# print("Total data di Chroma:", collection.count())
# print(collection.metadata)

import requests

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
            headers={
                "User-Agent": "Mozilla/5.0"
            }
        )

        data = res.json()
        results = data.get("results", [])

        if not results:
            return None

        # ambil link pertama saja
        first = results[0]
        return first.get("url")

    except Exception as e:
        print(f"Error cari_link: {e}")
        return None


link = cari_link(
    "1 Pria Penikam Nus Kei hingga Tewas Ternyata Atlet MMA"
)

print("Link yang ditemukan:", link)