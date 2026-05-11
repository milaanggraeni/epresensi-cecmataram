<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_kelas',
        'jumlah_peserta',
        'wali_kelas',
    ];

    public function pesertas()
    {
        return $this->hasMany(Peserta::class);
    }

    public function jadwals()
    {
        return $this->hasMany(Jadwal::class);
    }
}
