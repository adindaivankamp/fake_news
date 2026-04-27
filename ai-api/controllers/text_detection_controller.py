from services.text_pipeline_service import process_fake_news_pipeline
from services.text_service import clean_text_light

def detect_text_fake_news_controller(collection, transformer, nli,client,data,browser, searx_session, headers, text_classifier):

    print("Received query:", data)
    print("type(data):", type(data))
    if not data or "query" not in data:
        return {"error": "Query tidak ditemukan"}

    query = data["query"]
    query = clean_text_light(query)
    
    result = process_fake_news_pipeline(
        raw_text=query,
        collection=collection,
        transformer=transformer,
        nli=nli,
        client=client,
        browser=browser,
        text_classifier=text_classifier,
        searx_session=searx_session,
        headers=headers,
    )
    return result