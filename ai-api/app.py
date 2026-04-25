import asyncio
asyncio.set_event_loop_policy(asyncio.WindowsProactorEventLoopPolicy())

from fastapi import FastAPI
from contextlib import asynccontextmanager

from routes.route import create_routes
from config.chroma_config import get_chroma_collection
from config.transformer_config import get_transformer_model
from config.nli_config import get_nli_model
from config.genai_client import get_client
from config.clasifier_config import get_text_classifier
from config.config import Config
from config.session_config import create_searx_session, get_headers

from playwright.async_api import async_playwright

# ==============================
# LIFECYCLE (INIT & CLEANUP)
# ==============================
@asynccontextmanager
async def lifespan(app: FastAPI):
    print(f"Running in {Config.ENV} mode, DEBUG={Config.DEBUG}")

    # INIT dependencies
    app.state.collection = get_chroma_collection()
    app.state.transformer = get_transformer_model()
    app.state.nli = get_nli_model()
    app.state.text_classifier = get_text_classifier()
    app.state.image_classifier = None
    app.state.client = get_client()

    # INIT Playwright (async)
    app.state.playwright = await async_playwright().start()
    app.state.browser = await app.state.playwright.chromium.launch(headless=True)

    # INIT session requests
    app.state.searx_session = create_searx_session()
    app.state.headers = get_headers()
    print("✅ Playwright browser started")

    yield

    # CLEANUP
    await app.state.browser.close()
    await app.state.playwright.stop()
    print("🛑 Playwright stopped")


# ==============================
# INIT APP
# ==============================
app = FastAPI(lifespan=lifespan)

# ==============================
# REGISTER ROUTES
# ==============================
bp = create_routes()
app.include_router(bp)