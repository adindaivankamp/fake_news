<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

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
     */
    public function telusuriGambar(Request $request): JsonResponse
    {
        try {
            // Validasi file
            $validated = $request->validate([
                'gambar' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120', // Max 5MB
            ]);

            $file = $validated['gambar'];

            // Simpan file sementara
            $path = $file->store('temp-uploads', 'public');

            Log::info('Image search submitted', [
                'user_id' => Auth::id(),
                'filename' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
            ]);

            // TODO: Integrasikan dengan image detection API
            // Contoh:
            // $result = Http::post(env('AI_API_URL') . '/image-check', [
            //     'image' => base64_encode(file_get_contents(storage_path('app/public/' . $path))),
            //     'user_id' => auth()->id(),
            // ]);

            return response()->json([
                'success'    => true,
                'verdict'    => 'unclear',
                'confidence' => 38,
                'summary'    => 'Gambar sedang dianalisis menggunakan teknologi deteksi konten. '
                              . 'Sistem memeriksa keaslian, manipulasi, dan konteks gambar.',
                'sources'    => [
                    ['title' => 'Google Reverse Image Search', 'url' => 'https://images.google.com'],
                ],
                'image_path' => $path, // Untuk debugging, hapus di production
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'File tidak valid. Gunakan format JPEG, PNG, atau GIF (max 5MB).',
                'errors'  => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Image search error', [
                'user_id' => Auth::id(),
                'error'   => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengupload gambar.',
            ], 500);
        }
    }
}
