<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\history_view;
use App\Models\UserInteractions;
use Illuminate\Support\Facades\Response;
use Carbon\Carbon;

class RiwayatController extends Controller
{
    public function index(Request $request)
    {
         Carbon::setLocale('id');

         $search = request('search');

        $histories = history_view::with('request')
            ->when($search, function ($query) use ($search) {
                $query->where('username', 'like', "%{$search}%")
                    ->orWhere('input_text', 'like', "%{$search}%");
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->appends(['search' => $search]);

            $data = $histories->through(function ($history) {
            
            $isImageSearch = $history->request && $history->request->image_id != null;
            $isHoax = strtolower($history->final_label) === 'hoax';
            $confidence = ($history->final_confidence ?? 0) * 100;
            
            $persenHoax = $isHoax ? $confidence : (100 - $confidence);
            $persenBenar = $isHoax ? (100 - $confidence) : $confidence;

            return [
                'judul'      => $isImageSearch ? '[GAMBAR] Pencarian oleh: ' . $history->username : '[TEKS] Pencarian oleh: ' . $history->username,
                'penjelasan' => $isHoax ? "Hasil verifikasi menunjukkan bahwa sebagian besar informasi ini, yakni sekitar " . round($persenHoax) . "%, mengandung unsur hoaks atau ketidaksesuaian fakta. Mohon untuk memvalidasi kembali sumber informasi sebelum menyebarkannya." : "Hasil verifikasi menunjukkan bahwa informasi ini memiliki tingkat kebenaran sekitar " . round($persenBenar) . "% dan termasuk informasi yang valid berdasarkan hasil analisis sistem.",
                'user'       => $history->username,
                'date'       => $history->created_at ? Carbon::parse($history->created_at)->translatedFormat('l, j F Y') : '-',
                'deskripsi'  => $isImageSearch ? null : $history->input_text,
                'gambar'     => $isImageSearch && $history->request->image ? $history->request->image->file_path : null,
                'hoax'       => round($persenHoax),
                'benar'      => round($persenBenar),
            ];
            
        }); 
        return view('admin.riwayat', compact('data'));
    }

    public function filterRiwayat(Request $request)
    {
        // 1. Tangkap input dari frontend
        $bulan = $request->input('bulan');
        $tahun = $request->input('tahun');
        $label = $request->input('label'); 

        // 2. Mulai bikin query filter ke history_view
        $query = history_view::query();

        if (!empty($bulan)) {
            $query->whereMonth('created_at', $bulan);
        }

        if (!empty($tahun)) {
            $query->whereYear('created_at', $tahun);
        }

        if (!empty($label)) {
            $dbLabel = strtolower($label) === 'fake' ? 'hoax' : 'valid'; 
            $query->where('final_label', $dbLabel);
        }

        // 3. Eksekusi pencarian
        $histories = $query->get();

        // 4. Olah datanya 
        $data = $histories->map(function ($history) {
            $userCount = UserInteractions::where('request_id', $history->request_id)->count();

            return [
                'input_text' => $history->input_text,
                'user_count' => $userCount,
                'status'     => $history->status,
            ];
        });

        // 5. Kembalikan berupa JSON
        return response()->json([
            'status' => 'success',
            'data'   => $data
        ]);
    }
}