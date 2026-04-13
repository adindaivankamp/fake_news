import joblib

MODEL_PATH = "models/run_20250601/all_models_robust_1.pkl"


def load_classify_model():
    return joblib.load(MODEL_PATH)


def classify(models, data):
    return {
        "Decision Tree": models["Decision Tree"].predict(data)[0],
        "Random Forest": models["Random Forest"].predict(data)[0],
        "KNN": models["KNN"].predict(data)[0],
    }