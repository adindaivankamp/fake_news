from services.chroma_service import search_from_text
from services.nli_service import run_nli_top_label
from services.db_service import get_row_by_id
from collections import Counter

def run_stage1_kb_check(collection, transformer, nli, query, top_k=1, gap_threshold=0.45):

    results = search_from_text(collection, transformer, query, top_k=top_k)
    
    if not results:
        return {
            "top_k": top_k,
            "data": []
        }

    filtered = [
        r for r in results
        if r["score"] <= gap_threshold
    ]
    print("FILTERED:", filtered)
    if not filtered:
        return {
            "status": "fail"
        }
    print("FILTERED:", filtered)
    candidate_rows = [get_row_by_id(r["id"]) for r in filtered]
    print("CANDIDATE ROWS:", candidate_rows)
    pairs = [(query, row.get("title", "")) for row in candidate_rows]
    print("PAIRS UNTUK NLI:", pairs)
    nli_scores = run_nli_top_label(nli, pairs)
    print("NLI SCORES:", nli_scores)
    labels = [r["label"] for r in nli_scores]

    label_count = Counter(labels)

    majority_label = label_count.most_common(1)[0][0]
    
    if majority_label == "entailment":
        label = 1
    elif majority_label == "contradiction" or majority_label == "neutral":
        pairs = [(row.get("fact_text", ""), query) for row in candidate_rows]
        nli_scores = run_nli(nli, pairs)
        label_count = Counter([r["label"] for r in nli_scores])
        majority_label = label_count.most_common(1)[0][0]
        print("MAJORITY LABEL FAKTA:", majority_label)
        if majority_label == "entailment":
            label = 0
        elif majority_label == "contradiction":
            label = 1
        elif majority_label == "neutral":
            return {
            "top_k": top_k,
            "status": "fail",
            "pairs" : pairs,
            "query": query
        }
    else:
        return {
            "top_k": top_k,
            "status": "fail",
            "pairs" : pairs,
            "query": query
        }
        
        
    enriched = []
    for r, nli_res, row in zip(filtered, nli_scores, candidate_rows):
        enriched.append({
            **r,
            "judul": row.get("judul"),
            "nli_label": nli_res["label"],
            "nli_score": nli_res["score"],
            "hoax_text": row.get("hoax_text"),
            "fact_text": row.get("fact_text"),
            "category": row.get("category"),
        })

    return {
        "top_k": top_k,
        "query": query,
        "data": enriched,
        "status": "success"
    }