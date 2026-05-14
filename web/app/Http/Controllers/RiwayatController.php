<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\history_view;
use Carbon\Carbon;

class RiwayatController extends Controller
{
    public function index()
    {
         Carbon::setLocale('id');

        $histories = history_view::with('request.image')->orderBy('created_at', 'desc')->get();
        $data = $histories->map(function ($history) {
            
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
            
        })->toArray(); 
        return view('admin.riwayat', compact('data'));
    }
}