import chromadb
from dotenv import load_dotenv
import os

load_dotenv()

CHROMA_DIR = os.getenv("CHROMA_DIR")

def get_chroma_collection(collection_name: str):
    client = chromadb.PersistentClient(path=CHROMA_DIR)

    collection = client.get_or_create_collection(
        name=collection_name,
        metadata={"hnsw:space": "cosine"}
    )

    return collection