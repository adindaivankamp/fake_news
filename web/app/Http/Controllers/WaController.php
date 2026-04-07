<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class WaController extends Controller
{
    public function webhook(Request $request)
    {
        // Ambil data dari Fonnte
        $sender = $request->input('sender');
        $message = $request->input('message');

        // DEBUG (biar yakin masuk)
        \Log::info('WA MASUK', [
            'sender' => $sender,
            'message' => $message
        ]);
        

        // 🔥 DUMMY BALASAN (NO LOGIC)
        $reply = "Halo, pesan kamu sudah masuk ke sistem kami ✅";

        // Kirim balik ke WA
        Http::withHeaders([
            'Authorization' => env('FONNTE_TOKEN')
        ])->post('https://api.fonnte.com/send', [
            'target' => $sender,
            'message' => $reply
        ]);

        return response()->json(['status' => 'ok']);
    }
}