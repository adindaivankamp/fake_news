from services.text_pipeline_service import process_fake_news_pipeline
from services.text_service import clean_text_light
from services.chroma_service import input_text_request
import traceback

def detect_text_fake_news_controller(text_request, knowledge_base, transformer, nli,client,data,browser, searx_session, headers, text_classifier):
    try:
        if not data or "query" not in data:
            return {"error": "Query tidak ditemukan"}

        query = clean_text_light(data["query"])
        id_request = data["id_request"]

        result = process_fake_news_pipeline(
            raw_text=query,
            collection=knowledge_base,
            transformer=transformer,
            nli=nli,
            client=client,
            browser=browser,
            text_classifier=text_classifier,
            searx_session=searx_session,
            headers=headers,
        )

        if result.get("status") == "success" and result.get("data"):
            embedding = result["data"][0].get("query_embedding")

            if embedding:
                input_text_request(
                    text_request,
                    embedding,
                    id_request
                )

        return result

    except Exception as e:
        print("\n" + "="*50)
        print("ERROR CONTROLLER:", str(e))
        traceback.print_exc()
        print("="*50 + "\n")

        return {
            "status": "error",
            "message": str(e)
        }