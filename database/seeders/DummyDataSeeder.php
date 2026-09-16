<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Tutor;
use App\Models\Peserta;
use App\Models\Kelas;
use App\Models\Jadwal;
use App\Models\Absensi;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DummyDataSeeder extends Seeder
{
    public function run()
    {
        // 1. Create Kelas
        $kelas1 = Kelas::create(['nama_kelas' => '10 MIPA 1', 'jumlah_peserta' => '30', 'wali_kelas' => 'Bpk. Budi']);
        $kelas2 = Kelas::create(['nama_kelas' => '10 IPS 1', 'jumlah_peserta' => '30', 'wali_kelas' => 'Ibu Siti']);

        // 2. Create Tutor
        $userTutor = User::create([
            'name' => 'Tutor Budi',
            'email' => 'tutor@gmail.com',
            'role' => 'tutor',
            'password' => Hash::make('password')
        ]);
        $tutor = Tutor::create([
            'user_id' => $userTutor->id,
            'nama' => 'Budi Santoso',
            'mapel' => 'Matematika',
            'jenis_kelamin' => 'L',
            'alamat' => 'Jl. Pendidikan No 1',
            'nomor_hp' => '08123456789'
        ]);

        // 3. Create Peserta
        $userPeserta = User::create([
            'name' => 'Peserta Aldi',
            'email' => 'peserta@gmail.com',
            'role' => 'peserta',
            'password' => Hash::make('password')
        ]);
        $peserta = Peserta::create([
            'user_id' => $userPeserta->id,
            'nama' => 'Aldi Taher',
            'jenis_kelamin' => 'L',
            'qrcode' => 'QR-ALDI-001',
            'kelas_id' => $kelas1->id,
            'alamat' => 'Jl. Siswa No 2'
        ]);

        // 4. Create Jadwal (Today)
        $hariIni = Carbon::now()->locale('id')->dayName;
        $jadwal = Jadwal::create([
            'tutor_id' => $tutor->id,
            'kelas_id' => $kelas1->id,
            'mata_pelajaran' => 'Matematika',
            'hari' => $hariIni,
            'jam_mulai' => '07:00:00',
            'jam_selesai' => '09:00:00',
            'tanggal' => Carbon::now()->format('Y-m-d'),
            'ruangan' => 'Ruang 1'
        ]);

        // 5. Create Absensi (Today)
        Absensi::create([
            'peserta_id' => $peserta->id,
            'tanggal' => Carbon::now()->format('Y-m-d'),
            'jam_masuk' => Carbon::now()->format('H:i:s'),
            'status' => 'hadir',
            'keterangan' => 'Hadir tepat waktu'
        ]);
    }
}
