<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jadwal extends Model
{
    use HasFactory;

    protected $fillable = [
        'tutor_id',
        'kelas_id',
        'mata_pelajaran',
        'hari',
        'jam_mulai',
        'jam_selesai',
    ];

    public function tutor()
    {
        return $this->belongsTo(Tutor::class);
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }
}
