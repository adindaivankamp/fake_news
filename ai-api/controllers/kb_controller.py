import pandas as pd
from services.scraper_service import *
from flask import jsonify
import pandas as pd
from services.db_service import insert_to_mysql, get_latest_title
from services.chroma_service import insert_to_chroma

def update_knowledge_base_controller(model, collection, batch_size=32):
    print("🚀 Memulai proses scraping data hoaks...")
    latest_title = get_latest_title()
    print(f"📌 Judul terbaru di database: '{latest_title}'")
    df = scrape_new_hoaxes(latest_title)
    df = scrape_all(df)
    df = retry_scrape_nan(df)
    df = clean_dataframe(df)
    
    if df is None or df.empty:
        print("✅ Tidak ada data hoaks baru untuk diproses hari ini.")
        return None

    list_id = insert_to_mysql(df)
    insert_to_chroma(df, list_id, model, collection, batch_size)
    return jsonify({
        "status": "success",
        "message": "Scraping dan sinkronisasi selesai"
    }), 200
    
