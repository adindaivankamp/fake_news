import chromadb
import os
from dotenv import load_dotenv

load_dotenv()

CHROMA_DIR = os.getenv("CHROMA_DIR")
COLLECTION_NAME = os.getenv("COLLECTION_NAME")

def get_chroma_collection():
    client = chromadb.PersistentClient(path=CHROMA_DIR)

    collection = client.get_or_create_collection(
        name=COLLECTION_NAME,
        metadata={"hnsw:space": "cosine"}
    )

    return collection