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
            ['name' => 'M. Reishi Fauzi', 'email' => 'reishi@admin.com', 'role' => 'admin', 'phone_number' => '085773071834'],
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
            ['input_text' => 'Tolong sebarkan! Rebusan bawang putih dan air hangat 2 gelas sehari bisa membunuh virus di tenggorokan sebelum masuk ke paru-paru.', 'final_label' => 'HOAX', 'final_confidence' => 0.96, 'status' => 'completed'],
            ['input_text' => 'BANTUAN KUOTA GRATIS 100GB UNTUK PELAJAR. Klik link berikut untuk klaim sebelum kehabisan: http://bantuan-kuota-gratis.site/klaim', 'final_label' => 'HOAX', 'final_confidence' => 0.99, 'status' => 'completed'],
            ['input_text' => 'Telah dibuka pendaftaran CPNS Jalur Khusus (VIP) langsung penempatan wilayah Malang Raya. Hubungi nomor ini untuk syarat dan biaya administrasi.', 'final_label' => 'HOAX', 'final_confidence' => 0.95, 'status' => 'completed'],
            ['input_text' => 'Ini penampakan uang baru pecahan 1 juta rupiah yang sudah mulai diedarkan oleh Bank Indonesia hari ini.', 'final_label' => 'DISINFORMASI', 'final_confidence' => 0.89, 'status' => 'completed'],
            ['input_text' => 'INFO PENTING! Akan ada pemadaman listrik total di seluruh Jawa Timur tanggal 25-27 April karena perbaikan gardu induk.', 'final_label' => 'HOAX', 'final_confidence' => 0.85, 'status' => 'pending'],
        ];

        $requestData = array_map(function($req, $index) use ($now) {
            return array_merge($req, ['image_id' => $index + 1, 'created_at' => $now, 'updated_at' => $now]);
        }, $requests, array_keys($requests));
        DB::table('requests')->insert($requestData);

        // 5. Seeder Image Search Results (Link ke situs fact-checker)
        $imageSearchResults = [
    [
        'source_url' => json_encode([
            'https://turnbackhoax.id/2020/01/bawang-putih-sembuhkan-virus-corona',
            'https://cekfakta.com/bawang-putih-corona'
        ]),
        'similarity_score' => 0.92,
        'mean_date_score' => 0.90
    ],
    [
        'source_url' => json_encode([
            'https://kominfo.go.id/content/detail/hoaks-kuota-gratis',
            'https://cekfakta.com/kuota-gratis-hoaks'
        ]),
        'similarity_score' => 0.98,
        'mean_date_score' => 0.85
    ],
];
        $imgSearchResultData = array_map(function($res, $index) use ($now) {
            return array_merge($res, ['request_id' => $index + 1, 'created_at' => $now, 'updated_at' => $now]);
        }, $imageSearchResults, array_keys($imageSearchResults));
        DB::table('image_search_results')->insert($imgSearchResultData);

        // 6. Seeder Stage 1 Results (Hasil deteksi awal AI)
        $stage1Results = [
            ['similarity_score' => 0.95, 'nli_score' => 0.88,'is_stop' => true],
            ['similarity_score' => 0.99, 'nli_score' => 0.92, 'is_stop' => true],
            ['similarity_score' => 0.85, 'nli_score' => 0.80, 'is_stop' => false],
            ['similarity_score' => 0.70, 'nli_score' => 0.65, 'is_stop' => false],
            ['similarity_score' => 0.60, 'nli_score' => 0.55, 'is_stop' => false],
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

        $stage2Data = array_map(function($res, $index) use ($now) {
    return [
        'request_id' => $index + 1,
        'time_credibility' => 0.80 + ($index * 0.03),
        'title_credibility' => 0.78 + ($index * 0.04),
        'mean_contradiction' => 0.10 + ($index * 0.01),
        'mean_entailment' => 0.70 + ($index * 0.02),
        'std_contradiction' => 0.05 + ($index * 0.005),

        // ⬇️ INI PENTING
        'url' => json_encode($res['urls']),

        'created_at' => $now,
        'updated_at' => $now
    ];
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