<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class HoaxDetectionController extends Controller
{
    public function detectText(Request $request)
    {
        // Validasi input dari frontend
        $request->validate([
            'input_text' => 'required|string'
        ]);

        $inputText = $request->input_text;

        // ==========================================
        // 1. DETEKSI HOAX (API)
        // Simulasi mendapat response dari API AI (Python)
        // ==========================================
        $aiApiResponse = [
            "top_k" => 1,
            "query" => $inputText,
            "data" => [
                [
                    "id" => "1", // Anggap ini relasi ke knowledge_base.id
                    "score" => 0.27239328622817993,
                    "judul" => null,
                    "nli_label" => "entailment",
                    "nli_score" => 2.5755879878997803,
                    "hoax_text" => "Beredar klaim di media sosial bahwa tidak ada kapal Pertamina yang tertahan di Selat Hormuz...",
                    "fact_text" => "Klaim tersebut hoaks. Faktanya, hingga 10 Maret 2026, dua kapal Pertamina yaitu VLCC Pertamina Pride dan VLCC Gamsunoro masih tertahan di Selat Hormuz.",
                    "category" => "hoaks"
                ]
            ],
            "status" => "success"
        ];

        // Gunakan DB Transaction agar jika ada error, data tidak tersimpan setengah-setengah
        DB::beginTransaction();

        try {
            // Ekstrak data dari response AI
            $aiData = $aiApiResponse['data'][0];

            // Konversi kategori 'hoaks' menjadi 'HOAX' atau 'FAKTA'
            $finalLabel = strtoupper($aiData['category']) === 'HOAKS' ? 'HOAX' : 'FAKTA';

            // Untuk UI persentase, kita buat simulasi dari score (misal default 70% jika hoax)
            // Di real-case, kamu bisa hitung dari probabilitas API
            $hoaxPercentage = 70;

            // ==========================================
            // 2. SIMPAN DATA
            // ==========================================

            // A. Simpan ke tabel 'requests'
            $requestId = DB::table('requests')->insertGetId([
                'input_text' => $inputText,
                'final_label' => $finalLabel,
                'final_confidence' => $hoaxPercentage / 100,
                'status' => 'completed',
                'reason' => $aiData['fact_text'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // --- TAMBAHAN FIX ERROR FOREIGN KEY ---
            // Cek apakah knowledge_id sudah ada di database, jika belum buatkan otomatis
            $knowledgeId = $aiData['id'];
            if (!DB::table('knowledge_base')->where('id', $knowledgeId)->exists()) {
                DB::table('knowledge_base')->insert([
                    'id' => $knowledgeId,
                    'title' => 'Data Dummy Knowledge Base',
                    'hoax_text' => $aiData['hoax_text'],
                    'fact_text' => $aiData['fact_text'],
                    'category' => $aiData['category'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            // --------------------------------------

            // B. Simpan ke tabel 'stage1_results'
            DB::table('stage1_results')->insert([
                'request_id' => $requestId,
                'knowledge_id' => $knowledgeId,
                'similarity_score' => $aiData['score'],
                'nli_score' => $aiData['nli_score'],
                'predicted_label' => $finalLabel,
                'is_stop' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();

            // ==========================================
            // 3. MENAMPILKAN HASIL (Return ke Frontend)
            // ==========================================
            return response()->json([
                'status' => 'success',
                'message' => 'Deteksi selesai dilakukan.',
                'data' => [
                    'request_id' => $requestId,
                    'original_text' => $inputText,
                    'analysis' => [
                        'label' => $finalLabel, // "HOAX"
                        'percentage_hoax' => $hoaxPercentage, // 70
                        'percentage_fact' => 100 - $hoaxPercentage, // 30
                        'explanation' => $aiData['fact_text'],
                    ],
                    // Data dummy untuk link rujukan seperti di desain UI
                    'sources' => [
                        [
                            'title' => 'TurnBackHoax (2026). Kapal Pertamina di Selat Hormuz.',
                            'url' => 'https://turnbackhoax.id/...'
                        ],
                        [
                            'title' => 'Kominfo (2026). Klarifikasi Isu Selat Hormuz.',
                            'url' => 'https://kominfo.go.id/...'
                        ]
                    ]
                ]
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deteksi hoax: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat memproses data.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
