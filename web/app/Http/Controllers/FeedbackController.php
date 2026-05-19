<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Feedbacks;

class FeedbackController extends Controller
{
    public function store(Request $request)
    {
        Log::info('Menerima umpan balik', [
            'user_id' => Auth::id(),
            'request_id' => $request->input('request_id'),
            'feedback' => $request->input('feedback')
        ]);
        $validated = $request->validate([
            'feedback'   => 'required|string|max:2000',
            'request_id' => 'required|integer', // Tangkap ID-nya
        ]);

        try {
            $userId = Auth::check() ? Auth::id() : 2;

            Feedbacks::create([
                'user_id'    => $userId,
                'feedback'   => $validated['feedback'],
                'request_id' => $validated['request_id'] // Simpan ke database
            ]);
            // ... (lanjutannya sama)

            // 4. Kirim respons sukses agar tulisan di layar berubah
            return response()->json([
                'success' => true,
                'message' => 'Umpan balik berhasil disimpan'
            ]);
        } catch (\Exception $e) {
            // Jika gagal (misal database error), catat di log
            Log::error('Gagal menyimpan feedback: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim umpan balik, terjadi kesalahan di server.'
            ], 500);
        }
    }
}
