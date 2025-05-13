<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, \Illuminate\Notifications\Notifiable; // Tambahkan HasApiTokens

    protected $table = 'users';
    protected $fillable = ['name', 'email', 'password', 'peran'];

    public function siswa()
    {
        return $this->hasOne(Siswa::class);
    }

    public function transaksi()
    {
        return $this->hasMany(Transaksi::class, 'petugas_id');
    }
}