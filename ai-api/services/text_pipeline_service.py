from services.text_stage1.text_stage1_service import run_stage1_kb_check
from services.text_stage2.text_stage2_service import run_stage2_web_check
from services.llm_service import extract_claim_and_query
# from text_stage3_service import finalize_stage3

def process_fake_news_pipeline(raw_text, collection, transformer, nli,client):

    # ===== STAGE 1 =====
    s1 = run_stage1_kb_check(collection, transformer, nli, raw_text)
    if s1["status"] == "success":
        return s1
    
    fact_check_data = extract_claim_and_query(raw_text, client)

    klaim = fact_check_data["claim"]
    query = fact_check_data["main_query"]
    
    artikel = run_stage2_web_check(query,klaim,transformer,nli,client)
    return artikel