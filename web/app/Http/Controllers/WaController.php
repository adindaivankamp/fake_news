<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

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

    public function linkWhatsApp(Request $request)
    {
        // 1. Validasi inputan form dari web
        $request->validate([
            'wa_number' => 'required|numeric'
        ]);

        /** @var \App\Models\User $currentUser */
        $currentUser = Auth::user();
        $waNumber = $request->wa_number;

        // 2. Cek apakah ada akun di DB yang udah pake nomor WA ini
        $existingUser = User::where('phone_number', $waNumber)->first();

        // 3. LOGIKA KETUA: Kalau akunnya ada DAN itu bukan akun yang lagi login sekarang -> HAPUS!
        if ($existingUser && $existingUser->id !== $currentUser->id) {
            $existingUser->delete(); 
        }

        // 4. "Kalau ga ada ya create biasa aja" -> Update nomor WA ke akun yang sekarang login
        $currentUser->phone_number = $waNumber;
        $currentUser->save();

        // 5. Kembalikan ke halaman web sebelumnya dengan pesan sukses
        return back()->with('success', 'Nomor WhatsApp berhasil disambungkan!');
    }
}