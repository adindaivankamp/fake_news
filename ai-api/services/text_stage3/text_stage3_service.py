import traceback
import numpy as np

from .search_service import search_news
from .feature_service import extract_features

FEATURE_ORDER = [
    "message_similarity_score",
    "time_consistency_score",
    "mean_entailment",
    "mean_contradiction",
    "std_entailment",
]


def _safe_extract_vector(features_dict):
    """
    memastikan semua feature ada, kalau tidak default 0.0
    """
    return [float(features_dict.get(f, 0.0)) for f in FEATURE_ORDER]


def run_stage3_online_search(
    query,
    transformer,
    nli,
    searx_session,
    headers,
    text_classifier
):

    try:
        results = search_news(query, searx_session, headers)

        if not results:
            return {
                "status": "fail",
                "reason": "no_search_results",
                "query": query,
                "data": []
            }

        # limit input biar stabil
        top_results = results[:10]

        features_dict = extract_features(query, top_results, nli, transformer)

        if not features_dict:
            return {
                "status": "fail",
                "reason": "feature_extraction_failed",
                "query": query,
                "data": []
            }

        vector = _safe_extract_vector(features_dict)

        # safety check input model
        if len(vector) != len(FEATURE_ORDER):
            return {
                "status": "fail",
                "reason": "invalid_feature_vector_length",
                "expected": len(FEATURE_ORDER),
                "got": len(vector),
                "query": query,
                "data": []
            }

        proba = text_classifier.predict_proba([vector])[0]

        prediction = int(np.argmax(proba))
        confidence = float(np.max(proba))

        urls = list({
            r.get("url")
            for r in results
            if r.get("url")
        })

        return {
            "status": "success",
            "query": query,
            "prediction": prediction,
            "confidence": confidence,
            "urls": urls,
            "feature_vector": dict(zip(FEATURE_ORDER, vector))
        }

    except Exception as e:
        error_trace = traceback.format_exc()

        print("\n" + "=" * 60)
        print("STAGE 3 ERROR")
        print(f"Query: {query}")
        print(f"Error: {str(e)}")
        print(error_trace)
        print("=" * 60 + "\n")

        return {
            "status": "error",
            "query": query,
            "error_message": str(e),
            "traceback": error_trace,
            "data": []
        }