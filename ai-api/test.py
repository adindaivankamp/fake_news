from config.chroma_config import get_chroma_collection

collection = get_chroma_collection()
import os

BASE_DIR = os.path.dirname(os.path.abspath(__file__))

IMG_DIR = os.path.abspath(os.path.join(BASE_DIR, "..", "..", "prediction_images"))
print("IMG_DIR:", IMG_DIR)
print("Base directory:", BASE_DIR)
print("Total data di Chroma:", collection.count())
print(collection.metadata)