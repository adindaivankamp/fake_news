<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WaController;

Route::get('/', function () {
    return view('welcome');
});

// Route untuk Webhook dari WhatsApp (Di luar middleware auth karena diakses oleh sistem/API)
Route::any('/wa-webhook', [WaController::class, 'webhook']);

// Route khusus untuk user yang sudah login di Web
Route::middleware(['auth'])->group(function () {
    
    // ... (taruh route dashboard lu di sini nanti kalau ada)
    
    // Route buat nyambungin WA
    Route::post('/link-wa', [WaController::class, 'linkWhatsApp'])->name('wa.link');

});
