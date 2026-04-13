import os
from dotenv import load_dotenv
from google import genai

load_dotenv()

_client = None  # cache singleton


def get_client():
    global _client

    if _client is None:
        api_key = os.getenv("GOOGLE_API_KEY")

        if not api_key:
            raise ValueError("GOOGLE_API_KEY tidak ditemukan di environment")

        _client = genai.Client(api_key=api_key)

    return _client