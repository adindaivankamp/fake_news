<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // 🔥 HALAMAN INPUT NOMOR
    public function showPhoneForm()
    {
        return redirect()->route('login.wa.verify');
    }

    // 🔥 REQUEST TOKEN
    public function requestToken(Request $request)
    {
        $request->validate([
            'phone_number' => 'required'
        ]);

        $user = User::where('phone_number', $request->phone_number)->first();

        if (!$user) {
            return back()->with('error', 'Nomor ini belum terdaftar. Silakan cek via WhatsApp pada menu Dapatkan Melalui WhatsApp.');
        }

        $token = rand(100000, 999999);

        $user->login_token = $token;
        $user->token_expired_at = now()->addMinutes(5);
        $user->save();

        // kirim ke WA
        Http::withHeaders([
            'Authorization' => env('FONNTE_TOKEN')
        ])->post('https://api.fonnte.com/send', [
            'target' => $user->phone_number,
            'message' => "Kode login kamu: $token"
        ]);

        // 🔥 SIMPAN NOMOR KE SESSION
        session(['phone_number' => $user->phone_number]);

        // 🔥 REDIRECT KE HALAMAN TOKEN
        return redirect('/login-wa/verify')->with('success', 'Token dikirim ke WhatsApp');
    }

    // 🔥 HALAMAN INPUT TOKEN
    public function showTokenForm()
    {
        return view('auth.login-token');
    }

    // 🔥 VERIFY TOKEN
    public function verifyToken(Request $request)
    {
        $request->validate([
            'token' => 'required'
        ]);

        $phone = session('phone_number');

        $user = User::where('phone_number', $phone)
            ->where('login_token', $request->token)
            ->first();

        if (!$user) {
            return back()->with('error', 'Token salah');
        }

        if (now()->gt($user->token_expired_at)) {
            return back()->with('error', 'Token expired');
        }

        Auth::login($user);

        // hapus token
        $user->login_token = null;
        $user->token_expired_at = null;
        $user->save();

        session()->forget('phone_number');

        return redirect('/')->with('success', 'Login berhasil');
    }
}
