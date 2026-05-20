<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Users;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $search = request('search');
        $usersFromDb = Users::when($search, function ($query) use ($search) {
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('phone_number', 'like', "%{$search}%");
        })
        ->paginate(10)
        ->appends(['search' => $search]);

        $usersFromDb->through(function ($user) {
            return [
                'nama' => $user->name,
                'email' => $user->email,
                'whatsapp' => $user->phone_number
            ];
        });

        return view('admin.user', [
            'users' => $usersFromDb,]);
    }

    /**
     * Update user profile
     */
    public function updateProfile(Request $request)
    {
        /** @var Users|null $user */
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

            $registeredPhone = Users::where(
                'phone_number',
                $validated['phone_number']
            )->first();

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

    /**
     * Return JSON data dummy buat admin
     */
    public function getUserData()
    {
        $usersFromDb = Users::all();

        $userData = $usersFromDb->map(function ($user) {
            return [
                'nama' => $user->name,
                'email' => $user->email,
                'whatsapp' => $user->phone_number
            ];
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Data pengguna berhasil dimuat.',
            'data' => $userData
        ]);
    }
}