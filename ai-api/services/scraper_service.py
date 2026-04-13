import pandas as pd
from playwright.sync_api import sync_playwright
import re
import time
import random

# =========================
# FUNCTION: scrape halaman utama
# =========================
def scrape_new_hoaxes(latest_title: str):
    data_all = []
    # Bersihkan target judul agar perbandingannya akurat (lowercase & strip spasi)
    target_stop = latest_title.strip().lower() if latest_title else None
    
    print(f"🔍 Mencari data baru hingga judul: '{target_stop}'")

    with sync_playwright() as p:
        browser = p.chromium.launch(headless=True)
        context = browser.new_context(
            user_agent="Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36"
        )
        page = context.new_page()

        page.goto("https://www.komdigi.go.id/berita/berita-hoaks")
        page.wait_for_timeout(3000)

        found = False
        page_num = 1
        max_pages = 15 # Batasi agar tidak overload jika target tidak pernah ketemu

        while not found and page_num <= max_pages:
            print(f"📄 Scraping Halaman {page_num}...")
            
            # Ambil semua elemen link artikel
            elements = page.query_selector_all("a[href*='berita-hoaks/detail']")
            
            # Set untuk melacak link unik di halaman INI saja (mencegah double hit di 1 halaman)
            seen_in_page = set()

            for el in elements:
                href = el.get_attribute("href")
                text = el.inner_text()

                if href and text:
                    link = "https://www.komdigi.go.id" + href
                    judul = text.strip().lower()

                    if not judul or "baca selengkapnya" in judul:
                        continue
                    
                    if link in seen_in_page:
                        continue

                    if target_stop and (target_stop in judul or judul in target_stop):
                        print(f"🛑 Stop! Menemukan kemiripan dengan data lama: '{judul}'")
                        found = True
                        break

                    data_all.append((judul, link))
                    seen_in_page.add(link)

            if found:
                break

    
            next_button = page.locator("button:has(svg.chevron-right_icon)")
            
            if next_button.is_visible() and next_button.is_enabled():
                try:
                    next_button.click()
                    page.wait_for_timeout(3000)
                    page_num += 1
                except Exception as e:
                    print(f"⚠️ Gagal klik Next: {e}")
                    break
            else:
                print("🏁 Sudah mencapai halaman terakhir.")
                break

        browser.close()

    # Buat DataFrame
    df = pd.DataFrame(data_all, columns=["judul", "link"])
    
    df = df.drop_duplicates(subset=['link']).reset_index(drop=True)
    
    print(f"✅ Selesai! Berhasil mengambil {len(df)} data baru.")
    return df

# =========================
# FUNCTION: scrape 1 halaman
# =========================
def scrape_single(page, url):
    try:
        page.goto(url)
        page.wait_for_load_state("networkidle")
        page.wait_for_timeout(2000)

        body_text = page.locator("body").inner_text()

        # 1. Ambil Judul
        try:
            judul = page.locator("h3").first.inner_text()
        except:
            judul = ""

        # 2. Ekstrak Tanggal HANYA setelah Judul
        tanggal = None
        pola_tanggal = r'\d{1,2}\s+(?:Januari|Februari|Maret|April|Mei|Juni|Juli|Agustus|September|Oktober|November|Desember)\s+\d{4}'

        if judul and judul in body_text:
            posisi_akhir_judul = body_text.find(judul) + len(judul)
            teks_setelah_judul = body_text[posisi_akhir_judul:]
            match_tanggal = re.search(pola_tanggal, teks_setelah_judul, re.IGNORECASE)
            if match_tanggal:
                tanggal = match_tanggal.group(0)

        # Fallback
        if not tanggal:
            match_tanggal_fallback = re.search(pola_tanggal, body_text, re.IGNORECASE)
            tanggal = match_tanggal_fallback.group(0) if match_tanggal_fallback else None

        # 2 & 3. klaim & Fakta
        match_konten = re.search(
            r'Penjelasan\s*[:]?\s*(.*?)(?=\n\s*(?:Link Counter|Referensi)\s*[:]?|\Z)',
            body_text, re.DOTALL | re.IGNORECASE
        )

        klaim = None
        fakta = None

        if match_konten:
            konten_utama = match_konten.group(1).strip()
            paragraf = re.split(r'\n+', konten_utama)
            paragraf = [p.strip() for p in paragraf if p.strip()]

            if len(paragraf) > 0:
                klaim = paragraf[0] 
            if len(paragraf) > 1:
                fakta = "\n".join(paragraf[1:])

        # 4. Link Counter
        match_link = re.search(
            r'(?:Link Counter|Referensi)\s*[:]?\s*(.*?)(?=Bagikan|\Z)',
            body_text, re.DOTALL | re.IGNORECASE
        )

        link_counter_final = None

        if match_link:
            raw_link_text = match_link.group(1).strip()
            kumpulan_link = re.findall(r'https?://[^\s]+', raw_link_text)

            if len(kumpulan_link) > 0:
                link_counter_final = kumpulan_link
            elif len(raw_link_text) > 0:
                teks_sumber = re.sub(r'\s+', ' ', raw_link_text).strip()
                link_counter_final = [teks_sumber]

        return {
            "tanggal": tanggal,
            "klaim": klaim,
            "fakta": fakta,
            "link_counter": link_counter_final
        }

    except Exception as e:
        print(f"Error di {url}: {e}")
        return {
            "tanggal": None,
            "klaim": None,
            "fakta": None,
            "link_counter": None
        }
        
