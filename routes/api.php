<?php

use App\Http\Controllers\Api\SiswaController;
use App\Http\Controllers\Api\TransaksiController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TagihanController;
use App\Http\Controllers\Api\RoleMiddleware;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\KelasController;
use App\Http\Controllers\Api\KelasSiswaController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    // Siswa
    Route::get('/siswa', [SiswaController::class, 'index']);
    Route::get('/siswa/{id}', [SiswaController::class, 'show']);

    // Transaksi
    Route::post('/transaksi', [TransaksiController::class, 'store'])
        ->middleware('role:petugas_koperasi,petugas_bendahara');
    Route::post('transaksi/multiple', [TransaksiController::class, 'storeMultiple']);

    // Tagihan
    Route::get('tagihan/{id}', [TagihanController::class, 'show']);
    Route::get('/tagihan', [TagihanController::class, 'index']);
    Route::post('/tagihan', [TagihanController::class, 'store'])
        ->middleware('role:petugas_koperasi,petugas_bendahara');
    Route::put('/tagihan/{id}', [TagihanController::class, 'update'])
        ->middleware('role:petugas_koperasi,petugas_bendahara');
    Route::delete('/tagihan/{id}', [TagihanController::class, 'destroy'])
        ->middleware('role:petugas_koperasi,petugas_bendahara');
    Route::get('/tagihan/belum-lunas/{siswaId}', [TagihanController::class, 'getTagihanBelumLunas']);
});