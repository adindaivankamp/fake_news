<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\history_view;
use App\Models\Requests; // Tabel utamanya
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;


class HistoryManagementController extends Controller
{
    /**
     * 1. TAMPILKAN SEMUA DATA (Hanya yang belum di-soft delete)
     */
    public function index()
    {
        $histories = history_view::whereHas('request', function($query) {
            $query->whereNull('deleted_at');
        })->orderBy('created_at', 'desc')->get();

        return response()->json([
            'status' => 'success',
            'data' => $histories
        ]);
    }

    /**
     * 2. TAMPILKAN DATA SAMPAH (Yang sudah di-soft delete)
     */
    public function trash()
    {
        $trashedHistories = history_view::whereHas('request', function($query) {
            $query->onlyTrashed();
        })->orderBy('created_at', 'desc')->get();

        return response()->json([
            'status' => 'success',
            'data' => $trashedHistories
        ]);
    }

    /**
     * 3. SOFT DELETE (Sembunyikan dari history utama)
     */
    public function softDelete($requestId)
    {
        Log::info("Attempting to soft delete request with ID: $requestId");
        Log::info(Requests::find($requestId));
        $request = Requests::find($requestId);
        
        
        if (!$request) {
            return response()->json(['status' => 'error', 'message' => 'Data tidak ditemukan'], 404);
        }

        $request->delete(); 

        return response()->json(['status' => 'success', 'message' => 'Data berhasil dipindah ke tempat sampah mek!']);
    }

    /**
     * 4. RESTORE (Kembalikan data yang di-soft delete)
     */
    public function restore($requestId)
    {
        // Harus pake withTrashed() buat nyari data yang udah di-soft delete
        $request = Requests::withTrashed()->find($requestId);

        if (!$request) {
            return response()->json(['status' => 'error', 'message' => 'Data tidak ditemukan'], 404);
        }

        $request->restore(); 

        return response()->json(['status' => 'success', 'message' => 'Data berhasil dikembalikan ke history mek!']);
    }

    /**
     * 5. HARD DELETE (Hapus permanen ke akar-akarnya)
     */
    public function hardDelete($requestId)
    {
        $request = Requests::withTrashed()->find($requestId);

        if (!$request) {
            return response()->json(['status' => 'error', 'message' => 'Data tidak ditemukan'], 404);
        }

        DB::beginTransaction();
        try {
            // Hapus relasi dulu biar nggak nyangkut (Kalau di DB nggak pake Cascade)
            // $request->imageSearchResults()->delete();
            // $request->stage1Results()->delete();
            
            // Hapus permanen data utamanya
            $request->forceDelete(); 

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Data berhasil dimusnahkan permanen mek!']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => 'Gagal hapus permanen: ' . $e->getMessage()], 500);
        }
    }
}