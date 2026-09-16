<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Jadwal extends Model
{
    use HasFactory;

    protected $fillable = [
        'tutor_id',
        'kelas_id',
        'mata_pelajaran',
        'hari',
        'tanggal',
        'ruangan',
        'jam_mulai',
        'jam_selesai',
        'session_token',
        'session_date',
    ];

    protected $casts = [
        'session_date' => 'date',
    ];

    public function tutor()
    {
        return $this->belongsTo(Tutor::class);
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    public function absensis()
    {
        return $this->hasMany(Absensi::class);
    }

    /**
     * Get peserta yang terdaftar di kelas jadwal ini (via relasi kelas)
     */
    public function pesertas()
    {
        return $this->hasManyThrough(Peserta::class, Kelas::class, 'id', 'kelas_id', 'kelas_id', 'id');
    }

    /**
     * Generate session token harian unik untuk QR Code
     * Token berubah setiap hari sehingga QR kemarin tidak bisa dipakai
     */
    public function generateSessionToken(): string
    {
        $today = now()->toDateString();

        // Jika token sudah ada untuk hari ini, return yang ada
        if ($this->session_token && $this->session_date && $this->session_date->toDateString() === $today) {
            return $this->session_token;
        }

        // Generate token baru
        $token = Str::random(32);
        $this->update([
            'session_token' => $token,
            'session_date' => $today,
        ]);

        return $token;
    }

    /**
     * Get konten QR Code yang berisi jadwal_id + token (format JSON encoded)
     */
    public function getQrContent(): string
    {
        $token = $this->generateSessionToken();

        return json_encode([
            'jadwal_id' => $this->id,
            'token' => $token,
            'date' => now()->toDateString(),
        ]);
    }

    /**
     * Validasi apakah token yang diberikan valid untuk jadwal ini hari ini
     */
    public function isTokenValid(string $token): bool
    {
        return $this->session_token === $token
            && $this->session_date
            && $this->session_date->toDateString() === now()->toDateString();
    }

    /**
     * Cek apakah peserta terdaftar di kelas jadwal ini
     */
    public function isPesertaRegistered(Peserta $peserta): bool
    {
        return $peserta->kelas_id === $this->kelas_id;
    }

    /**
     * Check apakah ada konflik jadwal
     * Konflik terjadi jika:
     * - Tutor yang SAMA mengajar di jam dan hari yang sama (tidak peduli kelas)
     * ATAU
     * - Kelas yang SAMA memiliki 2 pelajaran di jam yang sama
     * 
     * @param string $hari
     * @param string $jam_mulai
     * @param string $jam_selesai
     * @param int $kelas_id
     * @param int $tutor_id
     * @param int|null $exclude_id - ID jadwal yang diexclude (untuk update)
     * @return bool
     */
    public static function hasConflict($hari, $jam_mulai, $jam_selesai, $kelas_id, $tutor_id = null, $exclude_id = null)
    {
        // Base query condition untuk check jam yang tumpang tindih
        $jamCondition = function ($q) use ($jam_mulai, $jam_selesai) {
            $q->whereRaw("? < jam_selesai AND ? > jam_mulai", [$jam_mulai, $jam_mulai])
                ->orWhereRaw("? < jam_selesai AND ? > jam_mulai", [$jam_selesai, $jam_selesai])
                ->orWhereRaw("? <= jam_mulai AND ? >= jam_selesai", [$jam_mulai, $jam_selesai]);
        };

        // Exclude jadwal yang sedang diedit
        $excludeCondition = function ($q) use ($exclude_id) {
            if ($exclude_id) {
                $q->where('id', '!=', $exclude_id);
            }
        };

        // CONFLICT 1: Check jika ada tutor yang sama mengajar di jam/hari yang sama (kelas berbeda)
        if ($tutor_id) {
            $tutorConflict = self::where('tutor_id', $tutor_id)
                ->where('hari', $hari)
                ->where(function ($q) use ($jamCondition) {
                    $jamCondition($q);
                })
                ->where(function ($q) use ($excludeCondition) {
                    $excludeCondition($q);
                })
                ->exists();

            if ($tutorConflict) {
                return true;
            }
        }

        // CONFLICT 2: Check jika kelas yang sama punya jadwal yang tumpang tindih pada hari yang sama
        $kelasConflict = self::where('kelas_id', $kelas_id)
            ->where('hari', $hari)
            ->where(function ($q) use ($jamCondition) {
                $jamCondition($q);
            })
            ->where(function ($q) use ($excludeCondition) {
                $excludeCondition($q);
            })
            ->exists();

        return $kelasConflict;
    }

    /**
     * Get konflik jadwal detail untuk display ke user
     */
    public static function getConflictDetail($hari, $jam_mulai, $jam_selesai, $kelas_id, $tutor_id = null, $exclude_id = null)
    {
        // Base condition untuk jam yang tumpang tindih
        $jamCondition = function ($q) use ($jam_mulai, $jam_selesai) {
            $q->whereRaw("? < jam_selesai AND ? > jam_mulai", [$jam_mulai, $jam_mulai])
                ->orWhereRaw("? < jam_selesai AND ? > jam_mulai", [$jam_selesai, $jam_selesai])
                ->orWhereRaw("? <= jam_mulai AND ? >= jam_selesai", [$jam_mulai, $jam_selesai]);
        };

        // Exclude condition
        $excludeCondition = function ($q) use ($exclude_id) {
            if ($exclude_id) {
                $q->where('id', '!=', $exclude_id);
            }
        };

        // Check conflict dari tutor dulu
        if ($tutor_id) {
            $tutorConflicts = self::where('tutor_id', $tutor_id)
                ->where('hari', $hari)
                ->with(['tutor', 'kelas'])
                ->where(function ($q) use ($jamCondition) {
                    $jamCondition($q);
                })
                ->where(function ($q) use ($excludeCondition) {
                    $excludeCondition($q);
                })
                ->get();

            if ($tutorConflicts->count() > 0) {
                return $tutorConflicts;
            }
        }

        // Jika tidak ada conflict tutor, check conflict kelas
        return self::where('kelas_id', $kelas_id)
            ->where('hari', $hari)
            ->with(['tutor', 'kelas'])
            ->where(function ($q) use ($jamCondition) {
                $jamCondition($q);
            })
            ->where(function ($q) use ($excludeCondition) {
                $excludeCondition($q);
            })
            ->get();
    }
}
