import os
import json
from dotenv import load_dotenv
from google import genai

# load .env
load_dotenv()


def extract_claim_and_query(raw_text, client):
    prompt = f"""
    Tugas kamu adalah:
    1. Mengekstrak klaim utama dari teks 
    2. Mengubahnya menjadi:
    - sebuah QUERY pencarian berita yang ringkas
    - dan sebuah KLAIM faktual dalam bentuk kalimat lengkap (deklaratif)

    Aturan:
    1. Fokus pada informasi faktual (bukan opini, ajakan, atau doa)
    2. Identifikasi entitas penting: orang, organisasi, lokasi, kejadian, waktu
    3. Hilangkan bagian tidak relevan seperti salam, ajakan "viralkan", atau opini
    4. Jangan mengubah makna asli dari informasi
    5. Gunakan bahasa Indonesia
    6. Query maksimal 10 kata
    7. Klaim harus berupa kalimat lengkap yang bisa diuji kebenarannya (verifiable)
    8. Klaim harus eksplisit (mengandung subjek + predikat + objek/keterangan)
    9. Jangan menambahkan informasi baru yang tidak ada di teks

    Format output (JSON saja):

    {{
    "main_query": "...",
    "claim": "..."
    }}

    Contoh:

    Teks:
    "Sebarkan! Pemerintah akan membagikan bantuan uang tunai sebesar 500 ribu rupiah kepada seluruh warga mulai bulan depan! Ini resmi dan sudah disetujui!"

    Output:
    {{
    "main_query": "bantuan tunai pemerintah 500 ribu bulan depan",
    "claim": "Pemerintah akan membagikan bantuan uang tunai sebesar 500 ribu rupiah kepada seluruh warga mulai bulan depan."
    }}

    Teks:
    "Beredar kabar bahwa artis terkenal ditangkap karena kasus narkoba di Jakarta pada Januari 2025. Mohon doanya."

    Output:
    {{
    "main_query": "artis ditangkap narkoba Jakarta Januari 2025",
    "claim": "Seorang artis terkenal ditangkap karena kasus narkoba di Jakarta pada Januari 2025."
    }}

    Sekarang lakukan untuk teks berikut:

    TEXT MENTAH:
    {raw_text}
    """

    try:
        response = client.models.generate_content(
            model="gemma-3-27b-it",
            contents=prompt,
        )

        raw_text = response.text.strip()

        # bersihkan markdown JSON
        if raw_text.startswith("```json"):
            raw_text = raw_text[7:-3].strip()
        elif raw_text.startswith("```"):
            raw_text = raw_text[3:-3].strip()

        hasil_json = json.loads(raw_text)
        return hasil_json

    except Exception as e:
        return {
            "error": str(e),
            "raw_response": response.text if "response" in locals() else "Gagal koneksi"
        }
        
def llm_fallback_func(klaim, chunks, client):
    context_text = "\n\n".join([c["text"] for c in chunks])

    prompt = f"""
    Kamu adalah sistem verifikasi fakta.

    Tugas:
    Tentukan apakah KLAIM berikut didukung oleh KONTEKS berita yang diberikan.

    Aturan:
    - Gunakan hanya informasi dari konteks
    - Jangan menambah pengetahuan luar
    - Jika tidak cukup informasi → UNCERTAIN
    - Jika konteks mendukung → VALID
    - Jika bertentangan → HOAX
    - Berikan alasan singkat dan jelas
    - Berikan confidence dalam persen (0-100)

    FORMAT OUTPUT (JSON):
    {{
      "label": "VALID | HOAX | UNCERTAIN",
      "confidence": 0-100,
      "reason": "penjelasan singkat"
    }}

    KLAIM:
    {klaim}

    KONTEKS:
    {context_text}
    """

    response = client.models.generate_content(
        model="gemma-3-27b-it",
        contents=prompt,
    )

    raw_text = response.text.strip()

    if raw_text.startswith("```json"):
        raw_text = raw_text[7:-3].strip()
    elif raw_text.startswith("```"):
        raw_text = raw_text[3:-3].strip()

    result = json.loads(raw_text)

    return {
        "result": result,
        "chunks": chunks
    }
    
import json

def llm_query_extractor_fallback(text, client):
    prompt = f"""
You are a query extraction system for a news fact-checking search engine.

Task:
Extract a short, factual search query from the input text.

Rules:
- Focus only on factual information (entities, events, locations, dates)
- Ignore opinions, warnings, and irrelevant phrases
- Do NOT summarize the text
- Do NOT add new information
- Output must be max 12 words
- Output must be keyword-style search query
- Return ONLY valid JSON

FORMAT OUTPUT:
{{
  "query": "your extracted search query here"
}}

INPUT TEXT:
{text}
"""

    response = client.models.generate_content(
        model="gemma-3-27b-it",
        contents=prompt,
    )

    raw_text = response.text.strip()

    # remove markdown if exists
    if raw_text.startswith("```json"):
        raw_text = raw_text[7:-3].strip()
    elif raw_text.startswith("```"):
        raw_text = raw_text[3:-3].strip()

    result = json.loads(raw_text)

    return result