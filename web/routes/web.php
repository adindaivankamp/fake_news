<?php

use Illuminate\Support\Facades\Route;

// Controllers
use App\Http\Controllers\AdminController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RiwayatController;
use App\Http\Controllers\PencarianController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UmpanBalikController;
use App\Http\Controllers\WaController;
use App\Http\Controllers\Api\HoaxDetectionController;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('landing_page.landing');
})->name('landing');

// Pencarian
Route::get('/pencarian', [PencarianController::class, 'index'])->name('beranda');
Route::post('/telusuri', [PencarianController::class, 'telusuri'])->name('telusuri');
Route::post('/telusuri-gambar', [PencarianController::class, 'telusuriGambar'])->name('telusuri.gambar');

// WhatsApp page
Route::get('/dapatkan-whatsapp', function () {
    return view('whatsapp');
})->name('whatsapp.page');

// Uji coba deteksi
Route::get('/uji-coba-deteksi', function () {
    return view('uji-coba-deteksi');
});
Route::post('/api/detect-text', [HoaxDetectionController::class, 'detectText']);


/*
|--------------------------------------------------------------------------
| AUTH ROUTES
|--------------------------------------------------------------------------
*/

// Login web
Route::get('/masuk', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/masuk', [LoginController::class, 'login'])->name('login.post');
Route::post('/keluar', [LoginController::class, 'logout'])->name('logout');

// Login via WhatsApp
Route::prefix('login-wa')->group(function () {
    Route::get('/', [AuthController::class, 'showPhoneForm']);
    Route::post('/request', [AuthController::class, 'requestToken']);
    Route::get('/verify', [AuthController::class, 'showTokenForm'])->name('login.wa.verify');
    Route::post('/verify', [AuthController::class, 'verifyToken']);
});

// Google Auth
Route::prefix('auth/google')->group(function () {
    Route::get('/redirect', [GoogleAuthController::class, 'redirect'])->name('google.redirect');
    Route::get('/callback', [GoogleAuthController::class, 'callback'])->name('google.callback');
});


/*
|--------------------------------------------------------------------------
| ADMIN ROUTES
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->group(function () {

    // Dashboard
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    // User
    Route::get('/user', [UserController::class, 'index']);

    // Umpan balik
    Route::get('/umpanbalik', [UmpanBalikController::class, 'index']);

    // Riwayat
    Route::prefix('riwayat')->group(function () {
        Route::get('/', [RiwayatController::class, 'index']);
        Route::get('/edit/{id}', [RiwayatController::class, 'edit']);
        Route::post('/update/{id}', [RiwayatController::class, 'update']);
        Route::get('/delete/{id}', [RiwayatController::class, 'delete']);
    });
});


/*
|--------------------------------------------------------------------------
| WHATSAPP WEBHOOK (NO AUTH)
|--------------------------------------------------------------------------
*/

Route::any('/wa-webhook', [WaController::class, 'webhook']);


/*
|--------------------------------------------------------------------------
| AUTHENTICATED USER ROUTES
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    // Profile
    Route::post('/profile/update', [UserController::class, 'updateProfile'])->name('profile.update');

    // Link WhatsApp
    Route::post('/link-wa', [WaController::class, 'linkWhatsApp'])->name('wa.link');
});