<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Kelas;
use App\Models\Peserta;
use App\Models\Tutor;
use App\Models\Jadwal;
use App\Exports\LaporanKehadiranPesertaExport;
use App\Exports\LaporanKehadiranTutorExport;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use PDF;

class LaporanKehadiranController extends Controller
{
    /**
     * Tampilkan halaman utama Laporan Kehadiran
     */
    public function index()
    {
        $kelasList = Kelas::orderBy('nama_kelas')->get();
        $bulan = date('m');
        $tahun = date('Y');

        return view('absensi.laporan-kehadiran', compact('kelasList', 'bulan', 'tahun'));
    }

    /**
     * Endpoint laporan kehadiran peserta (form submit)
     */
    public function peserta(Request $request)
    {
        $kelasId = $request->kelas_id;
        $bulan = $request->bulan ?? date('m');
        $tahun = $request->tahun ?? date('Y');

        $kelasList = Kelas::orderBy('nama_kelas')->get();

        // Ambil peserta berdasarkan kelas (jika dipilih)
        $pesertaQuery = Peserta::with('kelas');
        if ($kelasId) {
            $pesertaQuery->where('kelas_id', $kelasId);
        }
        $pesertas = $pesertaQuery->orderBy('nama')->get();

        // Hitung hari efektif (kecuali Minggu)
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
                if ($currentDate->dayOfWeek !== Carbon::SUNDAY) {
                    $hariEfektif++;
                    $tanggalList[] = $currentDate->format('Y-m-d');
                }
                $currentDate->addDay();
            }
        }

        // Build rekap per peserta
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
            $izin  = $absensiData->where('status', 'Izin')->count() + $absensiData->where('status', 'izin')->count();
            $sakit = $absensiData->where('status', 'Sakit')->count() + $absensiData->where('status', 'sakit')->count();
            $alfaDb = $absensiData->where('status', 'Alfa')->count() + $absensiData->where('status', 'alfa')->count();

            $totalTercatat = $hadir + $izin + $sakit + $alfaDb;
            $alpa = $alfaDb + max(0, $hariEfektif - $totalTercatat);

            // Detail per tanggal
            $detail = [];
            foreach ($tanggalList as $tgl) {
                $record = $absensiData->get($tgl);
                $detail[] = [
                    'tanggal' => $tgl,
                    'tanggal_format' => Carbon::parse($tgl)->isoFormat('D MMM'),
                    'hari' => Carbon::parse($tgl)->isoFormat('ddd'),
                    'status' => $record ? ucfirst(strtolower($record->status)) : 'Alpa',
                    'jam_masuk' => $record && $record->jam_masuk ? date('H:i', strtotime($record->jam_masuk)) : '-',
                    'keterangan' => $record ? ($record->keterangan ?? '-') : '-',
                ];
            }

                $rekapPeserta[] = [
                    'peserta' => $p,
                    'hadir' => $hadir,
                    'izin' => $izin,
                    'sakit' => $sakit,
                    'alpa' => $alpa,
                    'detail' => $detail,
                ];
            }
        }

        // Summary totals
        $totalHadir = collect($rekapPeserta)->sum('hadir');
        $totalIzin  = collect($rekapPeserta)->sum('izin');
        $totalSakit = collect($rekapPeserta)->sum('sakit');
        $totalAlpa  = collect($rekapPeserta)->sum('alpa');

        $namaBulan = Carbon::createFromDate($tahun, $bulan, 1)->isoFormat('MMMM Y');
        $selectedKelas = $kelasId ? Kelas::find($kelasId) : null;

        return view('absensi.laporan-kehadiran', compact(
            'kelasList', 'bulan', 'tahun', 'rekapPeserta',
            'hariEfektif', 'namaBulan', 'selectedKelas',
            'totalHadir', 'totalIzin', 'totalSakit', 'totalAlpa',
            'tanggalList'
        ))->with('activeTab', 'peserta');
    }

    /**
     * Endpoint laporan kehadiran tutor (form submit)
     */
    public function tutor(Request $request)
    {
        $namaTutor = $request->nama_tutor;
        $bulan = $request->bulan ?? date('m');
        $tahun = $request->tahun ?? date('Y');

        $kelasList = Kelas::orderBy('nama_kelas')->get();

        // Query tutor
        $tutorQuery = Tutor::with('jadwals.kelas');
        if ($namaTutor) {
            $tutorQuery->where('nama', 'like', '%' . $namaTutor . '%');
        }
        $tutors = $tutorQuery->orderBy('nama')->get();

        // Mapping hari Indonesia
        $dayMap = [
            'Senin' => 1, 'Selasa' => 2, 'Rabu' => 3,
            'Kamis' => 4, 'Jumat' => 5, 'Sabtu' => 6, 'Minggu' => 0,
        ];

        // Build rekap per tutor
        $rekapTutor = [];
        foreach ($tutors as $t) {
            $jadwalGrouped = $t->jadwals->groupBy('hari');

            // Hitung total jadwal per minggu
            $totalJadwalPerMinggu = $t->jadwals->count();

            // Hitung jumlah minggu di bulan ini
            $systemStartDate = Carbon::create(2026, 6, 1)->startOfDay();
            $requestedMonthStart = Carbon::createFromDate($tahun, $bulan, 1)->startOfMonth();
            $requestedMonthEnd = Carbon::createFromDate($tahun, $bulan, 1)->endOfMonth();
            $now = Carbon::now()->endOfDay();

            $startDate = $requestedMonthStart->copy()->max($systemStartDate);
            $endDate = $requestedMonthEnd->copy()->min($now);

            // Hitung total hari mengajar dalam bulan (berdasarkan jadwal)
            $totalHariMengajar = 0;
            $detailJadwal = [];

            if ($startDate->lte($endDate)) {
                foreach ($jadwalGrouped as $hari => $jadwals) {
                $hariNum = $dayMap[$hari] ?? null;
                if ($hariNum === null) continue;

                // Hitung berapa kali hari ini muncul dalam bulan
                $currentDate = $startDate->copy();
                $count = 0;
                while ($currentDate->lte($endDate)) {
                    if ($currentDate->dayOfWeek === $hariNum) {
                        $count++;
                    }
                    $currentDate->addDay();
                }
                $totalHariMengajar += $count;

                    foreach ($jadwals as $j) {
                        $detailJadwal[] = [
                            'hari' => $hari,
                            'kelas' => $j->kelas->nama_kelas ?? '-',
                            'mata_pelajaran' => $j->mata_pelajaran,
                            'jam' => date('H:i', strtotime($j->jam_mulai)) . ' - ' . date('H:i', strtotime($j->jam_selesai)),
                            'frekuensi_bulan' => $count . 'x',
                        ];
                    }
                }
            }

            $rekapTutor[] = [
                'tutor' => $t,
                'total_jadwal_per_minggu' => $totalJadwalPerMinggu,
                'total_hari_mengajar' => $totalHariMengajar,
                'detail_jadwal' => $detailJadwal,
            ];
        }

        $namaBulan = Carbon::createFromDate($tahun, $bulan, 1)->isoFormat('MMMM Y');

        return view('absensi.laporan-kehadiran', compact(
            'kelasList', 'bulan', 'tahun', 'rekapTutor',
            'namaBulan', 'namaTutor'
        ))->with('activeTab', 'tutor');
    }

    /**
     * Export laporan kehadiran peserta ke Excel
     */
    public function exportPesertaExcel(Request $request)
    {
        $kelasId = $request->kelas_id;
        $bulan = $request->bulan ?? date('m');
        $tahun = $request->tahun ?? date('Y');

        $namaFile = 'Laporan_Kehadiran_Peserta_' . Carbon::createFromDate($tahun, $bulan, 1)->isoFormat('MMMM_Y') . '.xlsx';

        return Excel::download(
            new LaporanKehadiranPesertaExport($bulan, $tahun, $kelasId),
            $namaFile
        );
    }

    /**
     * Export laporan kehadiran peserta ke PDF
     */
    public function exportPesertaPdf(Request $request)
    {
        $kelasId = $request->kelas_id;
        $bulan = $request->bulan ?? date('m');
        $tahun = $request->tahun ?? date('Y');

        // Ambil data seperti method peserta()
        $pesertaQuery = Peserta::with('kelas');
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
        $tanggalList = [];
        
        if ($startDate->lte($endDate)) {
            $currentDate = $startDate->copy();
            while ($currentDate->lte($endDate)) {
                if ($currentDate->dayOfWeek !== Carbon::SUNDAY) {
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

            $detail = [];
            foreach ($tanggalList as $tgl) {
                $record = $absensiData->get($tgl);
                $detail[] = [
                    'tanggal' => $tgl,
                    'tanggal_format' => Carbon::parse($tgl)->isoFormat('D MMM'),
                    'hari' => Carbon::parse($tgl)->isoFormat('ddd'),
                    'status' => $record ? ucfirst(strtolower($record->status)) : 'Alpa',
                    'jam_masuk' => $record && $record->jam_masuk ? date('H:i', strtotime($record->jam_masuk)) : '-',
                    'keterangan' => $record ? ($record->keterangan ?? '-') : '-',
                ];
            }

                $rekapPeserta[] = [
                    'peserta' => $p,
                    'hadir' => $hadir,
                    'izin' => $izin,
                    'sakit' => $sakit,
                    'alpa' => $alpa,
                    'detail' => $detail,
                ];
            }
        }

        $totalHadir = collect($rekapPeserta)->sum('hadir');
        $totalIzin = collect($rekapPeserta)->sum('izin');
        $totalSakit = collect($rekapPeserta)->sum('sakit');
        $totalAlpa = collect($rekapPeserta)->sum('alpa');

        $namaBulan = Carbon::createFromDate($tahun, $bulan, 1)->isoFormat('MMMM Y');
        $selectedKelas = $kelasId ? Kelas::find($kelasId) : null;

        $data = compact(
            'rekapPeserta',
            'hariEfektif',
            'namaBulan',
            'selectedKelas',
            'totalHadir',
            'totalIzin',
            'totalSakit',
            'totalAlpa',
            'tanggalList'
        );

        $pdf = PDF::loadView('exports.laporan-kehadiran-peserta-pdf', $data);
        $namaFile = 'Laporan_Kehadiran_Peserta_' . Carbon::createFromDate($tahun, $bulan, 1)->isoFormat('MMMM_Y') . '.pdf';

        return $pdf->stream($namaFile);
    }

    /**
     * Export laporan kehadiran tutor ke Excel
     */
    public function exportTutorExcel(Request $request)
    {
        $bulan = $request->bulan ?? date('m');
        $tahun = $request->tahun ?? date('Y');

        $namaFile = 'Laporan_Kehadiran_Tutor_' . Carbon::createFromDate($tahun, $bulan, 1)->isoFormat('MMMM_Y') . '.xlsx';

        return Excel::download(
            new LaporanKehadiranTutorExport($bulan, $tahun),
            $namaFile
        );
    }

    /**
     * Export laporan kehadiran tutor ke PDF
     */
    public function exportTutorPdf(Request $request)
    {
        $namaTutor = $request->nama_tutor;
        $bulan = $request->bulan ?? date('m');
        $tahun = $request->tahun ?? date('Y');

        $tutorQuery = Tutor::with('jadwals.kelas');
        if ($namaTutor) {
            $tutorQuery->where('nama', 'like', '%' . $namaTutor . '%');
        }
        $tutors = $tutorQuery->orderBy('nama')->get();

        $dayMap = [
            'Senin' => 1, 'Selasa' => 2, 'Rabu' => 3,
            'Kamis' => 4, 'Jumat' => 5, 'Sabtu' => 6, 'Minggu' => 0,
        ];

        $rekapTutor = [];
        foreach ($tutors as $t) {
            $jadwalGrouped = $t->jadwals->groupBy('hari');

            $systemStartDate = Carbon::create(2026, 6, 1)->startOfDay();
            $requestedMonthStart = Carbon::createFromDate($tahun, $bulan, 1)->startOfMonth();
            $requestedMonthEnd = Carbon::createFromDate($tahun, $bulan, 1)->endOfMonth();
            $now = Carbon::now()->endOfDay();

            $startDate = $requestedMonthStart->copy()->max($systemStartDate);
            $endDate = $requestedMonthEnd->copy()->min($now);

            $totalHariMengajar = 0;
            $detailJadwal = [];

            if ($startDate->lte($endDate)) {
                foreach ($jadwalGrouped as $hari => $jadwals) {
                $hariNum = $dayMap[$hari] ?? null;
                if ($hariNum === null) continue;

                $currentDate = $startDate->copy();
                $count = 0;
                while ($currentDate->lte($endDate)) {
                    if ($currentDate->dayOfWeek === $hariNum) {
                        $count++;
                    }
                    $currentDate->addDay();
                }
                $totalHariMengajar += $count;

                    foreach ($jadwals as $j) {
                        $detailJadwal[] = [
                            'hari' => $hari,
                            'kelas' => $j->kelas->nama_kelas ?? '-',
                            'mata_pelajaran' => $j->mata_pelajaran,
                            'jam' => date('H:i', strtotime($j->jam_mulai)) . ' - ' . date('H:i', strtotime($j->jam_selesai)),
                            'frekuensi_bulan' => $count . 'x',
                        ];
                    }
                }
            }

            $rekapTutor[] = [
                'tutor' => $t,
                'total_jadwal_per_minggu' => count($jadwalGrouped),
                'total_hari_mengajar' => $totalHariMengajar,
                'detail_jadwal' => $detailJadwal,
            ];
        }

        $namaBulan = Carbon::createFromDate($tahun, $bulan, 1)->isoFormat('MMMM Y');

        $data = compact('rekapTutor', 'namaBulan', 'namaTutor');

        $pdf = PDF::loadView('exports.laporan-kehadiran-tutor-pdf', $data);
        $namaFile = 'Laporan_Kehadiran_Tutor_' . Carbon::createFromDate($tahun, $bulan, 1)->isoFormat('MMMM_Y') . '.pdf';

        return $pdf->stream($namaFile);
    }
}
