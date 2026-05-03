from services.text_stage1.text_stage1_service import run_stage1_kb_check
from services.text_stage2.text_stage2_service import run_stage2_web_check
from services.text_stage3.text_stage3_service import run_stage3_online_search
from services.llm_service import extract_claim_and_query,llm_query_extractor_fallback 
import numpy as np

def process_fake_news_pipeline(
    raw_text,
    collection,
    transformer,
    nli,
    client,
    browser,
    text_classifier,
    searx_session,
    headers
):
    try:

        # ===== STAGE 1 =====
        s1 = run_stage1_kb_check(collection, transformer, nli, raw_text)

        if s1.get("status") == "success":
            return s1


        # ===== STAGE 2 =====
        s2 = run_stage3_online_search(
            raw_text,
            transformer,
            nli,
            searx_session,
            headers,
            text_classifier
        )

        if s2.get("status") == "success":
            return s2


        # ===== FALLBACK LLM =====
        if s2.get("status") == "fail":
            query = llm_query_extractor_fallback(raw_text, client)

            s2_retry = run_stage3_online_search(
                query,
                transformer,
                nli,
                searx_session,
                headers,
                text_classifier
            )

            if s2_retry.get("status") == "success":
                return s2_retry

        # ===== FINAL FALLBACK =====
        # fact_check_data = extract_clean_query(raw_text, transformer)

        # query = fact_check_data
        # klaim = raw_text
        # # klaim = fact_check_data["claim"]
        # # query = fact_check_data["main_query"]
        # artikel = await run_stage2_web_check(query, klaim, transformer, nli, client, browser)

        return {
            "status": "fail"
        }

    except Exception as e:

        # ===== GLOBAL ERROR HANDLING =====
        return {
            "status": "error",
            "message": str(e)
        }


def extract_clean_query(text,transformer):
    words = list(set(text.lower().split()))
    clean_words = [w for w in words if len(w) > 3]
    
    if not clean_words: return "berita hoaks terbaru -youtube"

    sentence_vec = transformer.encode([text])
    word_vecs = transformer.encode(clean_words)
    
    scores = np.dot(word_vecs, sentence_vec.T).flatten()
    
    top_indices = np.argsort(scores)[-5:]
    keywords = [clean_words[i] for i in top_indices]
    
    return f"berita {' '.join(keywords)} -youtube"