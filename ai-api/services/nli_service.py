# services/nli_service.py

from typing import List, Tuple, Dict


LABELS = ["entailment","neutral", "contradiction"]


# =========================
# CORE (single source of truth)
# =========================
def run_nli_raw(
    model,
    pairs: List[Tuple[str, str]],
    batch_size: int = 16
) -> List[Dict[str, float]]:


    if not pairs:
        return []

    scores = model.predict(pairs, batch_size=batch_size)

    return [
        {label: float(s[i]) for i, label in enumerate(LABELS)}
        for s in scores
    ]


# =========================
# WRAPPER: top label
# =========================
def run_nli_top_label(
    model,
    pairs: List[Tuple[str, str]],
    batch_size: int = 16
) -> List[Dict[str, float]]:

    raw_results = run_nli_raw(model, pairs, batch_size)

    return [
        {
            "label": max(r, key=r.get),
            "score": max(r.values())
        }
        for r in raw_results
    ]


# =========================
# HELPER: build pairs
# =========================
def build_pairs(
    query: str,
    results: List[dict],
    field: str = "title",
    reverse: bool = False
) -> List[Tuple[str, str]]:

    pairs = []

    for r in results:
        text = r.get(field, "")

        if isinstance(text, str) and text.strip():
            if reverse:
                pairs.append((text, query))
            else:
                pairs.append((query, text))

    return pairs


def generate_nli_results(
    query: str,
    results: List[dict],
    nli,
    field: str = "title",
    batch_size: int = 16,
    reverse: bool = False
) -> List[Dict[str, float]]:

    pairs = build_pairs(query, results, field=field, reverse=reverse)

    return run_nli_raw(nli, pairs, batch_size)