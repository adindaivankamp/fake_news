import torch

def search_similar(knowledge_base, query_embedding, top_k=5):
    results = knowledge_base.query(
        query_embeddings=[query_embedding],
        n_results=top_k
    )

    ids = results["ids"][0]
    distances = results["distances"][0]

    output = []
    for i in range(len(ids)):
        output.append({
            "id": ids[i],
            "score": distances[i],  
        })

    return output


def search_from_text(knowledge_base, model, query_text, top_k=5):
    query_embedding = model.encode(query_text).tolist()

    try:
        result = search_similar(
            knowledge_base,
            query_embedding,
            top_k
        )

        return {
            "status": "success",
            "data": result,
            "query_embedding": query_embedding
        }

    except Exception as e:
        return {
            "status": "error",
            "message": str(e),
            "query_embedding": query_embedding
        }
    
def insert_to_chroma(df, list_id, model, knowledge_base, batch_size=32):
    
    df['teks_vektor'] = df.get('klaim', df.get('penjelasan')).fillna("")
    list_teks = df['teks_vektor'].tolist()
    clean_ids = [str(i) for i in list_id]
    device = "cuda" if torch.cuda.is_available() else "cpu"
    print(f"🧠 Mulai membuat vektor menggunakan {device.upper()}...")
    
    list_vektor = model.encode(
        list_teks,
        show_progress_bar=True,
        batch_size=batch_size,
        device=device
    ).tolist()
    
    knowledge_base.add(
        ids=clean_ids,
        embeddings=list_vektor,
    )
    
    print("🎉 Semua proses selesai! Data tersinkronisasi di MySQL dan ChromaDB.")
    
def input_text_request(text_request, vector, request_id):
    print(f"🔍 Menerima request_id {request_id} dengan vektor: {vector}")
    if vector is None or len(vector) == 0:
        raise ValueError("Vector tidak boleh kosong")
    print(f"🧠 Menyisipkan vektor untuk request_id {request_id} ke ChromaDB...")

    text_request.add(
        ids=[str(request_id)],
        embeddings=[vector]
    )
    
def search_similar_input(embedding,text_request,THRESHOLD = 0.90):

    results = text_request.query(
        query_embeddings=[embedding],
        n_results=1
    )
    print(results)

    ids = results.get("ids", [[]])[0]
    distances = results.get("distances", [[]])[0]

    # tidak ada data
    if not ids:
        return {
            "status": "fail"
        }

    distance = distances[0]
    similarity = 1 - distance
    similarity = max(0.0, min(1.0, similarity))
    # similarity kurang dari threshold
    if similarity <= THRESHOLD:
        return {
            "status": "fail"
        }

    return {
        "status": "success",
        "request_id": ids[0],
        "similarity": distance
    }