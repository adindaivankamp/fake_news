# 🛡️ Lensa Hoax — AI Fake News Detection System

> “Di era informasi yang bergerak lebih cepat daripada kebenaran, Lensa Hoax hadir sebagai filter awal untuk membantu manusia menilai ulang apa yang mereka baca dan lihat.”

Lensa Hoax adalah sistem berbasis AI yang dirancang untuk mendeteksi berita palsu (hoax) melalui **teks dan gambar**, serta dapat diakses melalui **web interface dan WhatsApp bot**.

Project ini dikembangkan sebagai **Project Based Learning (PBL)** dan masih dalam tahap pengembangan aktif (*Work in Progress*).

---

## 📌 The Problem

Setiap hari, informasi tersebar tanpa batas:
- Berita viral yang belum tentu benar
- Gambar yang sudah dimanipulasi
- Konten yang sulit diverifikasi secara cepat

Manusia butuh alat bantu untuk:
> ⚡ Mengecek kebenaran informasi secara cepat dan sederhana

---

## 💡 The Solution — Lensa Hoax

Lensa Hoax menggunakan pendekatan AI untuk membantu proses verifikasi informasi dalam 2 bentuk utama:

- 📝 **Text-based detection** → menganalisis isi berita
- 🖼️ **Image-based detection** → menganalisis konteks visual berita

Hasil akhir ditampilkan sebagai:
> 📊 Probabilitas: Hoax vs Fakta

---

## 🚀 Key Features

### 📝 AI Text Detection
Masukkan teks berita dan sistem akan menganalisis kemungkinan hoax secara otomatis.

### 🖼️ Image-Based Detection
Unggah gambar berita untuk mendeteksi indikasi informasi palsu atau manipulasi visual.

### 💬 WhatsApp Bot Integration
Tanpa membuka website, pengguna bisa langsung:
- Kirim teks
- Kirim gambar  
dan mendapatkan hasil analisis otomatis melalui WhatsApp (Fonnte API).

### 🌐 Web Experience
Interface sederhana untuk:
- Input teks
- Upload gambar
- Melihat hasil prediksi secara real-time

---

## 🧠 How It Thinks (AI System)

Di balik layar, Lensa Hoax menggunakan beberapa komponen AI:

- 🔤 Text embedding untuk memahami makna kalimat
- 📊 Machine Learning model (Random Forest) untuk klasifikasi
- 🔍 Semantic analysis untuk melihat kemiripan konteks
- 🧠 Model NLI untuk memahami hubungan antar pernyataan

---

## 🏗️ Tech Stack

### 🔷 Frontend
- Laravel
- Socialite (Google Login)
- Cloudinary (Image Storage)
- Fonnte API (WhatsApp Integration)

### 🔶 AI Backend
- FastAPI (Python)
- Scikit-learn (Random Forest)
- Sentence Transformers
- Playwright (automation & data handling)

### 🗄️ Database
- MySQL

---

## ⚙️ Getting Started

### 📁 1. Clone Project
```bash
cd ai-api
````

---

### 🐍 2. Setup Environment

```bash
python -m venv venv
```

Activate:

**Windows**

```bash
venv\Scripts\activate
```

**Mac / Linux**

```bash
source venv/bin/activate
```

---

### 📦 3. Install Dependencies

```bash
pip install -r requirements.txt
```

---
### 📦 4. Install Tools
```bash
python setup.py --step all
```
---
### ⚙️ 5. Run AI Service

```bash
uvicorn app:app 
```

---

### 🌐 6. Run Web App (Laravel)

```bash
php artisan serve
```

---

## 🤖 AI Models Inside

### 🧠 Text Embedding Model

* `sentence-transformers/paraphrase-multilingual-MiniLM-L12-v2`
* Converts text into semantic vectors
* License: Apache 2.0

### 🔍 NLI Model

* `MoritzLaurer/Multilingual-MiniLMv2-L6-mnli-xnli`
* Understands logical relationship between sentences
* License: Apache 2.0

### 🌳 Classification Model

* Random Forest Classifier (scikit-learn)
* Trained using custom dataset

---

## 👨‍💻 Team

* **Project Manager / AI Engineer** — Purnama Ridzky Nugraha

---

## ⚠️ Status

🚧 **Work in Progress (WIP)**
Lensa Hoax is actively being developed and improved for better accuracy and scalability.

---

## 📜 Closing Note

Lensa Hoax is not just a project —
it is an attempt to bridge the gap between **information speed and information truth**.

> “Because not everything that goes viral is real.”
