<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class GoogleAuthController extends Controller
{
    // Fungsi buat ngelempar user ke halaman Login Google
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    // Fungsi pas Google ngebalikin data usernya ke web kita
    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            // Cek apakah email dari Google udah ada di database kita?
            $user = User::where('email', $googleUser->getEmail())->first();

            if (!$user) {
                // JALAN NINJA: Kalau belum ada, kita buatin akunnya otomatis!
                // Karena DB lu wajibin ada 'password', kita kasih password acak aja yang kuat.
                $user = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'password' => Hash::make(Str::random(24)), // Password dummy biar DB seneng
                ]);
            }

            // Langsung login-in usernya
            Auth::login($user);

            // Arahin ke halaman dashboard (ganti 'dashboard' sama route tujuan lu)
            return redirect('/dashboard');

        } catch (\Exception $e) {
            return redirect('/')->with('error', 'Gagal login pakai Google!');
        }
    }
}