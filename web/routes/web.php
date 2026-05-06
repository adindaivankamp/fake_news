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
use App\Http\Controllers\ApiController;
use App\Http\Controllers\ImageDetectionController;

/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES
|--------------------------------------------------------------------------
*/

// Landing Page
Route::get('/', function () {
    return view('landing_page.landing');
})->name('landing');

// Pencarian
Route::get('/pencarian', [PencarianController::class, 'index'])->name('beranda');
Route::post('/telusuri', [PencarianController::class, 'telusuri'])->name('telusuri');
Route::post('/telusuri-gambar', [PencarianController::class, 'telusuriGambar'])->name('telusuri.gambar');
// Pencarian terpopuler (halaman statis)
Route::get('/pencarian/populer', function () {
    return view('user.pencarian-terpopuler');
})->name('pencarian.populer');

// WhatsApp Page
Route::get('/dapatkan-whatsapp', function () {
    return view('whatsapp');
})->name('whatsapp.page');

// Uji coba deteksi
Route::get('/uji-coba-deteksi', function () {
    return view('uji-coba-deteksi');
});
Route::post('/api/detect-text', [HoaxDetectionController::class, 'detectText'])
    ->name('detect.text');
Route::post('/api/detect-image', [ImageDetectionController::class, 'detect'])->name('detect.image');

/*
|--------------------------------------------------------------------------
| AUTH ROUTES
|--------------------------------------------------------------------------
*/

// Login Web
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

    Route::get('/pencarian', [PencarianController::class, 'index'])->name('beranda');
    Route::get('/pencarian-terpopuler', function () {
        return view('user.pencarian-terpopuler');
    })->name('pencarian.populer');
    Route::post('/telusuri', [PencarianController::class, 'telusuri'])->name('telusuri');
    Route::post('/telusuri-gambar', [PencarianController::class, 'telusuriGambar'])->name('telusuri.gambar');

/*
|--------------------------------------------------------------------------
| ADMIN ROUTES (PROTECTED)
|--------------------------------------------------------------------------
*/

// Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {

    // Dashboard
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    // User Management
    Route::get('/user', [UserController::class, 'index']);
    Route::get('/user-data', [UserController::class, 'getUserData']);

    // Umpan Balik
    Route::get('/umpanbalik', [UmpanBalikController::class, 'index']);
    Route::get('/umpanbalik-data', [UmpanBalikController::class, 'getFeedbackData']);

    // Riwayat
    Route::prefix('riwayat')->group(function () {
        Route::get('/', [RiwayatController::class, 'index']);
        Route::get('/edit/{id}', [RiwayatController::class, 'edit']);
        Route::post('/update/{id}', [RiwayatController::class, 'update']);
        Route::get('/delete/{id}', [RiwayatController::class, 'delete']);
    });

// });


/*
|--------------------------------------------------------------------------
| WHATSAPP WEBHOOK & API (NO AUTH)
|--------------------------------------------------------------------------
*/

Route::any('/wa-webhook', [WaController::class, 'webhook']);
Route::post('/detect-hoax', [ApiController::class, 'detectHoax']);

// Login using WhatsApp (web)
Route::get('/login-wa', [AuthController::class, 'showPhoneForm'])->name('login.wa');
Route::post('/login-wa/request', [AuthController::class, 'requestToken'])->name('login.wa.request');
Route::get('/login-wa/verify', [AuthController::class, 'showTokenForm'])->name('login.wa.verify');
Route::post('/login-wa/verify', [AuthController::class, 'verifyToken'])->name('login.wa.verify.post');


/*
|--------------------------------------------------------------------------
| AUTHENTICATED USER ROUTES
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    // Update Profile
    Route::post('/profile/update', [UserController::class, 'updateProfile'])->name('profile.update');

    // Link WhatsApp
    Route::post('/link-wa', [WaController::class, 'linkWhatsApp'])->name('wa.link');
});
