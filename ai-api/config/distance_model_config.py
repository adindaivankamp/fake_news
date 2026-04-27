import tensorflow as tf
from dotenv import load_dotenv
import os
load_dotenv()
DISTANCE_MODEL_PATH = os.getenv("DISTANCE_MODEL_PATH")

def get_distance_model():
    def euclidean_distance(vectors):
        x, y = vectors
        K = tf.keras.backend
        sum_square = tf.reduce_sum(tf.square(x - y), axis=1, keepdims=True)
        return tf.sqrt(tf.maximum(sum_square, K.epsilon()))

    def loss_fn(y_true, y_pred):
        margin = 1.0
        y_true = tf.cast(y_true, y_pred.dtype)
        square_pred = tf.square(y_pred)
        margin_square = tf.square(tf.maximum(margin - y_pred, 0))
        return tf.reduce_mean(y_true * square_pred + (1 - y_true) * margin_square)

    def l2_normalize_fn(x):
        return tf.math.l2_normalize(x, axis=1)

    model = tf.keras.models.load_model(
        DISTANCE_MODEL_PATH,
        custom_objects={
            "euclidean_distance": euclidean_distance,
            "loss_fn": loss_fn,
            "l2_normalize_fn": l2_normalize_fn
        }
    )

    return model