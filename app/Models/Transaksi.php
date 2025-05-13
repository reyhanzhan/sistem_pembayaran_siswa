<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    protected $table = 'transaksi'; // Sesuaikan dengan nama tabel di migration
    protected $fillable = ['siswa_id', 'jumlah', 'tanggal_bayar', 'petugas_id', 'status'];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    public function petugas()
    {
        return $this->belongsTo(User::class, 'petugas_id');
    }

    public function tagihan()
    {
        return $this->belongsTo(Tagihan::class);
    }
}