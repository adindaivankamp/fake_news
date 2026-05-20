<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use App\Models\Users;
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
            $googleUser = Socialite::driver('google')
            ->setHttpClient(new \GuzzleHttp\Client(['verify' => false]))
            ->user();

            // Cek apakah email dari Google udah ada di database kita?
            $user = Users::where('email', $googleUser->getEmail())->first();

            if (!$user) {
                // Kalo belum ada, kita buat user baru
                $user = Users::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'password' => Hash::make(Str::random(24)),
                    'role' => 'user'
                ]);
            }
            // Langsung login in usernya
            Auth::login($user);

            if ($user->role === 'user') {
                return redirect('pencarian');
            }

            return redirect('/dashboard');

        } catch (\Exception $e) {
            dd([
    'message' => $e->getMessage(),
    'file' => $e->getFile(),
    'line' => $e->getLine(),
    'trace' => $e->getTraceAsString(),
]);
            return redirect('/')->with('error', 'Gagal login pakai Google!');
        }
    }
}