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

load_dotenv()
import chromadb

def init_chroma():
    client = chromadb.Client()

    print("🚀 Membuat collection baru (cosine)...")

    collection = client.create_collection(
        name="berita_hoax",
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
COLLECTION_NAME = os.getenv("COLLECTION_NAME")
CSV_PATH = os.getenv("CSV_PATH")

# ==========================================
# 1. CLEAR CHROMA
# ==========================================
def clear_chroma(collection):
    total = collection.count()

    if total == 0:
        print("⚠️ ChromaDB sudah kosong.")
        return

    print(f"⚠️ Menghapus {total} data dari ChromaDB...")

    data = collection.get()
    ids = data.get("ids", [])

    collection.delete(ids=ids)

    print("✅ Data berhasil dihapus.")

# ==========================================
# 2. SEED PARQUET → CHROMA
# ==========================================
def seed_parquet_to_chroma(collection, path_file=PARQUET_PATH):
    # Cek file
    if not os.path.exists(path_file):
        print(f"❌ File tidak ditemukan: {path_file}")
        return

    # Hindari double insert
    total_data = collection.count()
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
        collection.add(
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
        client.delete_collection(name=COLLECTION_NAME)
        print("🗑️ Collection berhasil dihapus.")
    except Exception as e:
        print(f"⚠️ Gagal hapus collection: {e}")

# ==========================================
# SEED CSV TO MYSQL
# ==========================================

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

    # 3. Hindari double insert (opsional tapi recommended)
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

            # Format tanggal
            published_at = None
            if pd.notna(row["tanggal"]):
                try:
                    published_at = datetime.strptime(row["tanggal"], "%Y-%m-%d")
                except:
                    pass

            # Insert ke knowledge_base
            insert_kb = """
                INSERT INTO knowledge_base
                (title, hoax_text, fact_text, category, source_url, published_at, created_at, updated_at)
                VALUES (%s, %s, %s, %s, %s, %s, NOW(), NOW())
            """

            cursor.execute(insert_kb, (
                row["judul"],
                row["klaim"],
                row["fakta"],
                row["kategori"],
                row["link"],
                published_at
            ))

            knowledge_id = cursor.lastrowid

            # Parsing link_counter
            links = []
            if pd.notna(row["link_counter"]):
                try:
                    links = ast.literal_eval(row["link_counter"])
                except Exception:
                    pass

            # Insert ke knowledge_links
            if isinstance(links, list):
                insert_link = """
                    INSERT INTO knowledge_links
                    (knowledge_id, url, created_at, updated_at)
                    VALUES (%s, %s, NOW(), NOW())
                """

                for url in links:
                    cursor.execute(insert_link, (knowledge_id, url))

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
        choices=["seed", "clear","delete", "model","nli" ,"playwright","mysql","all"],
        default="all",
        help="Step yang dijalankan"
    )

    args = parser.parse_args()

    # Init Chroma dari config (bukan dari sini lagi)
    collection = get_chroma_collection()

    if args.step == "clear":
        clear_chroma(collection)
    elif args.step == "seed":
        seed_parquet_to_chroma(collection)
    elif args.step == "model":
        download_model()
    elif args.step == "nli":
        download_nli_model()
    elif args.step == "playwright":
        download_playwright()
    elif args.step == "delete":
        delete_chroma_collection()
    elif args.step == "mysql":
        seed_csv_to_mysql(CSV_PATH)
    elif args.step == "all":
        clear_chroma(collection)
        seed_parquet_to_chroma(collection)
        seed_csv_to_mysql(CSV_PATH)
        download_model()

    print("=== SETUP SELESAI ===")