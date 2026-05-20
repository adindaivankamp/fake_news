<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

use App\Models\Requests;
use App\Models\KnowledgeBase;
use App\Models\Stage1Results;
use App\Models\Stage2Result;
use App\Models\UserInteractions;
use Illuminate\Foundation\Auth\User;

class TextDetectionController extends Controller
{
    /**
     * ========================================================
     * 1. FUNGSI UTAMA & PEMANGGILAN API
     * ========================================================
     */
    public function detectText(Request $request)
    {
        $request->validate([
            'query' => 'required|string'
        ]);

        $inputText = $request->input('query');
        $userId = Auth::check() ? Auth::id() : 2;

        try {
            // ========================================================
            // A. CEK SIMILARITY SEARCH TERLEBIH DAHULU
            // ========================================================
            $simResponse = Http::timeout(60)->post('http://127.0.0.1:8004/similarity-search', [
                'query' => $inputText
            ]);
            if ($simResponse->successful()) {
                $simData = $simResponse->json();

                // Cek jika status success dari Python API
                if (isset($simData['status']) && $simData['status'] === 'success' && !empty($simData['request_id'])) {

                    $matchedId = $simData['request_id'];
                    $similarityScore = $simData['similarity'] ?? 0;

                    $oldRequest = Requests::find($matchedId);

                    if ($oldRequest) {
                        // HANYA tambah UserInteraction baru yang mengarah ke request lama
                        UserInteractions::create([
                            'user_id'    => $userId,
                            'request_id' => $matchedId,
                        ]);

                        // Format data lama agar persis dengan output frontend
                        $responseData = $this->formatMatchedResponse($oldRequest, $matchedId);

                        return response()->json([
                            'status' => $oldRequest->status === 'stage1' ? 'stage1' : 'stage2',
                            'data' => $responseData
                        ]);
                    }
                }
            }

            // ========================================================
            // B. JIKA TIDAK ADA MATCH, BUAT REQUEST BARU & HIT API DETEKSI
            // ========================================================
            DB::beginTransaction();
            // Baru buat request database jika memang benar-benar tidak ada kemiripan
            $requestData = Requests::create([
                'input_text' => $inputText,
                'status' => 'pending'
            ]);

            $requestId = $requestData->id;

            UserInteractions::create([
                'user_id'    => $userId,
                'request_id' => $requestId,
            ]);

            $response = Http::timeout(120)->post('http://127.0.0.1:8004/text-detection', [
                'query' => $inputText,
                'id_request' => $requestId
            ]);

            if (!$response->successful()) {
                throw new \Exception('Gagal terhubung ke AI API');
            }

            $aiApiResponse = $response->json();

            if (!isset($aiApiResponse['status']) || $aiApiResponse['status'] !== 'success') {
                throw new \Exception('Response AI gagal atau tidak valid');
            }
            // -----------------------

            $stage = $aiApiResponse['stage'] ?? 'stage_1';
            $responseData = [];

            // Arahkan ke fungsi spesifik berdasarkan Stage
            if ($stage === 'stage_1') {
                $responseData = $this->processStage1($aiApiResponse, $requestData);
            } elseif ($stage === 'stage_2') {
                $responseData = $this->processStage2($aiApiResponse, $requestData);
            } else {
                throw new \Exception('Stage tidak dikenali');
            }

            // Jika semua lancar, Commit DB dan kembalikan JSON
            DB::commit();

            return response()->json([
                'status' => $stage === 'stage_1' ? 'stage1' : 'stage2',
                'data' => $responseData
            ]);
        } catch (\Exception $e) {
            // Rollback jika ada transaksi yang sedang berjalan
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }
            Log::error('Error di TextDetectionController: ' . $e->getMessage());
            // Update status error jika requestData baru sudah sempat dibuat
            if (isset($requestData)) {
                Requests::where('id', $requestData->id)->update(['status' => 'error']);
            }


            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * ========================================================
     * 2. FUNGSI KHUSUS: STAGE 1
     * ========================================================
     */
    private function processStage1(array $aiApiResponse, Requests $requestData): array
    {
        if (empty($aiApiResponse['data'])) {
            throw new \Exception('Data stage 1 kosong');
        }

        $aiData = $aiApiResponse['data'][0];
        $requestId = $requestData->id;

        // Label Database
        $finalLabel = strtolower($aiData['category'] ?? '') === 'hoaks' ? 'fake' : 'real';
        $similarityScore = $aiData['score'] ?? 1;

        $finalConfidence = max(0, min(1, 1 - $similarityScore));
        $confidencePercentage = round($finalConfidence * 100, 2);

        $requestData->update([
            'final_label' => $finalLabel,
            'final_confidence' => $finalConfidence,
            'status' => 'stage1'
        ]);

        $knowledgeId = $aiData['id'] ?? null;
        if ($knowledgeId) {
            $knowledge = KnowledgeBase::find($knowledgeId);

            if (!$knowledge) {
                KnowledgeBase::create([
                    'id' => $knowledgeId,
                    'title' => $aiData['title'] ?? '-',
                    'hoax_text' => $aiData['hoax_text'] ?? null,
                    'fact_text' => $aiData['fact_text'] ?? null,
                    'category' => $aiData['category'] ?? null,
                ]);
            }

            Stage1Results::create([
                'request_id' => $requestId,
                'knowledge_id' => $knowledgeId,
                'similarity_score' => $similarityScore,
                'nli_score' => $aiData['nli_score'] ?? 0,
                'is_stop' => true
            ]);
        }

        // ==========================================
        // FORMAT UNTUK FRONTEND
        // ==========================================
        $verdict = ($finalLabel === 'fake') ? 'fake' : 'valid';
        $factText = $aiData['fact_text'] ?? '';

        if (!empty($factText)) {
            $summaryText = ltrim($factText, ', ');
            $sources = [['title' => 'Database Knowledge Base Anti-Hoax', 'url' => '']];
        } else {
            $statusTeks = ($verdict === 'fake') ? 'HOAX' : 'FAKTA';
            $summaryText = "Hasil analisis menemukan indikasi " . $statusTeks . " dengan tingkat keyakinan " . $confidencePercentage . "%.";
            $sources = [];
        }

        return [
            'success'    => true,
            'verdict'    => $verdict,
            'confidence' => $confidencePercentage,
            'summary'    => $summaryText,
            'sources'    => $sources,
            'link_counter' => $knowledge['link_counter'] ?? 0,
            'raw_data'   => [
                'stage' => 'stage1',
                'request_id' => $requestId,
                'title' => $aiData['title'] ?? null,
                'category' => $aiData['category'] ?? null,
            ]
        ];
    }

    /**
     * ========================================================
     * 3. FUNGSI KHUSUS: STAGE 2
     * ========================================================
     */
    private function processStage2(array $aiApiResponse, Requests $requestData): array
    {
        $requestId = $requestData->id;
        $prediction = $aiApiResponse['prediction'] ?? 0;
        $finalLabel = $prediction == 1 ? 'fake' : 'real';

        $rawConfidence = $aiApiResponse['confidence'] ?? 0;
        $finalConfidence = max(0, min(1, $rawConfidence));
        $confidencePercentage = round($finalConfidence * 100, 2);

        $featureVector = $aiApiResponse['feature_vector'] ?? [];
        $requestData->update([
            'final_label' => $finalLabel,
            'final_confidence' => $finalConfidence,
            'status' => 'stage2'
        ]);

        Stage2Result::create([
            'request_id' => $requestId,
            'time_credibility' => $featureVector['time_consistency_score'] ?? null,
            'title_credibility' => $featureVector['message_similarity_score'] ?? null,
            'mean_entailment' => $featureVector['mean_entailment'] ?? null,
            'mean_contradiction' => $featureVector['mean_contradiction'] ?? null,
            'std_contradiction' => $featureVector['std_contradiction'] ?? null,
            'url' => $aiApiResponse['urls'] ?? []
        ]);

        // ==========================================
        // FORMAT UNTUK FRONTEND
        // ==========================================
        $verdict = ($finalLabel === 'fake') ? 'fake' : 'valid';
        $statusTeks = ($verdict === 'fake') ? 'HOAX' : 'FAKTA';
        $summaryText = "Hasil analisis menemukan indikasi " . $statusTeks . " dengan tingkat keyakinan " . $confidencePercentage . "%.";

        $sources = [];
        if (!empty($aiApiResponse['urls'])) {
            foreach ($aiApiResponse['urls'] as $url) {
                // 🔥 PERBAIKAN: Langsung kirim string URL-nya, hapus bentuk array ['title' => ..., 'url' => ...]
                $sources[] = $url;
            }
        }

        return [
            'success'    => true,
            'verdict'    => $verdict,
            'confidence' => $confidencePercentage,
            'summary'    => $summaryText,
            'sources'    => $sources,
            'raw_data'   => [
                'stage' => 'stage2',
                'request_id' => $requestId,
                'feature_vector' => $featureVector
            ]
        ];
    }

    /**
     * ========================================================
     * 4. FUNGSI BANTUAN: FORMAT DATA SIMILARITY KE FRONTEND
     * ========================================================
     */
    private function formatMatchedResponse($oldRequest, $usedRequestId): array
    {
        $status = $oldRequest->status; // 'stage1' atau 'stage2'
        $verdict = strtolower($oldRequest->final_label) === 'fake' ? 'fake' : 'valid';
        $confidencePercentage = round($oldRequest->final_confidence * 100, 2);

        if ($status === 'stage1') {
            $stage1Result = Stage1Results::where('request_id', $oldRequest->id)->first();
            $knowledge = $stage1Result ? KnowledgeBase::find($stage1Result->knowledge_id) : null;

            $factText = $knowledge ? $knowledge->fact_text : '';

            if (!empty($factText)) {
                $summaryText = ltrim($factText, ', ');
                $sources = [['title' => 'Database Knowledge Base Anti-Hoax', 'url' => '']];
            } else {
                $statusTeks = ($verdict === 'fake') ? 'HOAX' : 'FAKTA';
                $summaryText = "Hasil analisis menemukan indikasi " . $statusTeks . " dengan tingkat keyakinan " . $confidencePercentage . "%.";
                $sources = [];
            }

            return [
                'success'    => true,
                'is_similar' => true,
                'verdict'    => $verdict,
                'confidence' => $confidencePercentage,
                'summary'    => $summaryText,
                'sources'    => $sources,
                'link_counter' => $knowledge ? $knowledge->link_counter : 0,
                'raw_data'   => [
                    'stage' => 'stage1',
                    'request_id' => $usedRequestId,
                    'title' => $knowledge ? $knowledge->title : null,
                    'category' => $knowledge ? $knowledge->category : null,
                ]
            ];
        } else {
            // Format untuk Stage 2
            $stage2Result = Stage2Result::where('request_id', $oldRequest->id)->first();

            $statusTeks = ($verdict === 'fake') ? 'HOAX' : 'FAKTA';
            $summaryText = "Hasil analisis menemukan indikasi " . $statusTeks . " dengan tingkat keyakinan " . $confidencePercentage . "%.";

            $sources = [];
            if ($stage2Result && !empty($stage2Result->url)) {
                $urls = is_string($stage2Result->url) ? json_decode($stage2Result->url, true) : $stage2Result->url;
                if (is_array($urls)) {
                    foreach ($urls as $url) {
                        // 🔥 PERBAIKAN: Langsung kirim string URL-nya juga di sini
                        $sources[] = $url;
                    }
                }
            }

            $featureVector = $stage2Result ? [
                'time_consistency_score' => $stage2Result->time_credibility,
                'message_similarity_score' => $stage2Result->title_credibility,
                'mean_entailment' => $stage2Result->mean_entailment,
                'mean_contradiction' => $stage2Result->mean_contradiction,
                'std_contradiction' => $stage2Result->std_contradiction,
            ] : [];

            return [
                'success'    => true,
                'is_similar' => true,
                'verdict'    => $verdict,
                'confidence' => $confidencePercentage,
                'summary'    => $summaryText,
                'sources'    => $sources,
                'raw_data'   => [
                    'stage' => 'stage2',
                    'request_id' => $usedRequestId,
                    'feature_vector' => $featureVector
                ]
            ];
        }
    }
}
