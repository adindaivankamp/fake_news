<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ImageDetectionController;
use App\Http\Controllers\TextDetectionController; // Tambahkan import ini

class PencarianController extends Controller
{
    /**
     * Tampilkan halaman pencarian.
     */
    public function index()
    {
        return view('user.pencarian');
    }

    /**
     * Proses permintaan penelusuran teks.
     */
    public function telusuri(Request $request): JsonResponse
    {
        try {
            // 1. Validasi input dari frontend
            $validated = $request->validate([
                'informasi' => 'required|string|min:10|max:5000',
            ]);

            $informasi = trim($validated['informasi']);

            // 2. Ubah nama field 'informasi' menjadi 'query' agar cocok dengan TextDetectionController
            $request->merge([
                'query' => $informasi
            ]);

            // 3. Delegasikan dan kembalikan langsung hasilnya
            // Karena output TextDetectionController sudah berformat sempurna untuk Frontend
            $textDetectionController = new TextDetectionController();
            $detect = $textDetectionController->detectText($request);

            return $detect;
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
                'debug_error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Proses permintaan penelusuran gambar.
     */
    public function telusuriGambar(Request $request): JsonResponse
    {
        Log::info($request->all());
        $request->merge([
            'gambar' => $request->file('gambar')
        ]);
        Log::info('Received image search request', [
            'user_id' => Auth::id(),
            'has_image' => $request->hasFile('gambar')
        ]);
        $imageDetectionController = new ImageDetectionController();
        return $imageDetectionController->detect($request);
    }
}
