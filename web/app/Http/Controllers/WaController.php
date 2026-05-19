<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\MessageCache;
use App\Models\Users;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class WaController extends Controller
{
    public function webhook(Request $request)
    {
        try {

            $sender = (string) $request->input('sender');
            $message = trim(strtolower($request->input('message')));
            $name = $request->input('name');

            Users::firstOrCreate(
                ['phone_number' => $sender],
                ['name' => $name ?? 'User WA']
            );
            
            // 🔥 2. JIKA BUKAN COMMAND (#)
            if (!str_contains($message, '#')) {

                MessageCache::create([
                    'sender_number' => $sender,
                    'latest_message' => $message
                ]);

                return response()->json(['status' => 'cached']);
            }

            // 🔥 3. COMMAND: #detect
            if (str_starts_with($message, '#detect')) {

                $lastMessage = MessageCache::where('sender_number', $sender)
                    ->where('created_at', '>=', Carbon::now()->subMinutes(5))
                    ->latest() // urut terbaru
                    ->first(); // ambil 1 saja
                if ($lastMessage) {

                    $text = "📩 Pesan sebelumnya:\n\n " . $lastMessage->latest_message;

                    $reply = $text;

                    // 🔥 OPTIONAL: HAPUS SETELAH DIPAKAI
                    MessageCache::where('sender_number', $sender)->delete();
                } else {
                    $reply = "⚠️ Tidak ada pesan dalam 5 menit terakhir.";
                }
            } else {
                $reply = "❓ Command tidak dikenali";
            }

            // 🔥 4. KIRIM KE FONNTE
            Http::timeout(5)->withHeaders([
                'Authorization' => env('FONNTE_TOKEN')
            ])->post('https://api.fonnte.com/send', [
                'target' => $sender,
                'message' => $reply
            ]);

            return response()->json(['status' => 'replied']);
        } catch (\Exception $e) {

            Log::error('ERROR WA', [
                'msg' => $e->getMessage(),
                'line' => $e->getLine()
            ]);

            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function linkWhatsApp(Request $request)
    {
        // 1. Validasi inputan form dari web
        $request->validate([
            'wa_number' => 'required'
        ]);

        /** @var \App\Models\Users $currentUser */
        $currentUser = Auth::user();
        $waNumber = trim($request->wa_number);

        // 2. Cek apakah ada akun bot di DB yang udah pake nomor WA ini
        $existingWaUser = Users::where('phone_number', $waNumber)->first();

        // 3. Kalau nomor belum terdaftar di database, kembalikan error
        if (!$existingWaUser) {
            return back()->with('error', 'Nomor ini belum terdaftar. Silakan cek via WhatsApp pada menu Dapatkan Melalui WhatsApp.');
        }

        // 4. Kalau nomornya ada dan itu bukan akun yang lagi login -> merge akun
        if ($existingWaUser->id !== $currentUser->id) {
            DB::beginTransaction();
            try {
                // A. Pindahkan history ke akun utama (Gmail)
                \App\Models\UserInteractions::where('user_id', $existingWaUser->id)
                    ->update(['user_id' => $currentUser->id]);
                
                \App\Models\Images::where('uploaded_by', $existingWaUser->id)
                    ->update(['uploaded_by' => $currentUser->id]);

                \App\Models\Feedbacks::where('user_id', $existingWaUser->id)
                    ->update(['user_id' => $currentUser->id]);

                // B. Update nomor HP di akun web
                $currentUser->phone_number = $waNumber;
                $currentUser->save();

                // C. Hapus akun WA lama biar nggak duplikat
                $existingWaUser->delete(); 

                DB::commit();

                return back()->with('success', 'Akun WhatsApp berhasil dihubungkan dan riwayat telah digabungkan otomatis.');

            } catch (\Exception $e) {
                DB::rollBack();
                return back()->with('error', 'Gagal menggabungkan akun: ' . $e->getMessage());
            }
        }

        // 5. Kalau kebetulan ID-nya sama (emang udah nyambung)
        return back()->with('success', 'Nomor WhatsApp ini sudah terhubung dengan akun Anda.');
    }
}