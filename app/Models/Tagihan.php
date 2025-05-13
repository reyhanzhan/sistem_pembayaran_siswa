<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tagihan extends Model
{
    protected $table = 'tagihan';
    protected $fillable = [
        'siswa_id',
        'jenis_tagihan',
        'jumlah',
        'periode',
        'lunas',
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }
}