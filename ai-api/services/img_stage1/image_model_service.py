import numpy as np
import tensorflow as tf

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