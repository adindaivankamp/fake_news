def run_nli(nli_model, pairs):
    """
    pairs: list of (query, candidate_text)
    nli_model: CrossEncoder
    return: list of dict {"label": str, "score": float}
    """
    scores = nli_model.predict(pairs)  
    labels = ["contradiction", "neutral", "entailment"]

    results = []
    for s in scores:
        max_idx = s.argmax()
        results.append({
            "label": labels[max_idx],
            "score": float(s[max_idx])
        })
    return results