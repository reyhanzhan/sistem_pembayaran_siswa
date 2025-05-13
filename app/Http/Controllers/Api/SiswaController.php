<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use Illuminate\Http\Request;

class SiswaController extends Controller
{
    public function index()
    {
        $siswa = Siswa::with('user')->get();
        $data = $siswa->map(function ($s) {
            if (!$s->user) {
                $s->user = (object) [
                    'id' => 0,
                    'name' => $s->nama ?? 'Unknown',
                    'email' => 'unknown@example.com',
                    'peran' => 'unknown',
                ];
            }
            return $s;
        })->all();
        return response()->json($data);
    }

    public function show($id)
    {
        $siswa = Siswa::with(['user', 'transaksi.petugas'])->findOrFail($id);
        if (!$siswa->user) {
            $siswa->user = (object) [
                'id' => 0,
                'name' => $siswa->nama ?? 'Unknown',
                'email' => 'unknown@example.com',
                'peran' => 'unknown',
            ];
        }
        $siswa->transaksi->each(function ($transaksi) {
            $transaksi->hari_bayar = \Carbon\Carbon::parse($transaksi->tanggal_bayar)
                ->locale('id')
                ->translatedFormat('l');
        });
        return response()->json($siswa);
    }
}