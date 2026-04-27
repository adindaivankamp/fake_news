from datetime import datetime
from io import BytesIO

import requests
from PIL import Image
import numpy as np

from .image_model_service import  calculate_distance


DISTANCE_THRESHOLD = 0.4414


# =========================
# MAIN PIPELINE
# =========================
def compute_features(image_url, data_list, distance_model):
    if not data_list:
        return 0.0, 0.0, []

    input_img = load_from_url(image_url)

    enriched_data = _compute_image_features(
        data_list, distance_model, input_img
    )

    similarity_score = get_similarity_score(enriched_data)

    enriched_data = date_diff_and_scaling(enriched_data)

    avg_date_scaled = sum(
        item.get("date_scaled", 0.0) for item in enriched_data
    ) / len(enriched_data)

    return float(similarity_score), float(avg_date_scaled), enriched_data


# =========================
# IMAGE FEATURE COMPUTATION
# =========================
def _compute_image_features(data_list, distance_model, input_img):
    enriched = []

    for item in data_list:
        new_item = dict(item)

        try:
            img_target = load_from_url(new_item["thumbnail"])
            distance = calculate_distance(distance_model, input_img, img_target)

            new_item["img_distance"] = float(distance)
            new_item["pred_label"] = (
                "similar" if distance <= DISTANCE_THRESHOLD else "not similar"
            )

        except Exception:
            new_item["img_distance"] = 1.0
            new_item["pred_label"] = "not similar"

        enriched.append(new_item)

    return enriched


# =========================
# IMAGE LOADER
# =========================
def load_from_url(url):
    try:
        response = requests.get(url, timeout=5)
        response.raise_for_status()

        image = Image.open(BytesIO(response.content))

        if image.mode != "RGB":
            image = image.convert("RGB")

        return image

    except Exception:
        return None


# =========================
# SIMILARITY SCORE
# =========================
def get_similarity_score(data_list):
    if not data_list:
        return 0.0

    total = len(data_list)
    similar = sum(1 for x in data_list if x.get("pred_label") == "similar")

    return similar / total


# =========================
# DATE PROCESSING (PURE PYTHON)
# =========================
def date_diff_and_scaling(data_list):
    dates = []

    today = datetime.today().date()

    for item in data_list:
        try:
            date_str = item.get("date")
            if not date_str:
                diff = 0
            else:
                dt = datetime.strptime(date_str[:10], "%Y-%m-%d").date()
                diff = (today - dt).days

        except Exception:
            diff = 0

        item["date_diff"] = diff
        dates.append(diff)

    # robust scaling manual (median + IQR)
    arr = np.array(dates, dtype=float)

    if len(arr) == 0:
        for item in data_list:
            item["date_scaled"] = 0.0
        return data_list

    q1 = np.percentile(arr, 25)
    q3 = np.percentile(arr, 75)
    iqr = q3 - q1 if q3 != q1 else 1.0

    for item in data_list:
        item["date_scaled"] = (item["date_diff"] - q1) / iqr

    return data_list