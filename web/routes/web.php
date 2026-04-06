<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WaController;

Route::get('/', function () {
    return view('welcome');
});

Route::any('/wa-webhook', [WaController::class, 'webhook']);
