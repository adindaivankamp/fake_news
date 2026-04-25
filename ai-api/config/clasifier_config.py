import os
import joblib
from dotenv import load_dotenv

load_dotenv()

MODEL_PATH = os.getenv("CLASSIFIER_MODEL_PATH")

def get_text_classifier():
    if not os.path.exists(MODEL_PATH):
        raise Exception("Classifier model tidak ditemukan")

    model = joblib.load(MODEL_PATH)
    return model