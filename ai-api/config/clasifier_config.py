import os
import joblib
from dotenv import load_dotenv

load_dotenv()

TEXT_MODEL_PATH = os.getenv("TEXT_CLASSIFIER_MODEL_PATH")
IMG_MODEL_PATH = os.getenv("IMG_CLASSIFIER_MODEL_PATH")

def get_text_classifier():
    if not os.path.exists(TEXT_MODEL_PATH):
        raise Exception("Classifier model tidak ditemukan")

    model = joblib.load(TEXT_MODEL_PATH)
    return model

def get_img_classifier():
    if not os.path.exists(IMG_MODEL_PATH):
        raise Exception("Classifier model tidak ditemukan")

    models = joblib.load(IMG_MODEL_PATH)
    model = models["Random Forest"]
    return model

import joblib



