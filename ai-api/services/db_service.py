# services/db_service.py
from config.db_config import get_connection
import pandas as pd
import json
from contextlib import closing

def get_latest_title():
    """Ambil judul terbaru dari knowledge_base"""
    query = "SELECT judul FROM knowledge_base ORDER BY tanggal DESC LIMIT 1"
    with closing(get_connection()) as conn, conn.cursor() as cursor:
        cursor.execute(query)
        result = cursor.fetchone()
        return result[0].lower() if result else ""


def insert_to_mysql(df):
    """
    Simpan dataframe ke MySQL.
    df harus punya kolom: judul, link, kategori_hoaks, klaim/penjelasan, fakta, link_counter
    Return list id yang berhasil disimpan (str)
    """
    list_id_chroma = []

    df = df.where(pd.notnull(df), None)

    insert_query = """
        INSERT INTO knowledge_base (judul, link, kategori, hoax_text, fakta, link_counter)
        VALUES (%s, %s, %s, %s, %s, %s)
    """

    with closing(get_connection()) as db, closing(db.cursor()) as cursor:
        for _, row in df.iterrows():
            teks_hoaks = row.get('klaim') or row.get('penjelasan')
            link_mentah = row.get('link_counter')
            link_json = json.dumps(link_mentah) if link_mentah else None

            cursor.execute(insert_query, (
                row.get('judul'),
                row.get('link'),
                row.get('kategori_hoaks'),
                teks_hoaks,
                row.get('fakta'),
                link_json
            ))
            list_id_chroma.append(str(cursor.lastrowid))

        db.commit()

    print(f"✅ Berhasil menyimpan {len(list_id_chroma)} data ke MySQL.")
    return list_id_chroma


def get_row_by_id(id_):
    """Ambil seluruh kolom dari knowledge_base kecuali id"""
    query = "SELECT judul, hoax_text, fakta, kategori, link, link_counter FROM knowledge_base WHERE id=%s"
    with closing(get_connection()) as conn, closing(conn.cursor(dictionary=True)) as cursor:
        cursor.execute(query, (id_,))
        row = cursor.fetchone()
        return row or {}