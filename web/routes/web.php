<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RiwayatController;
use App\Http\Controllers\WaController;
use App\Http\Controllers\GoogleAuthController;

Route::get('/', function () {
    return view('landing_page.landing');
});

// 🔥 GROUP ADMIN
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {

    // DASHBOARD
    Route::get('/dashboard', [AdminController::class, 'dashboard']);

    // RIWAYAT
    Route::get('/riwayat', [RiwayatController::class, 'index']);
    Route::get('/riwayat/edit/{id}', [RiwayatController::class, 'edit']);
    Route::post('/riwayat/update/{id}', [RiwayatController::class, 'update']);
    Route::get('/riwayat/delete/{id}', [RiwayatController::class, 'delete']);
});

Route::any('/wa-webhook', [WaController::class, 'webhook']);

// Route khusus untuk user yang sudah login di Web
Route::middleware(['auth'])->group(function () {
    
    // ... (taruh route dashboard lu di sini nanti kalau ada)
    
    // Route buat nyambungin WA
    Route::post('/link-wa', [WaController::class, 'linkWhatsApp'])->name('wa.link');

});

// Route untuk Google Auth
Route::get('/auth/google/redirect', [GoogleAuthController::class, 'redirect'])->name('google.redirect');
Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])->name('google.callback');