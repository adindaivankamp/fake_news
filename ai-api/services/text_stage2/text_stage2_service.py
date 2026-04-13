from sklearn.metrics.pairwise import cosine_similarity
from services.nli_service import run_nli
from .data_pipeline_service import run_pipeline
from services.llm_service import llm_fallback_func

# =========================
# RETRIEVAL (cosine)
# =========================
def retrieve_top_k(query, transformer, chunk_vectors, chunks, k=5):
    query_vector = transformer.encode(query)
    sims = cosine_similarity([query_vector], chunk_vectors)[0]

    scored = []
    for chunk, score in zip(chunks, sims):
        chunk_copy = chunk.copy()
        chunk_copy["score"] = float(score)
        scored.append(chunk_copy)

    scored = sorted(scored, key=lambda x: x["score"], reverse=True)

    return scored[:k]

# =========================
# NLI CHECK
# =========================
def apply_nli(nli_model, query, chunks):
    pairs = [(c["text"], query) for c in chunks]
    results = run_nli(nli_model, pairs)

    for i in range(len(chunks)):
        chunks[i]["nli_label"] = results[i]["label"]
        chunks[i]["nli_score"] = results[i]["score"]

    return chunks


# =========================
# VALIDATION RULES
# =========================
def is_nli_valid(chunks):
    labels = [c["nli_label"] for c in chunks]

    neutral_count = labels.count("neutral")

    # mayoritas neutral → gagal
    if neutral_count > len(labels) / 2:
        return False

    return True


def is_score_gap_valid(chunks, threshold_gap=0.2):
    scores = [c["nli_score"] for c in chunks]

    if len(scores) < 2:
        return False

    gap = max(scores) - min(scores)

    if gap < threshold_gap:
        return False

    return True


# =========================
# MAIN PIPELINE
# =========================
def run_stage2_web_check(query,klaim,transformer,nli_model,client):
    
    data = run_pipeline(query, transformer)
    data = data["results"][0]
    chunk_vectors = [c["vector"] for c in data["chunks"]]
    # 1. Retrieval
    top_k = retrieve_top_k(query,transformer, chunk_vectors, data["chunks"])
    # 2. Filter similarity
    filtered = filtered = [c for c in top_k if c["score"] >= 0.5]
    if not filtered:
        return {
            "status": "fail",
            "query": query
        }

    # ambil chunks saja
    selected_chunks = [{"text": f["text"]} for f in filtered]

    # 3. NLI
    chunks_with_nli = apply_nli(nli_model, klaim, selected_chunks)

    # 4. Validasi NLI
    if not is_nli_valid(chunks_with_nli):
        return llm_fallback_func(query, filtered, client)
    if not is_score_gap_valid(chunks_with_nli):
        return llm_fallback_func(query, filtered, client)

    # 5. Jika lolos
    return {
        "status": "valid",
        "chunks": chunks_with_nli
    }