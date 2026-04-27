from config.chroma_config import get_chroma_collection
import os

collection = get_chroma_collection("knowledge_base")

BASE_DIR = os.path.dirname(os.path.abspath(__file__))
IMG_DIR = os.path.abspath(os.path.join(BASE_DIR, "..", "..", "prediction_images"))

print("IMG_DIR:", IMG_DIR)
print("Base directory:", BASE_DIR)

# ======================
# BASIC INFO
# ======================
print("\n===== CHROMA INFO =====")
print("Total data:", collection.count())

# ======================
# SAMPLE DATA (IMPORTANT)
# ======================
print("\n===== SAMPLE DATA =====")
sample = collection.peek(2)  # lebih aman daripada get full
print(sample)

# ======================
# COLLECTION METADATA
# ======================
print("\n===== METADATA =====")
try:
    print(collection.metadata)
except Exception as e:
    print("No metadata or not available:", e)