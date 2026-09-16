<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    use HasFactory;

    protected $fillable = [
        'peserta_id',
        'jadwal_id',
        'tanggal',
        'jam_masuk',
        'status',
        'keterangan',
    ];

    public function peserta()
    {
        return $this->belongsTo(Peserta::class);
    }

    public function jadwal()
    {
        return $this->belongsTo(Jadwal::class);
    }
}
