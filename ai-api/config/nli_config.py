from sentence_transformers import CrossEncoder
import torch.nn.functional as F
import os
from dotenv import load_dotenv

load_dotenv()

MODEL_DIR = os.getenv("NLI_MODEL_DIR")

def get_nli_model():
    if not os.path.exists(MODEL_DIR):
        raise Exception("Model NLI belum ada. Jalankan setup untuk download.")

    model = CrossEncoder(
        MODEL_DIR,
        activation_fn=lambda x: F.softmax(x, dim=1))
    return model