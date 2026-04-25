import traceback
from .search_service import search_news
from .feature_service import extract_features
import numpy as np

FEATURE_ORDER = [
    "time_consistency_score",
    "message_similarity_score",
    "mean_entailment",
    "mean_contradiction",
    "std_entailment",
]

def run_stage3_online_search(query, transformer, nli, searx_session, headers, text_classifier):
    try:

        results = search_news(query, searx_session, headers)  

        if not results:
            return {
                "status": "fail",
                "title": query,
            }

        features_dict = extract_features(query, results[:10], nli, transformer)
        vector = [features_dict[f] for f in FEATURE_ORDER]

        proba = text_classifier.predict_proba([vector])[0]

        prediction = int(np.argmax(proba))
        confidence = float(np.max(proba))
        urls = [r.get("url") for r in results if r.get("url")]
        return {
            "status": "success",
            "prediction": prediction,
            "confidence": confidence,
            "url" : urls
        }

    except Exception as e:
        print("\n" + "="*50)        
        print(f"Error   : {str(e)}")
        print("TRACEBACK:")
        traceback.print_exc()
        print("="*50 + "\n")

        return {
            "status": "error",
            "error_message": str(e),
        
        }