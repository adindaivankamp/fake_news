<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ImageDetectionController;

class PencarianController extends Controller
{
    /**
     * Tampilkan halaman pencarian.
     * Hanya accessible oleh user yang sudah login.
     */
    public function index()
    {
        return view('user.pencarian');
    }

    /**
     * Proses permintaan penelusuran teks.
     *
     * Endpoint: POST /telusuri
     * Request body JSON: { "informasi": "..." }
     *
     * Response JSON:
     * {
     *   "success": true|false,
     *   "verdict":    "hoax" | "valid" | "unclear",
     *   "confidence": 0-100,
     *   "summary":    "Penjelasan singkat.",
     *   "sources": [
     *     { "title": "...", "url": "..." }
     *   ]
     * }
     */
    public function telusuri(Request $request): JsonResponse
    {
        try {
            // Validasi input
            $validated = $request->validate([
                'informasi' => 'required|string|min:10|max:5000',
            ]);

            $informasi = trim($validated['informasi']);

            // Cek jika input terlalu pendek setelah trim
            if (strlen($informasi) < 10) {
                return response()->json([
                    'success' => false,
                    'message' => 'Informasi minimal harus 10 karakter.',
                ], 422);
            }

            // TODO: Integrasikan dengan AI API (GPT, Gemini, atau layanan internal)
            // Contoh:
            // $result = Http::post(env('AI_API_URL') . '/fact-check', [
            //     'text' => $informasi,
            //     'user_id' => auth()->id(),
            // ]);

            Log::info('Text search submitted', [
                'user_id' => Auth::id(),
                'length' => strlen($informasi),
            ]);

            // Placeholder response — ganti dengan logika nyata
            return response()->json([
                'success'    => true,
                'verdict'    => 'unclear',
                'confidence' => 45,
                'summary'    => ucfirst($informasi) . '... adalah informasi yang memerlukan verifikasi lebih lanjut. '
                              . 'Sistem sedang menganalisis data dari berbagai sumber terpercaya.',
                'sources'    => [
                    ['title' => 'Sumber 1', 'url' => 'https://example.com'],
                    ['title' => 'Sumber 2', 'url' => 'https://example.com'],
                ],
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors'  => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Text search error', [
                'user_id' => Auth::id(),
                'error'   => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan. Silakan coba lagi.',
            ], 500);
        }
    }

    /**
     * Proses permintaan penelusuran gambar.
     *
     * Endpoint: POST /telusuri-gambar
     * Request: multipart/form-data dengan key "gambar"
     * Delegasi ke ImageDetectionController untuk processing sebenarnya
     */
    public function telusuriGambar(Request $request): JsonResponse
    {
        // Rename 'gambar' menjadi 'image' untuk kompatibilitas dengan ImageDetectionController
        $request->merge([
            'image' => $request->file('gambar')
        ]);

        // Delegasi ke ImageDetectionController
        $imageDetectionController = new ImageDetectionController();
        return $imageDetectionController->detect($request);
    }
}
