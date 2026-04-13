<?php

use Illuminate\Support\Facades\Route;
// 1. TAMBAHIN INI DI ATAS:
use App\Http\Controllers\WhatsAppController; 

// ... (route lain yang mungkin udah ada)

Route::get('/', function () {
    return view('welcome');
});

// 2. MASUKIN ROUTE LU DI DALAM GRUP AUTH INI:
Route::middleware(['auth'])->group(function () {
    
    // ... (mungkin ada route dashboard lu di sini)
    
    // Ini dia route baru lu mek:
    Route::post('/link-wa', [WhatsAppController::class, 'linkWhatsApp'])->name('wa.link');

});