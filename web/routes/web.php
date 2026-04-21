<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RiwayatController;
use App\Http\Controllers\UmpanBalikController;
use App\Http\Controllers\WaController;

Route::get('/', function () {
    return view('landing_page.landing');
});

Route::get('/admin/dashboard', [AdminController::class, 'dashboard']);
Route::get('/admin/user', [UserController::class, 'index']);
Route::get('/admin/umpanbalik', [UmpanBalikController::class, 'index']);

// RIWAYAT
Route::get('/admin/riwayat', [RiwayatController::class, 'index']);
Route::get('/admin/riwayat/edit/{id}', [RiwayatController::class, 'edit']);
Route::post('/admin/riwayat/update/{id}', [RiwayatController::class, 'update']);
Route::get('/admin/riwayat/delete/{id}', [RiwayatController::class, 'delete']);
// Route untuk Webhook dari WhatsApp (Di luar middleware auth karena diakses oleh sistem/API)
Route::any('/wa-webhook', [WaController::class, 'webhook']);

// Route khusus untuk user yang sudah login di Web
Route::middleware(['auth'])->group(function () {
    
    // ... (taruh route dashboard lu di sini nanti kalau ada)
    
    // Route buat nyambungin WA
    Route::post('/link-wa', [WaController::class, 'linkWhatsApp'])->name('wa.link');

});
