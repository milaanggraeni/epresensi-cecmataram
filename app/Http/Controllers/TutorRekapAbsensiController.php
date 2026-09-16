<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tutor;
use App\Models\Jadwal;
use App\Models\Kelas;
use App\Models\Peserta;
use App\Models\Absensi;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use PDF;

class TutorRekapAbsensiController extends Controller
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

        // Kelas yang diampu
        $kelasIds = Jadwal::where('tutor_id', $tutor->id)->distinct('kelas_id')->pluck('kelas_id');
        $kelasList = Kelas::whereIn('id', $kelasIds)->orderBy('nama_kelas')->get();

        $kelasId = $request->kelas_id;
        $bulan = $request->bulan ?? date('m');
        $tahun = $request->tahun ?? date('Y');
        $search = $request->search;

        $pesertaQuery = Peserta::with('kelas')->whereIn('kelas_id', $kelasIds);
        if ($kelasId) {
            $pesertaQuery->where('kelas_id', $kelasId);
        }
        if ($search) {
            $pesertaQuery->where('nama', 'like', '%' . $search . '%');
        }
        $pesertas = $pesertaQuery->orderBy('nama')->get();

        // Hitung hari efektif (Senin - Jumat)
        $systemStartDate = Carbon::create(2026, 6, 1)->startOfDay();
        $requestedMonthStart = Carbon::createFromDate($tahun, $bulan, 1)->startOfMonth();
        $requestedMonthEnd = Carbon::createFromDate($tahun, $bulan, 1)->endOfMonth();
        $now = Carbon::now()->endOfDay();

        $startDate = $requestedMonthStart->copy()->max($systemStartDate);
        $endDate = $requestedMonthEnd->copy()->min($now);

        $hariEfektif = 0;
        $tanggalList = [];
        
        if ($startDate->lte($endDate)) {
            $currentDate = $startDate->copy();
            while ($currentDate->lte($endDate)) {
                // Senin sampai Jumat (1 - 5)
                if ($currentDate->dayOfWeek >= Carbon::MONDAY && $currentDate->dayOfWeek <= Carbon::FRIDAY) {
                    $hariEfektif++;
                    $tanggalList[] = $currentDate->format('Y-m-d');
                }
                $currentDate->addDay();
            }
        }

        $rekapPeserta = [];
        $hasAbsensi = Absensi::whereMonth('tanggal', $bulan)->whereYear('tanggal', $tahun)->exists();
        
        if ($hariEfektif > 0 || $hasAbsensi) {
            foreach ($pesertas as $p) {
                $absensiData = Absensi::where('peserta_id', $p->id)
                    ->whereMonth('tanggal', $bulan)
                    ->whereYear('tanggal', $tahun)
                    ->get()
                    ->keyBy('tanggal');

                $hadir = $absensiData->where('status', 'Hadir')->count() + $absensiData->where('status', 'hadir')->count();
                $izin = $absensiData->where('status', 'Izin')->count() + $absensiData->where('status', 'izin')->count();
                $sakit = $absensiData->where('status', 'Sakit')->count() + $absensiData->where('status', 'sakit')->count();
                $alfaDb = $absensiData->where('status', 'Alfa')->count() + $absensiData->where('status', 'alfa')->count();

                $totalTercatat = $hadir + $izin + $sakit + $alfaDb;
                $alpa = $alfaDb + max(0, $hariEfektif - $totalTercatat);

                $persentase = $hariEfektif > 0 ? round(($hadir / $hariEfektif) * 100) : 0;

                $detail = [];
                foreach ($tanggalList as $tgl) {
                    $record = $absensiData->get($tgl);
                    $detail[] = [
                        'tanggal' => Carbon::parse($tgl)->isoFormat('DD-MM-YYYY'),
                        'hari' => Carbon::parse($tgl)->isoFormat('dddd'),
                        'status' => $record ? ucfirst(strtolower($record->status)) : 'Alpa',
                        'jam_masuk' => $record && $record->jam_masuk ? date('H:i', strtotime($record->jam_masuk)) : '-',
                        'keterangan' => $record ? ($record->keterangan ?? '-') : '-',
                    ];
                }

                // Tambahkan info program dari jadwal tutor ini (ambil jadwal pertama yang cocok dengan kelas peserta)
                $jadwalTutor = Jadwal::where('tutor_id', $tutor->id)->where('kelas_id', $p->kelas_id)->first();
                $program = $jadwalTutor ? $jadwalTutor->mata_pelajaran : '-';

                $rekapPeserta[] = [
                    'peserta' => $p,
                    'program' => $program,
                    'hadir' => $hadir,
                    'izin' => $izin,
                    'sakit' => $sakit,
                    'alpa' => $alpa,
                    'persentase' => $persentase,
                    'detail' => $detail,
                ];
            }
        }

        $totalPeserta = count($rekapPeserta);
        $totalHadir = collect($rekapPeserta)->sum('hadir');
        $totalIzin = collect($rekapPeserta)->sum('izin');
        $totalSakit = collect($rekapPeserta)->sum('sakit');
        $totalAlpa = collect($rekapPeserta)->sum('alpa');
        
        $totalTargetKehadiran = $totalPeserta * $hariEfektif;
        $persentaseKelas = $totalTargetKehadiran > 0 ? round(($totalHadir / $totalTargetKehadiran) * 100) : 0;

        $namaBulan = Carbon::createFromDate($tahun, $bulan, 1)->isoFormat('MMMM Y');
        
        return view('tutor.rekap-absensi.index', compact(
            'kelasList', 'rekapPeserta', 'bulan', 'tahun', 'kelasId', 'search',
            'totalPeserta', 'totalHadir', 'totalIzin', 'totalSakit', 'totalAlpa', 'persentaseKelas',
            'namaBulan'
        ));
    }

    public function exportPdf(Request $request)
    {
        return $this->generatePdf($request, 'download');
    }

    public function streamPdf(Request $request)
    {
        return $this->generatePdf($request, 'stream');
    }

    private function generatePdf(Request $request, $action = 'stream')
    {
        $user = Auth::user();
        if ($user->role !== 'tutor') {
            abort(403);
        }

        $tutor = Tutor::where('user_id', $user->id)->first();
        if (!$tutor) {
            abort(404);
        }

        $kelasIds = Jadwal::where('tutor_id', $tutor->id)->distinct('kelas_id')->pluck('kelas_id');
        
        $kelasId = $request->kelas_id;
        $bulan = $request->bulan ?? date('m');
        $tahun = $request->tahun ?? date('Y');

        $pesertaQuery = Peserta::with('kelas')->whereIn('kelas_id', $kelasIds);
        if ($kelasId) {
            $pesertaQuery->where('kelas_id', $kelasId);
        }
        $pesertas = $pesertaQuery->orderBy('nama')->get();

        $systemStartDate = Carbon::create(2026, 6, 1)->startOfDay();
        $requestedMonthStart = Carbon::createFromDate($tahun, $bulan, 1)->startOfMonth();
        $requestedMonthEnd = Carbon::createFromDate($tahun, $bulan, 1)->endOfMonth();
        $now = Carbon::now()->endOfDay();

        $startDate = $requestedMonthStart->copy()->max($systemStartDate);
        $endDate = $requestedMonthEnd->copy()->min($now);

        $hariEfektif = 0;
        
        if ($startDate->lte($endDate)) {
            $currentDate = $startDate->copy();
            while ($currentDate->lte($endDate)) {
                if ($currentDate->dayOfWeek >= Carbon::MONDAY && $currentDate->dayOfWeek <= Carbon::FRIDAY) {
                    $hariEfektif++;
                }
                $currentDate->addDay();
            }
        }

        $rekapPeserta = [];
        foreach ($pesertas as $p) {
            $absensiData = Absensi::where('peserta_id', $p->id)
                ->whereMonth('tanggal', $bulan)
                ->whereYear('tanggal', $tahun)
                ->get();

            $hadir = $absensiData->where('status', 'Hadir')->count() + $absensiData->where('status', 'hadir')->count();
            $izin = $absensiData->where('status', 'Izin')->count() + $absensiData->where('status', 'izin')->count();
            $sakit = $absensiData->where('status', 'Sakit')->count() + $absensiData->where('status', 'sakit')->count();
            $alfaDb = $absensiData->where('status', 'Alfa')->count() + $absensiData->where('status', 'alfa')->count();

            $totalTercatat = $hadir + $izin + $sakit + $alfaDb;
            $alpa = $alfaDb + max(0, $hariEfektif - $totalTercatat);

            $persentase = $hariEfektif > 0 ? round(($hadir / $hariEfektif) * 100) : 0;

            $rekapPeserta[] = [
                'nama' => $p->nama,
                'kelas' => $p->kelas->nama_kelas ?? '-',
                'hadir' => $hadir,
                'izin' => $izin,
                'sakit' => $sakit,
                'alpa' => $alpa,
                'persentase' => $persentase,
            ];
        }

        $namaBulan = Carbon::createFromDate($tahun, $bulan, 1)->isoFormat('MMMM Y');
        $namaKelas = $kelasId ? Kelas::find($kelasId)->nama_kelas : 'Semua Kelas';

        $data = compact('rekapPeserta', 'namaBulan', 'namaKelas', 'tutor');

        $pdf = PDF::loadView('exports.tutor-rekap-absensi-pdf', $data);
        $namaFile = 'Rekap_Absensi_Tutor_' . str_replace(' ', '_', $tutor->nama) . '_' . Carbon::createFromDate($tahun, $bulan, 1)->isoFormat('MMMM_Y') . '.pdf';

        if ($action === 'download') {
            return $pdf->download($namaFile);
        }
        return $pdf->stream($namaFile);
    }
}
