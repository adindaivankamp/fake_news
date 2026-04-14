<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Riwayat;

class RiwayatController extends Controller
{
    public function index()
    {
        $data = Riwayat::all();
        return view('admin.riwayat.index', compact('data'));
    }

    public function edit($id)
    {
        $data = Riwayat::findOrFail($id);
        return view('admin.riwayat.edit', compact('data'));
    }

    public function update(Request $request, $id)
{
    $request->validate([
        'judul' => 'required',
        'status' => 'required'
    ]);

    $data = Riwayat::findOrFail($id);

    $data->update([
        'judul' => $request->judul,
        'status' => $request->status
    ]);

    return redirect('/admin/riwayat')->with('success', 'Data berhasil diupdate');
}

    public function delete($id)
    {
        Riwayat::findOrFail($id)->delete();
        return redirect('/admin/riwayat');
    }
}