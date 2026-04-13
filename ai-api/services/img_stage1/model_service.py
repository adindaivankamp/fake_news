import pandas as pd
from .classification_service import classify, load_classify_model

def predict(similarity_score, avg_date_scaled):
    try:
        classify_models = load_classify_model()
        
        x_data = pd.DataFrame(
            [[similarity_score, avg_date_scaled]], 
            columns=["similarity_score", "avg_date_robust"]
        )
        
        predictions = classify(classify_models, x_data)
        
        return predictions

    except Exception as e:
        return {"error": f"Prediction failed: {str(e)}"}