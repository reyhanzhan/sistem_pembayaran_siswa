<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tagihan;
use App\Models\Siswa;
use App\Models\Transaksi;

class TagihanController extends Controller
{
    public function index()
    {
        $tagihan = Tagihan::with('siswa.user')->get();
        return response()->json($tagihan);
    }

    public function store(Request $request)
{
    $request->validate([
        'siswa_id' => 'required|exists:siswa,id',
        'jenis_tagihan' => 'required|string',
        'jumlah' => 'required|numeric|min:0',
        'periode' => 'required|string',
    ]);

    $tagihan = Tagihan::create($request->all());

    $siswa = Siswa::find($request->siswa_id);
    $siswa->total_tagihan += $request->jumlah;
    $siswa->save();

    return response()->json([
        'message' => 'Tagihan berhasil ditambahkan',
        'tagihan' => $tagihan,
    ], 201);
}

    public function update(Request $request, $id)
    {
        $tagihan = Tagihan::findOrFail($id);

        $request->validate([
            'jenis_tagihan' => 'required|string',
            'jumlah' => 'required|numeric|min:0',
            'periode' => 'required|string',
        ]);

        // Kurangi total_tagihan siswa dengan jumlah lama
        $siswa = $tagihan->siswa;
        $siswa->total_tagihan -= $tagihan->jumlah;
        $siswa->save();

        // Update tagihan
        $tagihan->update($request->all());

        // Tambah total_tagihan siswa dengan jumlah baru
        $siswa->total_tagihan += $request->jumlah;
        $siswa->save();

        return response()->json([
            'message' => 'Tagihan berhasil diperbarui',
            'tagihan' => $tagihan,
        ]);
    }

    public function show($id)
    {
        $tagihan = Tagihan::findOrFail($id);
        return response()->json($tagihan);
    }

    public function destroy($id)
    {
        $tagihan = Tagihan::findOrFail($id);

        // Kurangi total_tagihan siswa
        $siswa = $tagihan->siswa;
        $siswa->total_tagihan -= $tagihan->jumlah;
        $siswa->save();

        $tagihan->delete();

        return response()->json([
            'message' => 'Tagihan berhasil dihapus',
        ]);
    }

    public function getTagihanBelumLunas($siswaId)
    {
        $tagihan = Tagihan::where('siswa_id', $siswaId)
            ->where('lunas', false)
            ->get();
        return response()->json($tagihan);
    }
}