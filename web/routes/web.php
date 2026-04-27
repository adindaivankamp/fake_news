<?php

use Illuminate\Support\Facades\Route;
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

Route::get('/', function () {
    return view('landing_page.landing');
})->name('landing');

// Login
Route::get('/masuk', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/masuk', [LoginController::class, 'login'])->name('login.post');
Route::post('/keluar', [LoginController::class, 'logout'])->name('logout');

Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

// Pencarian 
Route::get('/pencarian', [PencarianController::class, 'index'])->name('beranda');
Route::post('/telusuri', [PencarianController::class, 'telusuri'])->name('telusuri');
Route::post('/telusuri-gambar', [PencarianController::class, 'telusuriGambar'])->name('telusuri.gambar');
Route::get('/dapatkan-whatsapp', function () {
    return view('whatsapp');
})->name('whatsapp.page');

Route::get('/admin/dashboard', [AdminController::class, 'dashboard']);
Route::get('/admin/user', [UserController::class, 'index']);
Route::get('/admin/umpanbalik', [UmpanBalikController::class, 'index']);

// RIWAYAT
Route::get('/admin/riwayat', [RiwayatController::class, 'index']);
Route::get('/admin/riwayat/edit/{id}', [RiwayatController::class, 'edit']);
Route::post('/admin/riwayat/update/{id}', [RiwayatController::class, 'update']);
Route::get('/admin/riwayat/delete/{id}', [RiwayatController::class, 'delete']);
// Route untuk Webhook dari WhatsApp (Di luar middleware auth karena diakses oleh sistem/API)

// GROUP ADMIN
Route::prefix('admin')->group(function () {

    // DASHBOARD
    Route::get('/dashboard', [AdminController::class, 'dashboard']);

    // RIWAYAT
    Route::get('/riwayat', [RiwayatController::class, 'index']);
    Route::get('/riwayat/edit/{id}', [RiwayatController::class, 'edit']);
    Route::post('/riwayat/update/{id}', [RiwayatController::class, 'update']);
    Route::get('/riwayat/delete/{id}', [RiwayatController::class, 'delete']);
});

Route::any('/wa-webhook', [WaController::class, 'webhook']);

//login menggunakan wa di web
Route::get('/login-wa', [AuthController::class, 'showPhoneForm']);
Route::post('/login-wa/request', [AuthController::class, 'requestToken']);
Route::get('/login-wa/verify', [AuthController::class, 'showTokenForm']);
Route::post('/login-wa/verify', [AuthController::class, 'verifyToken']);

//uji coba deteksi hoax
Route::get('/uji-coba-deteksi', function () {
    return view('uji-coba-deteksi');
});
Route::post('/api/detect-text', [HoaxDetectionController::class, 'detectText']);


// Route khusus untuk user yang sudah login di Web
Route::middleware(['auth'])->group(function () {

    // ... (taruh route dashboard lu di sini nanti kalau ada)

    // Route buat nyambungin WA
    Route::post('/link-wa', [WaController::class, 'linkWhatsApp'])->name('wa.link');
});

// Route untuk Google Auth
Route::get('/auth/google/redirect', [GoogleAuthController::class, 'redirect'])->name('google.redirect');
Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])->name('google.callback');