import os
import argparse
import pandas as pd
from sentence_transformers import SentenceTransformer, CrossEncoder
from config.chroma_config import get_chroma_collection
from dotenv import load_dotenv
import subprocess
import pandas as pd
import ast
from datetime import datetime
from config.db_config import get_connection
import json

load_dotenv()
import chromadb

def init_chroma():
    client = chromadb.Client()

    print("🚀 Membuat collection baru (cosine)...")

    knowledge_base = client.create_collection(
        name="knowledge_base",
        metadata={"hnsw:space": "cosine"}
    )
    text_request = client.create_collection(
        name="text_request",
        metadata={"hnsw:space": "cosine"}
    )

    print("✅ Collection berhasil dibuat.")
# ==========================================
# CONFIG
# ==========================================
PARQUET_PATH = os.getenv("PARQUET_PATH")
MODEL_NAME = os.getenv("MODEL_NAME")
MODEL_DIR = os.getenv("MODEL_DIR")
NLI_MODEL_NAME = os.getenv("NLI_MODEL_NAME")
NLI_MODEL_DIR = os.getenv("NLI_MODEL_DIR")
CHROMA_DIR = os.getenv("CHROMA_DIR")
CSV_PATH = os.getenv("CSV_PATH")

# ==========================================
# 1. CLEAR CHROMA
# ==========================================
def clear_knowledge_base(knowledge_base):
    total = knowledge_base.count()

    if total == 0:
        print("⚠️ ChromaDB sudah kosong.")
        return

    print(f"⚠️ Menghapus {total} knowledge_base dari ChromaDB...")

    data = knowledge_base.get()
    ids = data.get("ids", [])

    knowledge_base.delete(ids=ids)

    print("✅ Data berhasil dihapus.")

def clear_text_request(text_request):
    total = text_request.count()

    if total == 0:
        print("⚠️ ChromaDB sudah kosong.")
        return

    print(f"⚠️ Menghapus {total} text_request dari ChromaDB...")

    data = text_request.get()
    ids = data.get("ids", [])

    text_request.delete(ids=ids)

    print("✅ Data berhasil dihapus.")

# ==========================================
# 2. SEED PARQUET → CHROMA
# ==========================================
def seed_parquet_to_chroma(knowledge_base, path_file=PARQUET_PATH):
    # Cek file
    if not os.path.exists(path_file):
        print(f"❌ File tidak ditemukan: {path_file}")
        return

    # Hindari double insert
    total_data = knowledge_base.count()
    if total_data > 0:
        print(f"⚠️ Chroma sudah berisi {total_data} data. Skip insert.")
        return

    print("Loading data dari Parquet...")

    try:
        df_seed = pd.read_parquet(path_file)
    except Exception as e:
        print(f"❌ Gagal membaca Parquet: {e}")
        print("👉 Install dulu: pip install pyarrow")
        return

    # Validasi kolom
    required_cols = {"id", "vektor"}
    if not required_cols.issubset(df_seed.columns):
        print("❌ Kolom wajib tidak ditemukan: 'id' dan 'vektor'")
        return
    df_seed["id"] = df_seed["id"].astype(int) + 1
    ids_list = df_seed["id"].astype(str).tolist()
    vektor_list = df_seed["vektor"].tolist()

    print(f"Menyisipkan {len(ids_list)} vektor ke ChromaDB...")

    try:
        knowledge_base.add(
            ids=ids_list,
            embeddings=vektor_list
        )
    except Exception as e:
        print(f"❌ Gagal insert ke ChromaDB: {e}")
        return

    print("✅ Seeder berhasil.")
    
# ==========================================
# 3. Model
# ==========================================
def download_model():
    print("Downloading model ke folder local...")

    os.makedirs(MODEL_DIR, exist_ok=True)

    model = SentenceTransformer(MODEL_NAME)
    model.save(MODEL_DIR)

    print(f"✅ Model disimpan di: {MODEL_DIR}")
    

# ==========================================
# 4. nli
# ==========================================

def download_nli_model():
    print("Downloading NLI model...")

    os.makedirs(NLI_MODEL_DIR, exist_ok=True)

    model = CrossEncoder(NLI_MODEL_NAME)
    model.save(NLI_MODEL_DIR)

    print("✅ NLI model ready.")
    
# ==========================================
# playwright
# ==========================================
def download_playwright():
    os.environ["PLAYWRIGHT_BROWSERS_PATH"] = "./.playwright-browsers"
    print("🚀 Menginstall Chromium untuk Playwright...")
    subprocess.run(["playwright", "install", "chromium"], check=True)
    print("✅ Chromium siap digunakan.")


# ==========================================
# DELETE CHROMA
# ==========================================
def delete_chroma_collection():
    client = chromadb.PersistentClient(path=CHROMA_DIR)

    try:
        client.delete_collection(name="knowledge_base")
        client.delete_collection(name="text_request")
        print("🗑️ Collection berhasil dihapus.")
    except Exception as e:
        print(f"⚠️ Gagal hapus collection: {e}")

