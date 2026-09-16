<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WaNotificationLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'absensi_id',
        'nomor_tujuan',
        'pesan',
        'status',
        'response',
    ];

    public function absensi()
    {
        return $this->belongsTo(Absensi::class);
    }
}
