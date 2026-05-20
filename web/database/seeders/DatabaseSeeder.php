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
        // Nonaktifkan foreign key checks sementara
        Schema::disableForeignKeyConstraints();

        $now = Carbon::now();

        // ==========================================
        // 1. Seeder Users 
        // ENUM role: user | admin
        // ==========================================
        $now = now();
        $users = [
            // ID 1
            ['id' => 1, 'name' => 'Fauzi', 'email' => 'Fauzi@admin.com', 'role' => 'admin', 'phone_number' => '085773071834'],

            // ID 2 - Guest
            ['id' => 2, 'name' => 'Guest User', 'email' => 'guest@lensa-hoax.com', 'role' => 'guest', 'phone_number' => '000000000000'],

            // Anggota Kelompok lainnya
            ['id' => 3, 'name' => 'Desi', 'email' => 'desi@student.com', 'role' => 'admin', 'phone_number' => '083841065365'],
        ];

        $userData = [];
        foreach ($users as $user) {
            $userData[] = [
                'id' => $user['id'], // Menambahkan ID eksplisit
                'name' => $user['name'],
                'email' => $user['email'],
                'phone_number' => $user['phone_number'],
                'role' => $user['role'],
                'created_at' => $now,
            ];
        }

        // Gunakan statement ini jika database kamu MySQL untuk mengizinkan insert ID manual
        DB::table('users')->insert($userData);

        // ==========================================
        // 2. Seeder Images 
        // ==========================================
        $images = [
             ['file_path' => 'uploads/images/ss_grup_keluarga_covid.jpg', 'original_filename' => 'ss_grup_keluarga_covid.jpg'],
             ['file_path' => 'uploads/images/link_phishing_kuota.png', 'original_filename' => 'link_kuota_gratis.png'],
             ['file_path' => 'uploads/images/brosur_cpns_palsu.pdf', 'original_filename' => 'brosur_vip_cpns.pdf'],
             ['file_path' => 'uploads/images/uang_satu_juta_viral.jpg', 'original_filename' => 'uang_1_juta.jpg'],
             ['file_path' => 'uploads/images/pengumuman_pln_palsu.jpeg', 'original_filename' => 'pengumuman_pln.jpeg'],
        ];

        $imageData = array_map(function ($img, $index) use ($now) {
             return array_merge($img, ['uploaded_by' => $index + 1, 'created_at' => $now]);
        }, $images, array_keys($images));
        DB::table('images')->insert($imageData);


        // ==========================================
        // 3. Seeder Requests 
        // FIX ENUM final_label: real | fake
        // FIX ENUM status: pending | stage1 | stage2 | image
        // ==========================================

        $requests = [
            ['input_text' => 'Tolong sebarkan! Rebusan bawang putih bisa membunuh virus.', 'final_label' => 'fake', 'final_confidence' => 0.96, 'status' => 'stage2'],
            ['input_text' => 'BANTUAN KUOTA GRATIS 100GB UNTUK PELAJAR.', 'final_label' => 'fake', 'final_confidence' => 0.99, 'status' => 'stage2'],
            ['input_text' => 'Pendaftaran CPNS Jalur Khusus (VIP) langsung penempatan.', 'final_label' => 'fake', 'final_confidence' => 0.95, 'status' => 'stage1'],
            ['input_text' => 'Uang baru pecahan 1 juta rupiah mulai diedarkan hari ini.', 'final_label' => 'fake', 'final_confidence' => 0.89, 'status' => 'stage1'],
            ['input_text' => 'Akan ada pemadaman listrik total Jawa Timur 25-27 April.', 'final_label' => null, 'final_confidence' => null, 'status' => 'pending'], // Pending tidak punya hasil
        ];

        $requestData = array_map(function ($req, $index) use ($now) {
            return array_merge($req, ['image_id' => $index + 1, 'created_at' => $now]);
        }, $requests, array_keys($requests));
        DB::table('requests')->insert($requestData);


        // ==========================================
        // 4. Seeder Image Search Results
        // FIX JSON array untuk source_url
        // ==========================================
        $imageSearchResults = [
            ['source_url' => 'https://turnbackhoax.id/2020/01/bawang-putih-sembuhkan-virus-corona', 'similarity_score' => 0.92, 'mean_date_score' => 0.90],
            ['source_url' => 'https://kominfo.go.id/content/detail/hoaks-kuota-gratis', 'similarity_score' => 0.98, 'mean_date_score' => 0.85],
            ['source_url' => 'https://www.bkn.go.id/pengumuman/awas-penipuan-cpns', 'similarity_score' => 0.85, 'mean_date_score' => 0.80],
            ['source_url' => 'https://turnbackhoax.id/2021/05/uang-pecahan-1-juta', 'similarity_score' => 0.90, 'mean_date_score' => 0.88],
            ['source_url' => 'https://web.pln.co.id/media/siaran-pers/klarifikasi-hoaks-pemadaman', 'similarity_score' => 0.77, 'mean_date_score' => 0.75],
        ];

        $imgSearchResultData = array_map(function ($res, $index) use ($now) {
            return array_merge($res, [
                'request_id' => $index + 1,
                'source_url' => json_encode($res['source_url']), // Convert ke JSON sesuai skema
                'created_at' => $now
            ]);
        }, $imageSearchResults, array_keys($imageSearchResults));
        DB::table('image_search_results')->insert($imgSearchResultData);


        // ==========================================
        // 5. Seeder Stage 1 Results
        // ==========================================
        $stage1Results = [
            ['similarity_score' => 0.95, 'nli_score' => 0.88, 'is_stop' => true],
            ['similarity_score' => 0.99, 'nli_score' => 0.92, 'is_stop' => true],
            ['similarity_score' => 0.85, 'nli_score' => 0.80, 'is_stop' => false],
            ['similarity_score' => 0.70, 'nli_score' => 0.65, 'is_stop' => false],
            ['similarity_score' => 0.60, 'nli_score' => 0.55, 'is_stop' => false],
        ];

        $stage1Data = array_map(function ($res, $index) use ($now) {
            return array_merge($res, ['request_id' => $index + 1, 'knowledge_id' => $index + 1, 'created_at' => $now]);
        }, $stage1Results, array_keys($stage1Results));
        DB::table('stage1_results')->insert($stage1Data);


        // ==========================================
        // 6. Seeder Feedbacks 
        // ==========================================
        $feedbacks = [
            ['feedback' => 'Wah, makasih banget! Hampir aja ibuku sebarin ke grup arisan keluarga.'],
            ['feedback' => 'Aplikasi deteksi hoax yang sangat membantu untuk cek link penipuan.'],
            ['feedback' => 'Deteksinya cepat, tolong tambahkan fitur report langsung ke Kominfo.'],
            ['feedback' => 'Hasilnya sesuai. Gambar uang 1 juta itu memang sudah sering beredar.'],
            ['feedback' => 'Sistem masih ragu-ragu di bagian pemadaman listrik, mungkin perlu update database.'],
        ];

        $feedbackData = array_map(function ($fb, $index) use ($now) {
            return array_merge($fb, ['user_id' => $index + 1, 'request_id' => $index + 1, 'created_at' => $now]);
        }, $feedbacks, array_keys($feedbacks));
        DB::table('feedbacks')->insert($feedbackData);


        // ==========================================
        // 7. Seeder Stage 2 Results 
        // ==========================================
        $stage2Results = [
            [
                'urls' => [
                    'https://turnbackhoax.id/bawang-putih',
                    'https://kominfo.go.id/awas-phishing'
                ]
            ],
            [
                'urls' => [
                    'https://bkn.go.id/klarifikasi-vip',
                    'https://bi.go.id/klarifikasi-uang'
                ]
            ]
        ];

        $stage2Data = array_map(function ($res, $index) use ($now) {
            return [
                'request_id' => $index + 1,
                'time_credibility' => 0.80 + ($index * 0.03),
                'title_credibility' => 0.78 + ($index * 0.04),
                'mean_contradiction' => 0.10 + ($index * 0.01),
                'mean_entailment' => 0.70 + ($index * 0.02),
                'std_contradiction' => 0.05 + ($index * 0.005),
                'url' => json_encode($res['urls']), // JSON encode sesuai skema
                'created_at' => $now
            ];
        }, $stage2Results, array_keys($stage2Results));
        DB::table('stage2_results')->insert($stage2Data);


        // ==========================================
        // 8. Seeder User Interactions
        // FIX ENUM source_channel: web | whatsapp
        // FIX ENUM interaction_type: new_detection | accepted_history | redetection
        // ==========================================
        $now = now();

        // 1. Siapkan 10 data (contoh)
        $userInteractions = [
            ['source_channel' => 'whatsapp', 'interaction_type' => 'new_detection'],
            ['source_channel' => 'whatsapp', 'interaction_type' => 'redetection'],
            ['source_channel' => 'web',      'interaction_type' => 'new_detection'],
            ['source_channel' => 'whatsapp', 'interaction_type' => 'new_detection'],
            ['source_channel' => 'web',      'interaction_type' => 'accepted_history'],
            ['source_channel' => 'whatsapp', 'interaction_type' => 'new_detection'],
            ['source_channel' => 'web',      'interaction_type' => 'redetection'],
            ['source_channel' => 'whatsapp', 'interaction_type' => 'new_detection'],
            ['source_channel' => 'web',      'interaction_type' => 'new_detection'],
            ['source_channel' => 'whatsapp', 'interaction_type' => 'accepted_history'],
        ];

        // 2. Buat "pool" ID yang memastikan angka 1-5 ada, sisanya sisipkan angka acak 1-5
        $requestIds = [1, 2, 3, 4, 5]; // Pastikan 1-5 masuk dulu
        for ($i = 0; $i < 5; $i++) {
            $requestIds[] = rand(1, 5); // Tambah 5 angka lagi secara acak (total jadi 10)
        }

        // 3. Acak urutan pool tersebut agar tidak berurutan 1,2,3,4,5 di awal
        shuffle($requestIds);

        // 4. Masukkan ke dalam array_map
        $interactionData = array_map(function ($interact, $index) use ($now, $requestIds) {
            return array_merge($interact, [
                'user_id'    => $index + 1,
                'request_id' => $requestIds[$index], // Ambil dari pool yang sudah diacak
                'created_at' => $now,
                'updated_at' => $now
            ]);
        }, $userInteractions, array_keys($userInteractions));

        DB::table('user_interactions')->insert($interactionData);

        // ==========================================
        // 10. Seeder Image Results
        // ==========================================
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
