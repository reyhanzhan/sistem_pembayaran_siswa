<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaksi;
use App\Models\Siswa;
use App\Models\Tagihan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransaksiController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'siswa_id' => 'required|exists:siswa,id',
            'tagihan_id' => 'required|exists:tagihan,id',
            'jumlah' => 'required|numeric|min:0',
        ]);

        $siswa = Siswa::findOrFail($request->siswa_id);
        $tagihan = Tagihan::findOrFail($request->tagihan_id);

        if ($tagihan->jumlah != $request->jumlah) {
            return response()->json(['message' => 'Jumlah pembayaran tidak sesuai dengan tagihan'], 400);
        }

        if ($tagihan->lunas) {
            return response()->json(['message' => 'Tagihan sudah lunas'], 400);
        }

        DB::beginTransaction();
        try {
            $transaksi = Transaksi::create([
                'siswa_id' => $request->siswa_id,
                'jumlah' => $request->jumlah,
                'tanggal_bayar' => now(),
                'petugas_id' => auth()->id(),
                'status' => 'sukses',
            ]);

            $tagihan->lunas = true;
            $tagihan->save();

            $siswa->total_tagihan -= $request->jumlah;
            $siswa->save();

            DB::commit();

            return response()->json([
                'message' => 'Pembayaran berhasil',
                'transaksi' => $transaksi,
                'sisa_tagihan' => $siswa->total_tagihan,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Gagal mencatat pembayaran: ' . $e->getMessage()], 500);
        }
    }

    public function storeMultiple(Request $request)
    {
        $request->validate([
            'siswa_id' => 'required|exists:siswa,id',
            'tagihan_ids' => 'required|array',
            'tagihan_ids.*' => 'exists:tagihan,id',
            'jumlah' => 'required|numeric|min:0',
        ]);

        $siswa = Siswa::findOrFail($request->siswa_id);
        $tagihanIds = $request->tagihan_ids;
        $totalJumlah = $request->jumlah;

        $totalTagihan = Tagihan::whereIn('id', $tagihanIds)->sum('jumlah');
        if ($totalTagihan != $totalJumlah) {
            return response()->json(['message' => 'Jumlah pembayaran tidak sesuai dengan total tagihan'], 400);
        }

        DB::beginTransaction();
        try {
            $transaksi = Transaksi::create([
                'siswa_id' => $request->siswa_id,
                'jumlah' => $totalJumlah,
                'tanggal_bayar' => now(),
                'petugas_id' => auth()->id(),
                'status' => 'sukses',
            ]);

            Tagihan::whereIn('id', $tagihanIds)->update(['lunas' => true]);

            $siswa->total_tagihan -= $totalJumlah;
            $siswa->save();

            DB::commit();

            return response()->json([
                'message' => 'Pembayaran berhasil',
                'transaksi' => $transaksi,
                'sisa_tagihan' => $siswa->total_tagihan,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Gagal mencatat pembayaran: ' . $e->getMessage()], 500);
        }
    }
}