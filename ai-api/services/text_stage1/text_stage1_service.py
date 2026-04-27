from collections import Counter
from services.chroma_service import search_from_text
from services.nli_service import run_nli_top_label
from services.db_service import get_row_by_id


def _majority_label(nli_scores):
    labels = [r.get("label") for r in nli_scores if r.get("label")]
    if not labels:
        return None
    return Counter(labels).most_common(1)[0][0]


def run_stage1_kb_check(collection, transformer, nli, query, top_k=1, gap_threshold=0.45):

    try:
        results = search_from_text(collection, transformer, query, top_k=top_k)

        if not results:
            return {
                "status": "fail",
                "reason": "no_search_results",
                "data": []
            }

        filtered = [r for r in results if r.get("score", 1.0) <= gap_threshold]

        if not filtered:
            return {
                "status": "fail",
                "reason": "no_results_pass_threshold",
                "data": []
            }

        candidate_rows = []
        valid_filtered = []

        for r in filtered:
            row = get_row_by_id(r["id"])
            if row:
                candidate_rows.append(row)
                valid_filtered.append(r)

        if not candidate_rows:
            return {
                "status": "fail",
                "reason": "no_candidate_rows_found",
                "data": []
            }

        # ===== STAGE 1: title vs query =====
        pairs = [(query, row.get("title", "")) for row in candidate_rows]

        nli_scores = run_nli_top_label(nli, pairs)
        majority_label = _majority_label(nli_scores)

        if not majority_label:
            return {
                "status": "fail",
                "reason": "nli_failed",
                "data": []
            }

        # ===== DECISION FLOW =====
        if majority_label == "entailment":
            final_label = 1

        elif majority_label in ["contradiction", "neutral"]:

            # fallback: fact_text vs query
            pairs = [(row.get("fact_text", ""), query) for row in candidate_rows]
            nli_scores = run_nli_top_label(nli, pairs)
            majority_label = _majority_label(nli_scores)

            if not majority_label:
                return {
                    "status": "fail",
                    "reason": "fallback_nli_failed",
                    "data": []
                }

            if majority_label == "entailment":
                final_label = 0

            elif majority_label == "contradiction":
                final_label = 1

            elif majority_label == "neutral":
                return {
                    "status": "fail",
                    "reason": "neutral_fallback",
                    "query": query,
                    "data": []
                }

        else:
            return {
                "status": "fail",
                "reason": "unknown_label",
                "data": []
            }

        # ===== ENRICH RESULT =====
        enriched = []
        for r, nli_res, row in zip(valid_filtered, nli_scores, candidate_rows):
            enriched.append({
                **r,
                "title": row.get("title"),
                "nli_label": nli_res.get("label"),
                "nli_score": nli_res.get("score"),
                "hoax_text": row.get("hoax_text"),
                "fact_text": row.get("fact_text"),
                "category": row.get("category"),
            })

        return {
            "status": "success",
            "query": query,
            "top_k": top_k,
            "final_label": final_label,
            "data": enriched
        }

    except Exception as e:
        return {
            "status": "error",
            "reason": str(e),
            "query": query,
            "data": []
        }