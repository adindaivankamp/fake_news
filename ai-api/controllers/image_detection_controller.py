from services.img_stage1.search_service import get_search_result
from services.img_stage1.metadata_service import extract_metadata
from services.img_stage1.feature_service import compute_features
import numpy as np

def detect_image_fake_controller(image_classifier, distance_model, data):
    try:
        # 1. input
        image_url = data.get("image_url")
        if not image_url:
            return {"error": "image_url is required"}

        # 2. search (SerpAPI / Lens)
        search_results, err = get_search_result(image_url)

        if err:
            return {"error": err}

        if not search_results:
            return {"error": "No search results"}

        # 3. metadata processing
        data_list = extract_metadata(search_results)

        if not data_list:
            return {"error": "No valid metadata"}

        # 4. feature engineering
        similarity_score, avg_date_scaled, enriched_data = compute_features(
            image_url, data_list, distance_model
        )

        # 5. prediction
        proba = image_classifier.predict_proba([[similarity_score, avg_date_scaled]])[0]
        prediction = int(np.argmax(proba))
        confidence = float(np.max(proba))
        
        # 6. response (FastAPI style)
        
        return {
            "similarity_score": similarity_score,
            "avg_date_scaled": avg_date_scaled,
            "prediction": prediction,
            "confidence": confidence,
            "data": enriched_data
        }

    except Exception as e:
        return {"error": str(e)}