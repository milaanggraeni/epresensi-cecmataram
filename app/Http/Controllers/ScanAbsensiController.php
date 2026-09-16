<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Jadwal;
use App\Models\Peserta;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ScanAbsensiController extends Controller
{
    public function scanPage()
    {
        $user = Auth::user();
        if ($user->role !== 'peserta') {
            return redirect()->route('dashboard')->with('error', 'Hanya peserta yang dapat mengakses halaman ini.');
        }

        return view('absensi.scan-absensi');
    }

    public function processScan(Request $request)
    {
        $user = Auth::user();

        if ($user->role !== 'peserta') {
            return response()->json(['success' => false, 'message' => 'Hanya peserta yang dapat melakukan absensi.'], 403);
        }

        $peserta = Peserta::where('user_id', $user->id)->first();
        if (!$peserta) {
            return response()->json(['success' => false, 'message' => 'Data peserta tidak ditemukan.'], 404);
        }

        $qrData = $request->input('qr_data');
        
        if (!$qrData) {
            return response()->json(['success' => false, 'message' => 'QR Code tidak valid.'], 400);
        }

        // Parse QR Data (JSON expected)
        $data = json_decode($qrData, true);
        
        if (!$data || !isset($data['jadwal_id']) || !isset($data['token'])) {
            return response()->json(['success' => false, 'message' => 'Format QR Code tidak dikenali.'], 400);
        }

        $jadwal = Jadwal::with('kelas')->find($data['jadwal_id']);

        if (!$jadwal) {
            return response()->json(['success' => false, 'message' => 'Jadwal tidak ditemukan.'], 404);
        }

        // 1. Validasi Token Sesi
        if (!$jadwal->isTokenValid($data['token'])) {
            return response()->json(['success' => false, 'message' => 'Sesi QR Code sudah tidak berlaku (Expired). Silakan minta tutor untuk refresh QR.'], 400);
        }

        // 2. Validasi Apakah Peserta Terdaftar di Kelas Jadwal Tersebut
        if (!$jadwal->isPesertaRegistered($peserta)) {
            return response()->json([
                'success' => false, 
                'message' => 'Akses Ditolak! Anda (' . $peserta->kelas->nama_kelas . ') tidak terdaftar di kelas ini (' . $jadwal->kelas->nama_kelas . ').'
            ], 403);
        }

        // 3. Cek Apakah Sudah Absen Hari Ini di Jadwal Ini
        $sudahAbsen = Absensi::where('peserta_id', $peserta->id)
            ->where('jadwal_id', $jadwal->id)
            ->whereDate('tanggal', date('Y-m-d'))
            ->exists();

        if ($sudahAbsen) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah melakukan absensi untuk sesi ini hari ini.'
            ], 400);
        }

        // 4. Simpan Absensi
        $absensi = Absensi::create([
            'peserta_id' => $peserta->id,
            'jadwal_id' => $jadwal->id,
            'tanggal' => date('Y-m-d'),
            'jam_masuk' => date('H:i:s'),
            'status' => 'hadir',
            'keterangan' => 'Hadir via scan QR kelas',
        ]);

        // 5. Kirim Notifikasi WA ke Wali
        $waService = new WhatsAppService();
        $waService->sendAbsensiNotification($absensi);

        return response()->json([
            'success' => true,
            'message' => 'Absensi berhasil tercatat!',
            'data' => [
                'nama' => $peserta->nama,
                'kelas' => $jadwal->kelas->nama_kelas,
                'mata_pelajaran' => $jadwal->mata_pelajaran,
                'jam' => date('H:i', strtotime($absensi->jam_masuk))
            ]
        ]);
    }
}
