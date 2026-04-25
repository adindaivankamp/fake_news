import requests
from requests.adapters import HTTPAdapter
from urllib3.util.retry import Retry
import os
from dotenv import load_dotenv

# =========================
# CONFIG
# =========================
load_dotenv()
SEARX_URL = os.getenv("SEARX_URL")


# =========================
# SESSION FACTORY
# =========================
def create_searx_session():
    session = requests.Session()

    retries = Retry(
        total=3,
        backoff_factor=1,
        status_forcelist=[500, 502, 503, 504],
        allowed_methods=["GET"]  
    )

    adapter = HTTPAdapter(max_retries=retries)

    session.mount("http://", adapter)
    session.mount("https://", adapter)

    return session


# =========================
# HEADERS
# =========================
def get_headers():
    return {
    "User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/120.0.0.0 Safari/537.36",
    "Accept": "application/json, text/html;q=0.9,*/*;q=0.8",
    "Accept-Language": "id-ID,id;q=0.9,en-US;q=0.8,en;q=0.7",
    "Connection": "keep-alive"
}