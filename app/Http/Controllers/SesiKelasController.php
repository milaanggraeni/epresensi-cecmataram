<?php

namespace App\Http\Controllers;

use App\Models\Jadwal;
use App\Models\Tutor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class SesiKelasController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if ($user->role !== 'tutor') {
            return redirect()->route('dashboard')->with('error', 'Akses ditolak.');
        }

        $tutor = Tutor::where('user_id', $user->id)->first();
        if (!$tutor) {
            return redirect()->route('dashboard')->with('error', 'Data tutor tidak ditemukan.');
        }

        $hariIni = \Carbon\Carbon::now()->locale('id')->dayName;

        $jadwalHariIni = Jadwal::with(['kelas', 'absensis' => function($q) {
                $q->whereDate('tanggal', date('Y-m-d'));
            }])
            ->where('tutor_id', $tutor->id)
            ->where('hari', $hariIni)
            ->orderBy('jam_mulai', 'asc')
            ->get();

        return view('sesi-kelas.index', compact('jadwalHariIni', 'tutor'));
    }

    public function showQr($jadwalId)
    {
        $user = Auth::user();
        if ($user->role !== 'tutor') {
            return redirect()->route('dashboard')->with('error', 'Akses ditolak.');
        }

        $tutor = Tutor::where('user_id', $user->id)->first();
        
        $jadwal = Jadwal::with(['kelas', 'absensis' => function($q) {
                $q->whereDate('tanggal', date('Y-m-d'));
            }])
            ->where('id', $jadwalId)
            ->where('tutor_id', $tutor->id)
            ->firstOrFail();

        // Generate QR Content
        $qrContent = $jadwal->getQrContent();

        return view('sesi-kelas.show-qr', compact('jadwal', 'qrContent'));
    }

    public function refreshToken(Request $request, $jadwalId)
    {
        $user = Auth::user();
        if ($user->role !== 'tutor') {
            return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
        }

        $tutor = Tutor::where('user_id', $user->id)->first();
        
        $jadwal = Jadwal::where('id', $jadwalId)
            ->where('tutor_id', $tutor->id)
            ->firstOrFail();

        // Regenerate token by setting it to null first so generateSessionToken creates a new one
        $jadwal->update(['session_token' => null]);
        $qrContent = $jadwal->getQrContent();

        // Return SVG of QR Code
        $svg = QrCode::format('svg')->size(400)->generate($qrContent);

        return response()->json([
            'success' => true,
            'qr_content' => $qrContent,
            'svg' => (string) $svg
        ]);
    }
    
    public function pesertaHadir($jadwalId)
    {
        $user = Auth::user();
        if ($user->role !== 'tutor') {
            return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
        }

        $tutor = Tutor::where('user_id', $user->id)->first();
        
        $jadwal = Jadwal::with(['absensis' => function($q) {
                $q->whereDate('tanggal', date('Y-m-d'));
            }, 'absensis.peserta'])
            ->where('id', $jadwalId)
            ->where('tutor_id', $tutor->id)
            ->firstOrFail();

        $pesertaList = $jadwal->absensis->map(function($absen) {
            return [
                'nama' => $absen->peserta->nama,
                'jam_masuk' => date('H:i', strtotime($absen->jam_masuk)),
            ];
        });

        return response()->json([
            'success' => true,
            'count' => $jadwal->absensis->count(),
            'total_peserta' => $jadwal->pesertas()->count(),
            'peserta' => $pesertaList
        ]);
    }
}
