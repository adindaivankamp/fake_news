import joblib
import numpy as np

# load model
model = joblib.load("./models/rf_model.pkl")


# susun fitur (HARUS urut sesuai training)
X = [[
        0.6967,	0.0513,	0.043902,	0.856553,	0.059734
]]

# prediksi
pred = model.predict(X)
proba = model.predict_proba(X)

print("Prediction:", pred[0])
print("Confidence:", max(proba[0]))
print(proba)
print(model.classes_)
