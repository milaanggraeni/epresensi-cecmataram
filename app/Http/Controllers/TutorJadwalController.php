<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Jadwal;
use App\Models\Kelas;
use App\Models\Tutor;
use App\Models\Peserta;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class TutorJadwalController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        if ($user->role !== 'tutor') {
            return redirect()->route('dashboard')->with('error', 'Hanya tutor yang dapat mengakses halaman ini.');
        }

        $tutor = Tutor::where('user_id', $user->id)->first();
        if (!$tutor) {
            return redirect()->route('dashboard')->with('error', 'Data tutor tidak ditemukan.');
        }

        $query = Jadwal::with(['kelas', 'tutor'])->where('tutor_id', $tutor->id);

        // Filter
        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal', $request->tanggal);
        }
        if ($request->filled('hari')) {
            $query->where('hari', $request->hari);
        }
        if ($request->filled('mata_pelajaran')) {
            $query->where('mata_pelajaran', $request->mata_pelajaran);
        }
        if ($request->filled('kelas_id')) {
            $query->where('kelas_id', $request->kelas_id);
        }

        // Apply sorting and paginate
        $jadwal = $query->orderBy('tanggal', 'desc')
                        ->orderBy('jam_mulai', 'asc')
                        ->paginate(10)
                        ->appends($request->query());

        // Process status and data
        $now = Carbon::now();
        $todayStr = $now->format('Y-m-d');

        foreach ($jadwal as $j) {
            // Determine Status
            if ($j->tanggal) {
                if ($j->tanggal == $todayStr) {
                    $j->status = 'Hari Ini';
                } elseif ($j->tanggal > $todayStr) {
                    $j->status = 'Akan Datang';
                } else {
                    $j->status = 'Selesai';
                }
            } else {
                // If no specific date, fallback to Akan Datang
                $j->status = 'Akan Datang';
            }

            // Number of participants
            $j->jumlah_peserta = Peserta::where('kelas_id', $j->kelas_id)->count();
        }

        // Stats
        $totalJadwal = Jadwal::where('tutor_id', $tutor->id)->count();
        $jadwalHariIni = Jadwal::where('tutor_id', $tutor->id)->whereDate('tanggal', $todayStr)->count();
        $totalKelas = Jadwal::where('tutor_id', $tutor->id)->distinct('kelas_id')->count('kelas_id');

        // Form filter options
        $kelasOptions = Kelas::whereIn('id', function($q) use ($tutor) {
            $q->select('kelas_id')->from('jadwals')->where('tutor_id', $tutor->id);
        })->get();

        $mapelOptions = Jadwal::where('tutor_id', $tutor->id)->distinct('mata_pelajaran')->pluck('mata_pelajaran');

        return view('tutor.jadwal.index', compact(
            'jadwal', 'totalJadwal', 'jadwalHariIni', 'totalKelas', 'kelasOptions', 'mapelOptions', 'request', 'tutor'
        ));
    }
}
