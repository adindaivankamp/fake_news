<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Nonaktifkan foreign key checks sementara karena tabel knowledge_base tidak di-seed
        Schema::disableForeignKeyConstraints();

        $now = Carbon::now();

        // 1. Seeder Users (Menggunakan data pengguna lokal)
        $users = [
            ['name' => 'M. Reishi Fauzi', 'email' => 'reishi@admin.com', 'role' => 'admin', 'phone_number' => '081234567890'],
            ['name' => 'Axelo', 'email' => 'axelo@student.com', 'role' => 'user', 'phone_number' => '081298765432'],
            ['name' => 'Firman', 'email' => 'firman@student.com', 'role' => 'user', 'phone_number' => '081345678901'],
            ['name' => 'Gilang', 'email' => 'gilang@student.com', 'role' => 'user', 'phone_number' => '085612345678'],
            ['name' => 'Purnama', 'email' => 'purnama@student.com', 'role' => 'user', 'phone_number' => '087812349876'],
        ];

        $userData = [];
        foreach ($users as $user) {
            $userData[] = [
                'name' => $user['name'],
                'email' => $user['email'],
                'email_verified_at' => $now,
                'password' => Hash::make('password123'),
                'phone_number' => $user['phone_number'],
                'role' => $user['role'],
                'login_token' => Str::random(15),
                'token_expired_at' => $now->copy()->addDays(1),
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }
        DB::table('users')->insert($userData);

        // 2. Seeder Riwayat (Judul pencarian hoax yang realistis)
        $riwayats = [
            ['judul' => 'Cek Fakta: Benarkah bawang putih rebus bisa menyembuhkan COVID-19?', 'hoax_percentage' => 95],
            ['judul' => 'Pesan berantai WhatsApp tentang pembagian kuota internet gratis 100GB dari pemerintah.', 'hoax_percentage' => 88],
            ['judul' => 'Informasi pendaftaran CPNS jalur VIP tanpa tes.', 'hoax_percentage' => 98],
            ['judul' => 'Video uang kertas pecahan baru Rp 1.000.000 bergambar Soekarno-Hatta.', 'hoax_percentage' => 92],
            ['judul' => 'Berita pemadaman listrik serentak se-Jawa Timur selama 3 hari berturut-turut.', 'hoax_percentage' => 75],
        ];

        $riwayatData = array_map(fn($r) => array_merge($r, ['created_at' => $now, 'updated_at' => $now]), $riwayats);
        DB::table('riwayat')->insert($riwayatData);

        // 3. Seeder Images (Nama file gambar bukti hoax)
        $images = [
            ['file_path' => 'uploads/images/ss_grup_keluarga_covid.jpg', 'original_filename' => 'ss_grup_keluarga_covid.jpg'],
            ['file_path' => 'uploads/images/link_phishing_kuota.png', 'original_filename' => 'link_kuota_gratis.png'],
            ['file_path' => 'uploads/images/brosur_cpns_palsu.pdf', 'original_filename' => 'brosur_vip_cpns.pdf'],
            ['file_path' => 'uploads/images/uang_satu_juta_viral.jpg', 'original_filename' => 'uang_1_juta.jpg'],
            ['file_path' => 'uploads/images/pengumuman_pln_palsu.jpeg', 'original_filename' => 'pengumuman_pln.jpeg'],
        ];

        $imageData = array_map(function($img, $index) use ($now) {
            return array_merge($img, ['uploaded_by' => $index + 1, 'created_at' => $now, 'updated_at' => $now]);
        }, $images, array_keys($images));
        DB::table('images')->insert($imageData);

        // 4. Seeder Requests (Teks input asli copas dari pesan viral)
        $requests = [
            ['input_text' => 'Tolong sebarkan! Rebusan bawang putih dan air hangat 2 gelas sehari bisa membunuh virus di tenggorokan sebelum masuk ke paru-paru.', 'final_label' => 'HOAX', 'final_confidence' => 0.96, 'status' => 'completed', 'reason' => 'Telah dibantah oleh WHO dan Kemenkes.'],
            ['input_text' => 'BANTUAN KUOTA GRATIS 100GB UNTUK PELAJAR. Klik link berikut untuk klaim sebelum kehabisan: http://bantuan-kuota-gratis.site/klaim', 'final_label' => 'HOAX', 'final_confidence' => 0.99, 'status' => 'completed', 'reason' => 'Link phishing, bukan domain resmi pemerintah (go.id).'],
            ['input_text' => 'Telah dibuka pendaftaran CPNS Jalur Khusus (VIP) langsung penempatan wilayah Malang Raya. Hubungi nomor ini untuk syarat dan biaya administrasi.', 'final_label' => 'HOAX', 'final_confidence' => 0.95, 'status' => 'completed', 'reason' => 'Pendaftaran CPNS hanya melalui portal resmi BKN (sscasn.bkn.go.id).'],
            ['input_text' => 'Ini penampakan uang baru pecahan 1 juta rupiah yang sudah mulai diedarkan oleh Bank Indonesia hari ini.', 'final_label' => 'DISINFORMASI', 'final_confidence' => 0.89, 'status' => 'completed', 'reason' => 'Gambar yang beredar adalah uang spesimen/uang peringatan, bukan alat pembayaran sah.'],
            ['input_text' => 'INFO PENTING! Akan ada pemadaman listrik total di seluruh Jawa Timur tanggal 25-27 April karena perbaikan gardu induk.', 'final_label' => 'HOAX', 'final_confidence' => 0.85, 'status' => 'pending', 'reason' => 'Menunggu verifikasi lanjutan, belum ada rilis resmi dari PLN.'],
        ];

        $requestData = array_map(function($req, $index) use ($now) {
            return array_merge($req, ['image_id' => $index + 1, 'created_at' => $now, 'updated_at' => $now]);
        }, $requests, array_keys($requests));
        DB::table('requests')->insert($requestData);

        // 5. Seeder Image Search Results (Link ke situs fact-checker)
        $imageSearchResults = [
            ['source_url' => 'https://turnbackhoax.id/2020/01/bawang-putih-sembuhkan-virus-corona', 'similarity_score' => 0.92],
            ['source_url' => 'https://kominfo.go.id/content/detail/hoaks-kuota-gratis', 'similarity_score' => 0.98],
            ['source_url' => 'https://www.bkn.go.id/pengumuman/awas-penipuan-cpns', 'similarity_score' => 0.85],
            ['source_url' => 'https://turnbackhoax.id/2021/05/uang-pecahan-1-juta', 'similarity_score' => 0.90],
            ['source_url' => 'https://web.pln.co.id/media/siaran-pers/klarifikasi-hoaks-pemadaman', 'similarity_score' => 0.77],
        ];

        $imgSearchResultData = array_map(function($res, $index) use ($now) {
            return array_merge($res, ['request_id' => $index + 1, 'created_at' => $now, 'updated_at' => $now]);
        }, $imageSearchResults, array_keys($imageSearchResults));
        DB::table('image_search_results')->insert($imgSearchResultData);

        // 6. Seeder Stage 1 Results (Hasil deteksi awal AI)
        $stage1Results = [
            ['similarity_score' => 0.95, 'nli_score' => 0.88, 'predicted_label' => 'HOAX', 'is_stop' => true],
            ['similarity_score' => 0.99, 'nli_score' => 0.92, 'predicted_label' => 'HOAX', 'is_stop' => true],
            ['similarity_score' => 0.85, 'nli_score' => 0.80, 'predicted_label' => 'HOAX', 'is_stop' => false],
            ['similarity_score' => 0.70, 'nli_score' => 0.65, 'predicted_label' => 'DISINFORMASI', 'is_stop' => false],
            ['similarity_score' => 0.60, 'nli_score' => 0.55, 'predicted_label' => 'HOAX', 'is_stop' => false],
        ];

        $stage1Data = array_map(function($res, $index) use ($now) {
            return array_merge($res, ['request_id' => $index + 1, 'knowledge_id' => $index + 1, 'created_at' => $now, 'updated_at' => $now]);
        }, $stage1Results, array_keys($stage1Results));
        DB::table('stage1_results')->insert($stage1Data);

        // 7. Seeder Feedbacks (Ulasan pengguna)
        $feedbacks = [
            ['feedback' => 'Wah, makasih banget! Hampir aja ibuku sebarin ke grup arisan keluarga.'],
            ['feedback' => 'Aplikasi deteksi hoax yang sangat membantu untuk cek link penipuan.'],
            ['feedback' => 'Deteksinya cepat, tolong tambahkan fitur report langsung ke Kominfo.'],
            ['feedback' => 'Hasilnya sesuai. Gambar uang 1 juta itu memang sudah sering beredar.'],
            ['feedback' => 'Sistem masih ragu-ragu di bagian pemadaman listrik, mungkin perlu update database.'],
        ];

        $feedbackData = array_map(function($fb, $index) use ($now) {
            return array_merge($fb, ['user_id' => $index + 1, 'request_id' => $index + 1, 'created_at' => $now, 'updated_at' => $now]);
        }, $feedbacks, array_keys($feedbacks));
        DB::table('feedbacks')->insert($feedbackData);

        // 8. Seeder Stage 2 Results (Ekstraksi artikel berita asli)
        $stage2Results = [
            ['article_title' => '[SALAH] Bawang Putih Dapat Menyembuhkan Virus Corona', 'article_url' => 'https://turnbackhoax.id/bawang-putih', 'chunk_text' => 'WHO menegaskan bahwa tidak ada bukti bawang putih dapat melindungi seseorang dari infeksi COVID-19.', 'predicted_label' => 'HOAX'],
            ['article_title' => 'Awas Penipuan Link Phishing Kuota Gratis Kemdikbud', 'article_url' => 'https://kominfo.go.id/awas-phishing', 'chunk_text' => 'Masyarakat diimbau untuk tidak mengklik tautan tidak resmi yang menjanjikan kuota internet gratis.', 'predicted_label' => 'HOAX'],
            ['article_title' => 'BKN Tegaskan Tidak Ada Jalur VIP CPNS', 'article_url' => 'https://bkn.go.id/klarifikasi-vip', 'chunk_text' => 'Penerimaan CPNS selalu dilakukan secara transparan melalui sistem CAT, tanpa adanya jalur khusus atau VIP.', 'predicted_label' => 'HOAX'],
            ['article_title' => 'Penjelasan BI Soal Viral Uang Rp 1 Juta', 'article_url' => 'https://bi.go.id/klarifikasi-uang', 'chunk_text' => 'Uang tersebut merupakan lembar spesimen atau uang specimen (house note) hasil cetakan Peruri.', 'predicted_label' => 'DISINFORMASI'],
            ['article_title' => '[HOAKS] Pemadaman Listrik 3 Hari di Jawa Timur', 'article_url' => 'https://turnbackhoax.id/pln-jatim', 'chunk_text' => 'PLN UID Jawa Timur mengklarifikasi bahwa pesan berantai mengenai pemadaman total adalah tidak benar.', 'predicted_label' => 'HOAX'],
        ];

        $stage2Data = array_map(function($res, $index) use ($now) {
            return array_merge($res, [
                'request_id' => $index + 1,
                'article_date' => $now->copy()->subDays(rand(1, 30))->format('Y-m-d'),
                'chunk_index' => 1,
                'similarity_score' => 0.85 + ($index * 0.02),
                'nli_score' => 0.80 + ($index * 0.03),
                'created_at' => $now,
                'updated_at' => $now
            ]);
        }, $stage2Results, array_keys($stage2Results));
        DB::table('stage2_results')->insert($stage2Data);

        // 9. Seeder User Interactions
        $userInteractions = [
            ['source_channel' => 'whatsapp', 'interaction_type' => 'message_receive'],
            ['source_channel' => 'telegram', 'interaction_type' => 'bot_command'],
            ['source_channel' => 'web', 'interaction_type' => 'form_submit'],
            ['source_channel' => 'whatsapp', 'interaction_type' => 'image_upload'],
            ['source_channel' => 'web', 'interaction_type' => 'feedback_submit'],
        ];

        $interactionData = array_map(function($interact, $index) use ($now) {
            return array_merge($interact, ['user_id' => $index + 1, 'request_id' => $index + 1, 'created_at' => $now, 'updated_at' => $now]);
        }, $userInteractions, array_keys($userInteractions));
        DB::table('user_interactions')->insert($interactionData);

        // 10. Seeder Message Cache (Pesan WA yang masuk ke sistem bot)
        $messageCaches = [
            ['sender_number' => '+6281234567890', 'latest_message' => 'Min, tolong cek ini beneran gak bawang putih bisa ngobatin covid?'],
            ['sender_number' => '+6281298765432', 'latest_message' => 'Cek link kuota gratis ini dong: http://bantuan-kuota-gratis.site'],
            ['sender_number' => '+6281345678901', 'latest_message' => 'Apakah info pendaftaran CPNS VIP ini penipuan?'],
            ['sender_number' => '+6285612345678', 'latest_message' => 'Wah ada uang pecahan baru 1 juta, beneran gak nih?'],
            ['sender_number' => '+6287812349876', 'latest_message' => 'Cek berita mati lampu se-Jawa Timur.'],
        ];

        $messageCacheData = array_map(fn($msg) => array_merge($msg, ['created_at' => $now, 'updated_at' => $now]), $messageCaches);
        DB::table('message_cache')->insert($messageCacheData);

        // 11. Seeder Image Results
        $imageResults = [
            ['link_img' => 'https://turnbackhoax.id/wp-content/uploads/2020/bawang_putih.jpg', 'title' => 'Thumbnail Hoax Bawang Putih'],
            ['link_img' => 'https://kominfo.go.id/images/ilustrasi_phishing.jpg', 'title' => 'Ilustrasi Phishing Kuota'],
            ['link_img' => 'https://turnbackhoax.id/wp-content/uploads/2021/cpns_palsu.jpg', 'title' => 'Brosur CPNS Palsu'],
            ['link_img' => 'https://turnbackhoax.id/wp-content/uploads/2021/uang_spesimen.jpg', 'title' => 'Spesimen Uang 1 Juta'],
            ['link_img' => 'https://turnbackhoax.id/wp-content/uploads/2022/hoax_pln.jpg', 'title' => 'Klarifikasi PLN'],
        ];

        $imgResultData = array_map(fn($res) => array_merge($res, ['created_at' => $now]), $imageResults);
        DB::table('image_results')->insert($imgResultData);

        // Aktifkan kembali foreign key checks
        Schema::enableForeignKeyConstraints();
    }
}