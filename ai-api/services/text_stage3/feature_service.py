import numpy as np
from services.nli_service import generate_nli_results
from sentence_transformers import util
import torch
import numpy as np

def extract_features(query, results,nli,transformer):
    time_consistency_score = compute_time_consistency_score(results)
    message_similarity_score = compute_message_similarity_score(query, results, transformer)
    nli_features = extract_nli_features(generate_nli_results(query, results, nli))
    
    return {
        "time_consistency_score": time_consistency_score,
        "message_similarity_score": message_similarity_score,
        **nli_features
    }
    
def compute_time_consistency_score(results, scale=86400):
    timestamps = []
    
    for r in results:
        if r.get("date"):
            timestamps.append(r["date"])

    if len(timestamps) > 1:
        sigma = np.std(timestamps)
        f1 = 1 / (1 + sigma / scale)
    else:
        f1 = 0.6 

    return round(float(f1), 4)


def compute_message_similarity_score(query, results, transformer):
    titles = [r["title"] for r in results if r.get("title")]

    if not titles:
        return 0.0

    emb_query = transformer.encode(query, convert_to_tensor=True)
    emb_titles = transformer.encode(titles, convert_to_tensor=True)

    sims = util.cos_sim(emb_query, emb_titles)[0]

    relevant_sims = sims[sims > 0.3]

    if len(relevant_sims) == 0:
        return 0.0

    f2 = torch.mean(relevant_sims).item()
    f2 =round(float(f2), 4)

    return f2

def extract_nli_features(nli_results):
    """
    nli_results: list of dict
    [
        {"entailment": 0.8, "neutral": 0.1, "contradiction": 0.1},
        ...
    ]
    """

    if not nli_results:
        return {
            "mean_entailment": 0,
            "mean_neutral": 0,
            "mean_contradiction": 0,
            "std_entailment": 0,
            "std_neutral": 0,
            "std_contradiction": 0,
        }

    entailments = [r["entailment"] for r in nli_results]
    neutrals = [r["neutral"] for r in nli_results]
    contradictions = [r["contradiction"] for r in nli_results]

    return {
        # mean
        "mean_entailment": float(np.mean(entailments)),
        "mean_contradiction": float(np.mean(contradictions)),

        # std
        "std_entailment": float(np.std(entailments)),
        "std_contradiction": float(np.std(contradictions)),
    }
    