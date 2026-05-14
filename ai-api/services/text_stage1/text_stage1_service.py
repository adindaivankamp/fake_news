from collections import Counter
from services.chroma_service import search_from_text
from services.nli_service import run_nli_top_label
from services.db_service import get_row_by_id


def _majority_label(nli_scores):
    labels = [r.get("label") for r in nli_scores if r.get("label")]
    if not labels:
        return None
    return Counter(labels).most_common(1)[0][0]


def run_stage1_kb_check(
    collection,
    transformer,
    nli,
    query,
    top_k=1,
    gap_threshold=0.45
):

    query_embedding = None

    def build_response(
        status,
        reason=None,
        final_label=None,
        data=None
    ):
        return {
            "status": status,
            "reason": reason,
            "query": query,
            "top_k": top_k,
            "final_label": final_label,
            "query_embedding": query_embedding,
            "data": data or []
        }

    try:

        # =========================
        # SEARCH
        # =========================
        search_result = search_from_text(
            collection,
            transformer,
            query,
            top_k=top_k
        )

        query_embedding = search_result.get("query_embedding")

        if search_result.get("status") == "error":
            return build_response(
                status="error",
                reason=search_result.get("message")
            )

        search_data = search_result.get("data", [])

        if not search_data:
            return build_response(
                status="fail",
                reason="no_search_results"
            )

        # =========================
        # FILTER DISTANCE
        # =========================
        filtered = [
            r for r in search_data
            if r.get("score", 1.0) <= gap_threshold
        ]

        if not filtered:
            return build_response(
                status="fail",
                reason="no_results_pass_threshold"
            )

        # =========================
        # GET ROWS
        # =========================
        candidate_rows = []
        valid_filtered = []

        for r in filtered:
            row = get_row_by_id(r["id"])

            if row:
                candidate_rows.append(row)
                valid_filtered.append(r)

        if not candidate_rows:
            return build_response(
                status="fail",
                reason="no_candidate_rows_found"
            )

        # =========================
        # STAGE 1
        # title vs query
        # =========================
        pairs = [
            (query, row.get("title", ""))
            for row in candidate_rows
        ]

        nli_scores = run_nli_top_label(nli, pairs)

        majority_label = _majority_label(nli_scores)

        if not majority_label:
            return build_response(
                status="fail",
                reason="nli_failed"
            )

        # =========================
        # DECISION FLOW
        # =========================
        if majority_label == "entailment":

            final_label = 1

        elif majority_label in ["contradiction", "neutral"]:

            # fallback:
            # fact_text vs query
            pairs = [
                (row.get("fact_text", ""), query)
                for row in candidate_rows
            ]

            nli_scores = run_nli_top_label(nli, pairs)

            majority_label = _majority_label(nli_scores)

            if not majority_label:
                return build_response(
                    status="fail",
                    reason="fallback_nli_failed"
                )

            if majority_label == "entailment":
                final_label = 0

            elif majority_label == "contradiction":
                final_label = 1

            elif majority_label == "neutral":
                return build_response(
                    status="fail",
                    reason="neutral_fallback"
                )

            else:
                return build_response(
                    status="fail",
                    reason="unknown_fallback_label"
                )

        else:
            return build_response(
                status="fail",
                reason="unknown_label"
            )

        # =========================
        # ENRICH RESULT
        # =========================
        enriched = []

        for r, nli_res, row in zip(
            valid_filtered,
            nli_scores,
            candidate_rows
        ):

            enriched.append({
                **r,
                "title": row.get("title"),
                "nli_label": nli_res.get("label"),
                "nli_score": nli_res.get("score"),
                "hoax_text": row.get("hoax_text"),
                "fact_text": row.get("fact_text"),
                "category": row.get("category"),
            })

        return build_response(
            status="success",
            final_label=final_label,
            data=enriched
        )

    except Exception as e:

        return build_response(
            status="error",
            reason=str(e)
        )