<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    public function detectHoax(Request $request): JsonResponse
    {
        return response()->json([
            'status' => 'ok',
            'message' => 'Endpoint detect-hoax siap digunakan.',
            'input' => $request->all(),
        ]);
    }
}
