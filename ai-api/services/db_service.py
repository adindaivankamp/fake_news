# services/db_service.py
from config.db_config import get_connection
import pandas as pd
import json
from contextlib import closing

def get_latest_title():
    """Ambil title terbaru dari knowledge_base"""
    query = "SELECT title FROM knowledge_base ORDER BY published_at DESC LIMIT 1"
    with closing(get_connection()) as conn, conn.cursor() as cursor:
        cursor.execute(query)
        result = cursor.fetchone()
        return result[0].lower() if result else ""

def insert_to_mysql(df):

    list_id_chroma = []

    df = df.where(pd.notnull(df), None)

    insert_kb_query = """
        INSERT INTO knowledge_base 
        (title, source_url, category, hoax_text, fact_text, link_counter)
        VALUES (%s, %s, %s, %s, %s, %s)
    """

    with closing(get_connection()) as db, closing(db.cursor()) as cursor:
        for _, row in df.iterrows():

            teks_hoaks = row.get('klaim') or row.get('penjelasan')

            links = row.get('link_counter')

            # handle string JSON
            if isinstance(links, str):
                try:
                    links = json.loads(links)
                except:
                    links = []

            if not isinstance(links, list):
                links = []

            cursor.execute(insert_kb_query, (
                row.get('judul'),
                row.get('link'),
                row.get('kategori_hoaks'),
                teks_hoaks,
                row.get('fakta'),
                json.dumps(links)
            ))

            kb_id = cursor.lastrowid
            list_id_chroma.append(str(kb_id))

        db.commit()

    print(f"✅ Berhasil menyimpan {len(list_id_chroma)} data ke MySQL.")
    return list_id_chroma

def get_row_by_id(id_):
    """Ambil seluruh kolom dari knowledge_base kecuali id"""
    query = "SELECT title, hoax_text, fact_text, category FROM knowledge_base WHERE id=%s"
    with closing(get_connection()) as conn, closing(conn.cursor(dictionary=True)) as cursor:
        cursor.execute(query, (id_,))
        row = cursor.fetchone()
        return row or {}