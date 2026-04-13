<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User; 

class WhatsAppController extends Controller
{
    public function linkWhatsApp(Request $request)
    {
        $request->validate([
            'wa_number' => 'required|numeric'
        ]);
        
        $currentUser = Auth::user();
        $waNumber = $request->wa_number;

        $existingUser = User::where('phone_number', $waNumber)->first();

        if ($existingUser && $existingUser->id !== $currentUser->id) {
            
            $existingUser->delete(); 
        }

        $currentUser->phone_number = $waNumber;
        $currentUser->save();

        return back()->with('success', 'Nomor WhatsApp berhasil disambungkan!');
    }
}