# ==========================================
# SEED CSV TO MYSQL
# ==========================================
from contextlib import closing

def clean_mysql_knowledge_base():
    """
    Bersihkan semua data knowledge_base (JSON schema version)
    dipakai sebelum seed ulang CSV
    """

    try:
        with closing(get_connection()) as conn, closing(conn.cursor()) as cursor:

            print("🧹 Cleaning knowledge_base...")

            # hapus semua data
            cursor.execute("DELETE FROM knowledge_base")

            # reset auto increment (biar ID mulai dari 1 lagi)
            cursor.execute("ALTER TABLE knowledge_base AUTO_INCREMENT = 1")

            conn.commit()

        print("✅ Knowledge base berhasil dibersihkan")

    except Exception as e:
        print(f"❌ Gagal clean knowledge base: {e}")
        
def seed_csv_to_mysql(path_csv):

    # 1. Cek file
    if not os.path.exists(path_csv):
        print(f"❌ File tidak ditemukan: {path_csv}")
        return

    print("📥 Loading data dari CSV...")

    try:
        df = pd.read_csv(path_csv)
    except Exception as e:
        print(f"❌ Gagal membaca CSV: {e}")
        return

    # 2. Validasi kolom
    required_cols = {"judul", "klaim", "fakta", "kategori", "link", "link_counter", "tanggal"}
    if not required_cols.issubset(df.columns):
        print("❌ Kolom wajib tidak lengkap")
        return

    conn = get_connection()
    cursor = conn.cursor()

    # 3. Cek data existing
    cursor.execute("SELECT COUNT(*) FROM knowledge_base")
    total = cursor.fetchone()[0]

    if total > 0:
        print(f"⚠️ Data sudah ada ({total} rows). Skip insert.")
        cursor.close()
        conn.close()
        return

    print(f"🚀 Menyisipkan {len(df)} data ke MySQL...")

    try:
        for _, row in df.iterrows():

            # format tanggal
            published_at = None
            if pd.notna(row["tanggal"]):
                try:
                    published_at = datetime.strptime(row["tanggal"], "%Y-%m-%d")
                except:
                    pass

            # parsing JSON links (list)
            links = []
            if pd.notna(row["link_counter"]):
                try:
                    links = ast.literal_eval(row["link_counter"])
                except:
                    links = []

            # convert ke JSON string
            links_json = json.dumps(links)

            # insert knowledge_base (tanpa tabel relasi)
            insert_kb = """
                INSERT INTO knowledge_base
                (title, hoax_text, fact_text, category, source_url, link_counter, published_at, created_at, updated_at)
                VALUES (%s, %s, %s, %s, %s, %s, %s, NOW(), NOW())
            """

            cursor.execute(insert_kb, (
                row["judul"],
                row["klaim"],
                row["fakta"],
                row["kategori"],
                row["link"],
                links_json,
                published_at
            ))

        conn.commit()
        print("✅ Seeder MySQL berhasil!")

    except Exception as e:
        conn.rollback()
        print(f"❌ Gagal insert: {e}")

    finally:
        cursor.close()
        conn.close()
# ==========================================
# MAIN
# ==========================================
if __name__ == "__main__":
    parser = argparse.ArgumentParser(description="Setup / Seeder ChromaDB")
    parser.add_argument(
        "--step",
        type=str,
        choices=["seed", "clear","delete", "model","nli" ,"playwright","mysql_seed","mysql_clean","all"],

        default="all",
        help="Step yang dijalankan"
    )

    args = parser.parse_args()

    # Init Chroma dari config (bukan dari sini lagi)
    text_request = get_chroma_collection("text_request")
    knowledge_base = get_chroma_collection("knowledge_base")

    if args.step == "clear":
        clear_knowledge_base(knowledge_base)
        clear_text_request(text_request)
    elif args.step == "seed":
        seed_parquet_to_chroma(knowledge_base)
    elif args.step == "model":
        download_model()
    elif args.step == "nli":
        download_nli_model()
    elif args.step == "playwright":
        download_playwright()
    elif args.step == "delete":
        delete_chroma_collection()
    elif args.step == "mysql_seed":
        seed_csv_to_mysql(CSV_PATH)
    elif args.step == "mysql_clean":
        clean_mysql_knowledge_base()
    elif args.step == "all":
        clear_knowledge_base(knowledge_base)
        clear_text_request(text_request)
        seed_parquet_to_chroma(knowledge_base)
        clean_mysql_knowledge_base()
        seed_csv_to_mysql(CSV_PATH)
        
        # 2. Models
        download_model()
        download_nli_model()
        download_playwright()

    print("\n=== SETUP SELESAI ===")