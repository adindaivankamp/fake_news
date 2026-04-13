import numpy as np
import tensorflow as tf

DISTANCE_MODEL_PATH = "models/run_20250528/model_2.keras"


def load_img_model():
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

    for layer in model.layers:
        layer.trainable = False

    return model


def preprocess_image(img):
    img = img.resize((224, 224))
    img = np.array(img) / 255.0
    return tf.convert_to_tensor(img, dtype=tf.float32)


def calculate_distance(model, img1, img2):
    img1 = preprocess_image(img1)
    img2 = preprocess_image(img2)

    img1 = np.expand_dims(img1, axis=0)
    img2 = np.expand_dims(img2, axis=0)

    return model.predict([img1, img2], verbose=0)[0][0]