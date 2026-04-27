from fastapi import APIRouter, Request
from controllers.image_detection_controller import detect_image_fake_controller
from controllers.text_detection_controller import detect_text_fake_news_controller
from controllers.kb_controller import update_knowledge_base_controller


def create_routes():
    router = APIRouter()

    @router.post("/text-detection")
    def text_detection(request: Request, data: dict):
        return detect_text_fake_news_controller(
            request.app.state.text_request,
            request.app.state.knowledge_base,
            request.app.state.transformer,
            request.app.state.nli,
            request.app.state.client,
            data,
            request.app.state.browser,
            request.app.state.searx_session,
            request.app.state.headers,
            request.app.state.text_classifier,

        )

    @router.post("/scrape")
    def scrape(request: Request):
        return update_knowledge_base_controller(
            request.app.state.transformer,
            request.app.state.knowledge_base,
        )

    @router.post("/image-detection")
    def image_detection(request: Request, data: dict):
        return detect_image_fake_controller(
            request.app.state.image_classifier,
            request.app.state.distance_model,
            data
        )

    return router