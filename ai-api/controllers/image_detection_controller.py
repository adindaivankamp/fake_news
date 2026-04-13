from services.img_stage1.search_service import get_search_result
from services.img_stage1.metadata_service import extract_metadata
from services.img_stage1.feature_service import compute_features
from services.img_stage1.model_service import predict


async def detect_image_fake_controller(browser, data):
    try:
        # 1. input
        image_url = data.get("image_url")
        if not image_url:
            return {"error": "image_url is required"}

        # 2. search (SerpAPI / Lens)
        search_results, err = await get_search_result(image_url)

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
            image_url, data_list
        )

        # 5. prediction
        predictions = predict(similarity_score, avg_date_scaled)

        # 6. response (FastAPI style)
        return {
            "similarity_score": similarity_score,
            "avg_date_scaled": avg_date_scaled,
            "predictions": predictions,
            "data": enriched_data
        }

    except Exception as e:
        return {"error": str(e)}