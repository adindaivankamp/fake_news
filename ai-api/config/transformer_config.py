from sentence_transformers import SentenceTransformer
import os
from dotenv import load_dotenv

load_dotenv()

MODEL_DIR = os.getenv("MODEL_DIR")

def get_transformer_model():
    if not os.path.exists(MODEL_DIR):
        raise Exception(
            "Model belum ada di local. Jalankan setup.py --step model "
        )

    model = SentenceTransformer(MODEL_DIR)
    return model