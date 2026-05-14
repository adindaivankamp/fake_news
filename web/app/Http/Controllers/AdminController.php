<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

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

            $category = strtolower($row->final_label) === 'fake'
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

        return view('admin.dashboard', compact('dashboardPopular'));
    }
}