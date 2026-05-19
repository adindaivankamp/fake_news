<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UmpanBalikController extends Controller
{
    public function index()
    {
        // Total feedback
        $totalFeedback = DB::table('feedbacks')->count();

        // sementara dummy sampai ada status baca
        $belumDibaca = $totalFeedback;

        return view(
            'admin.umpanbalik',
            compact(
                'totalFeedback',
                'belumDibaca'
            )
        );
    }

    public function getFeedbackData()
    {
        Carbon::setLocale('id');

        $feedbacks = DB::table('feedbacks')
            ->join('users', 'feedbacks.user_id', '=', 'users.id')
            ->select(
                'feedbacks.id', 
                'users.name as username', 
                'feedbacks.request_id', 
                'feedbacks.feedback', 
                'feedbacks.created_at'
            )
            ->orderBy('feedbacks.created_at', 'desc')
            ->get()
            ->map(function($item){

                return [
                    'id' => $item->id,
                    'username' => $item->username,
                    'request_id' => $item->request_id,
                    'feedback' => $item->feedback,

                    'date' => Carbon::parse(
                        $item->created_at
                    )->translatedFormat(
                        'l, j F Y • H:i'
                    ),
                ];
            });

        return response()->json([
            'status' => 'success',
            'message' => 'Data umpan balik berhasil dimuat.',
            'data' => $feedbacks
        ]);
    }
}
