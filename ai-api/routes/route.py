# routes/route.py
from fastapi import APIRouter, Request
from controllers.image_detection_controller import detect_image_fake_controller
from controllers.text_detection_controller import detect_text_fake_news_controller
from controllers.kb_controller import update_knowledge_base_controller


def create_routes():
    router = APIRouter()

    # ======================
    # TEXT DETECTION
    # ======================
    @router.post("/text-detection")
    async def text_detection(request: Request, data: dict):
        collection = request.app.state.collection
        transformer = request.app.state.transformer
        nli = request.app.state.nli
        client = request.app.state.client

        return detect_text_fake_news_controller(
            collection, transformer, nli, client, data
        )

    # ======================
    # SCRAPER
    # ======================
    @router.post("/scrape")
    async def scrape(request: Request):
        transformer = request.app.state.transformer
        collection = request.app.state.collection

        return update_knowledge_base_controller(
            transformer, collection
        )

    # ======================
    # IMAGE DETECTION
    # ======================
    @router.post("/image-detection")
    async def image_detection(request: Request, data: dict):
        browser = request.app.state.browser

        # async karena pakai playwright
        return await detect_image_fake_controller(browser, data)

    return router