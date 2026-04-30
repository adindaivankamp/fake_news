<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
     public function index()
    {
        return view('admin.user');
    }

    /**
     * Update user profile
     */
    public function updateProfile(Request $request)
    {
        /** @var User|null $user */
        $user = Auth::user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak ditemukan'
            ], 401);
        }

        // Validate input
        $validator = Validator::make($request->all(), [
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255|unique:users,email,' . $user->id,
            'phone_number' => 'nullable|string|max:20'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        $validated = $validator->validated();

        if (!empty($validated['phone_number'])) {
            $registeredPhone = User::where('phone_number', $validated['phone_number'])->first();

            if (!$registeredPhone) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nomor ini belum terdaftar. Silakan cek via WhatsApp pada menu Dapatkan Melalui WhatsApp.'
                ], 422);
            }
        }

        // Update user
        $user->name = $validated['name'] ?? $user->name;
        $user->email = $validated['email'] ?? $user->email;
        $user->phone_number = $validated['phone_number'] ?? $user->phone_number;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Profil berhasil diperbarui',
            'user' => $user
        ]);
    }
}

