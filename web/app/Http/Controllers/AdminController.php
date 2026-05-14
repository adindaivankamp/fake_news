<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\Users; 
use App\Models\Requests; 
use App\Models\history_view;
use App\Models\Feedbacks; 

class AdminController extends Controller
{
    public function dashboard()
    {
        /*
        |--------------------------------------------------------------------------
        | AMBIL DATA PENCARIAN POPULER
        |--------------------------------------------------------------------------
        */
        $histories = DB::table('history_view')
            ->select(
                'input_text',
                'final_label',
                DB::raw('COUNT(*) as count')
            )
            ->whereNotNull('final_label')
            ->groupBy('input_text', 'final_label')
            ->orderByDesc('count')
            ->limit(3)
            ->get();

        $dashboardPopular = [];

        foreach ($histories as $idx => $row) {

            $category = strtolower($row->final_label) === 'fake' || strtolower($row->final_label) === 'hoax'
                ? 'HOAX'
                : 'FAKTA';

            $dashboardPopular[] = [
                'rank' => $idx + 1,
                'badge' => $category,
                'title' => $row->input_text,
                'headline' => Str::limit($row->input_text, 60),
                'count' => $row->count,
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | AMBIL DATA STATISTIK DASHBOARD
        |--------------------------------------------------------------------------
        */
        $now = Carbon::now();
        $lastMonth = Carbon::now()->subMonth();

        // 1. STATISTIK PENGGUNA
        $total_pengguna = Users::count();
        $pengguna_sekarang = Users::whereMonth('created_at', $now->month)
                                  ->whereYear('created_at', $now->year)->count();
        $pengguna_lalu = Users::whereMonth('created_at', $lastMonth->month)
                              ->whereYear('created_at', $lastMonth->year)->count();
        
        // 2. STATISTIK BERITA (Diambil dari tabel requests)
        $total_berita = Requests::count();
        $berita_sekarang = Requests::whereMonth('created_at', $now->month)
                                   ->whereYear('created_at', $now->year)->count();
        $berita_lalu = Requests::whereMonth('created_at', $lastMonth->month)
                               ->whereYear('created_at', $lastMonth->year)->count();

        // 3. STATISTIK HOAX (Cari yang labelnya 'hoax' atau 'fake')
        $total_hoax = history_view::whereIn('final_label', ['hoax', 'fake'])->count();
        $hoax_sekarang = history_view::whereIn('final_label', ['hoax', 'fake'])
                                     ->whereMonth('created_at', $now->month)
                                     ->whereYear('created_at', $now->year)->count();
        $hoax_lalu = history_view::whereIn('final_label', ['hoax', 'fake'])
                                 ->whereMonth('created_at', $lastMonth->month)
                                 ->whereYear('created_at', $lastMonth->year)->count();

        // 4. STATISTIK UMPAN BALIK
        $total_umpan_balik = Feedbacks::count();
        $umpan_balik_sekarang = Feedbacks::whereMonth('created_at', $now->month)
                                         ->whereYear('created_at', $now->year)->count();
        $umpan_balik_lalu = Feedbacks::whereMonth('created_at', $lastMonth->month)
                                     ->whereYear('created_at', $lastMonth->year)->count();

        // 5. GABUNGKAN KE VARIABLE $dashboardStats
        $dashboardStats = [
            'total_pengguna'     => $total_pengguna,
            'persen_pengguna'    => $this->hitungPersentase($pengguna_sekarang, $pengguna_lalu),
            'total_berita'       => $total_berita,
            'persen_berita'      => $this->hitungPersentase($berita_sekarang, $berita_lalu),
            'total_hoax'         => $total_hoax,
            'persen_hoax'        => $this->hitungPersentase($hoax_sekarang, $hoax_lalu),
            'total_umpan_balik'  => $total_umpan_balik,
            'persen_umpan_balik' => $this->hitungPersentase($umpan_balik_sekarang, $umpan_balik_lalu),
            'last_updated'       => Carbon::now()->translatedFormat('d F Y, H:i') . ' WIB',
        ];

        return view('admin.dashboard', compact('dashboardPopular', 'dashboardStats'));
    }

    /**
     * Fungsi Helper untuk menghitung persentase kenaikan/penurunan bulan ini vs bulan lalu
     */
    private function hitungPersentase($bulanIni, $bulanLalu)
    {
        if ($bulanLalu == 0) {
            return $bulanIni > 0 ? 100 : 0;
        }
        
        $persen = (($bulanIni - $bulanLalu) / $bulanLalu) * 100;
        return round($persen, 1); 
    }
}