# =========================
# FUNCTION: scrape semua link
# =========================
def scrape_all(df):
    results = []

    with sync_playwright() as p:
        browser = p.chromium.launch(headless=True)
        page = browser.new_page()

        # set user agent (biar aman)
        page.set_extra_http_headers({
            "User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/120 Safari/537.36"
        })

        for url in df['link']:
            print(f"Scraping: {url}")

            data = scrape_single(page, url)
            results.append(data)

            time.sleep(random.uniform(2, 5))

        browser.close()

    df_detail = pd.DataFrame(results)

    df_final = pd.concat([df.reset_index(drop=True), df_detail], axis=1)

    return df_final

# =========================
# FUNCTION: Retry Scrape untuk Data NaN (Synchronous Version)
# =========================
def retry_scrape_nan(df_to_fix):
    # 1. Cari index baris yang memiliki minimal satu nilai NaN
    nan_indices = df_to_fix[df_to_fix.isnull().any(axis=1)].index
    total_nan = len(nan_indices)

    if total_nan == 0:
        print("✅ Mantap! Tidak ada data yang NaN.")
        return df_to_fix

    print(f"⚠️ Ditemukan {total_nan} baris dengan data NaN. Mulai proses retry...")

    # Menggunakan sync_playwright
    with sync_playwright() as p:
        browser = p.chromium.launch(headless=True)
        page = browser.new_page()

        # Set user agent agar terhindar dari pemblokiran
        page.set_extra_http_headers({
            "User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/120 Safari/537.36"
        })

        # 2. Loop hanya pada baris yang NaN
        for count, idx in enumerate(nan_indices, start=1):
            url = df_to_fix.loc[idx, 'link']
            print(f"[{count}/{total_nan}] Mencoba ulang index {idx}: {url}")

            # Panggil versi sync dari scrape_single
            new_data = scrape_single(page, url)

            # 3. Update data di DataFrame secara langsung menggunakan .at
            df_to_fix.at[idx, 'tanggal'] = new_data['tanggal']
            df_to_fix.at[idx, 'klaim'] = new_data['klaim']
            df_to_fix.at[idx, 'fakta'] = new_data['fakta']
            df_to_fix.at[idx, 'link_counter'] = new_data['link_counter']

            # Delay random 2-5 detik pakai modul time bawaan
            time.sleep(random.uniform(2, 5))

        browser.close()

    print("\n✅ Proses retry selesai!")
    return df_to_fix

# =========================
# FUNCTION: clean data
# =========================
def clean_dataframe(df):

    df['kategori_hoaks'] = df['judul'].apply(
        lambda x: re.search(r'\[(.*?)\]', x).group(1).strip()
        if re.search(r'\[(.*?)\]', x) else None
    )

    df['judul'] = df['judul'].apply(
        lambda x: re.sub(r'\s*\[.*?\]\s*', ' ', x).strip()
    )

    df['tanggal'] = df['tanggal'].str.replace(
        r'[^0-9A-Za-z ]', '', regex=True
    ).str.strip()

    bulan_dict = {
        'Januari':'01','Februari':'02','Maret':'03','April':'04','Mei':'05',
        'Juni':'06','Juli':'07','Agustus':'08','September':'09','Oktober':'10',
        'November':'11','Desember':'12'
    }

    def ubah_tanggal_manual(tgl_str):
        for bulan, angka in bulan_dict.items():
            if bulan in tgl_str:
                tgl_str = tgl_str.replace(bulan, angka)
        return tgl_str

    df['tanggal'] = df['tanggal'].apply(ubah_tanggal_manual)
    df['tanggal'] = pd.to_datetime(df['tanggal'], dayfirst=True, errors='coerce')

    df = df.dropna(subset=['tanggal'])
    df = df.sort_values(by='tanggal', ascending=False).reset_index(drop=True)

    df = df.drop_duplicates(subset=['judul'])

    return df