<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Jadwal;
use App\Models\LokasiSekolah;
use App\Models\HariLibur;
use App\Models\Peserta;
use App\Models\Tutor;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class AbsensiController extends Controller
{

    public function index()
    {
        $user = Auth::user();
        $peserta = null;

        $peserta = Peserta::with('kelas')->where('user_id', $user->id)->first();


        if (!$peserta) {
            return redirect()->route('dashboard')->with('error', 'Data peserta tidak ditemukan.');
        }

        // Cek Hari Libur
        $hariLibur = HariLibur::whereDate('tanggal', date('Y-m-d'))->first();
        if ($hariLibur) {
            // Cek apakah sudah digenerate absensi libur untuk hari ini
            $cekLiburGenerated = Absensi::whereDate('tanggal', date('Y-m-d'))
                ->where('status', 'Libur')
                ->exists();

            if (!$cekLiburGenerated) {
                // Generate absensi libur untuk semua peserta
                $semuaPeserta = Peserta::all();
                $dataAbsensi = [];
                $now = now();
                foreach ($semuaPeserta as $p) {
                    $sudahAda = Absensi::where('peserta_id', $p->id)
                        ->whereDate('tanggal', date('Y-m-d'))
                        ->exists();
                    if (!$sudahAda) {
                        $dataAbsensi[] = [
                            'peserta_id' => $p->id,
                            'tanggal' => date('Y-m-d'),
                            'jam_masuk' => null,
                            // 'jam_keluar' => null,
                            'status' => 'hadir',
                            'keterangan' => 'hadir ' . $hariLibur->keterangan,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ];
                    }
                }
                if (count($dataAbsensi) > 0) {
                    Absensi::insert($dataAbsensi);
                }
            }

            $absenHariIni = Absensi::where('peserta_id', $peserta->id)
                ->whereDate('tanggal', date('Y-m-d'))
                ->first();

            $lokasiSekolah = LokasiSekolah::first();
            return view('absensi.index', compact('peserta', 'lokasiSekolah', 'absenHariIni', 'hariLibur'));
        }

        $lokasiSekolah = LokasiSekolah::first();
        if (!$lokasiSekolah) {
            return redirect()->route('dashboard')->with('error', 'Koordinat Lokasi Sekolah belum diatur oleh Admin.');
        }

        $absenHariIni = Absensi::where('peserta_id', $peserta->id)
            ->whereDate('tanggal', date('Y-m-d'))
            ->first();

        $hariLibur = null;
        return view('absensi.index', compact('peserta', 'lokasiSekolah', 'absenHariIni', 'hariLibur'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $peserta = Peserta::where('user_id', $user->id)->first();

        if (!$peserta) {
            return response()->json(['success' => false, 'message' => 'Anda bukan peserta'], 403);
        }

        $lokasiSekolah = LokasiSekolah::first();
        if (!$lokasiSekolah) {
            return response()->json(['success' => false, 'message' => 'Lokasi sekolah belum diatur'], 500);
        }

        // Cek apakah sudah absen hari ini
        $cekAbsen = Absensi::where('peserta_id', $peserta->id)
            ->whereDate('tanggal', date('Y-m-d'))
            ->exists();

        if ($cekAbsen) {
            return response()->json(['success' => false, 'message' => 'Anda sudah melakukan absensi hari ini.'], 400);
        }

        $lat = $request->latitude;
        $lng = $request->longitude;
        $scannedQr = $request->qrcode;

        if (!$lat || !$lng) {
            return response()->json(['success' => false, 'message' => 'Lokasi Anda tidak terdeteksi.'], 400);
        }

        if (!$scannedQr) {
            return response()->json(['success' => false, 'message' => 'QR Code wajib dipindai.'], 400);
        }

        $pesertaQr = str_replace('.svg', '', $peserta->qrcode);

        if ($scannedQr !== $pesertaQr) {
            return response()->json(['success' => false, 'message' => 'QR Code tidak valid atau bukan milik Anda!'], 400);
        }

        // Haversine formula backend validation
        $earthRadius = 6371000; // in meters
        $latFrom = deg2rad($lokasiSekolah->latitude);
        $lonFrom = deg2rad($lokasiSekolah->longitude);
        $latTo = deg2rad($lat);
        $lonTo = deg2rad($lng);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
            cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
        $distance = $angle * $earthRadius;

        if ($distance > $lokasiSekolah->radius) {
            return response()->json([
                'success' => false,
                'message' => 'Anda berada di luar jangkauan lokasi sekolah (' . round($distance) . ' meter).'
            ], 400);
        }

        Absensi::create([
            'peserta_id' => $peserta->id,
            'tanggal' => date('Y-m-d'),
            'jam_masuk' => date('H:i:s'),
            'status' => 'Hadir',
            'keterangan' => 'Hadir',
            'latitude' => $lat,
            'longitude' => $lng,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Absensi berhasil tersimpan.'
        ]);
    }


    public function riwayatKehadiran()
    {
        $user = Auth::user();
        $peserta = null;



        $peserta = \App\Models\Peserta::where('user_id', $user->id)->first();


        if (!$peserta) {
            return redirect()->route('dashboard')->with('error', 'Data peserta tidak ditemukan.');
        }

        // 2. Ambil Riwayat Keseluruhan
        $riwayat = Absensi::where('peserta_id', $peserta->id)
            ->orderBy('tanggal', 'desc')
            ->get();

        // 3. Statistik Bulanan
        $bulanIni = date('m');
        $tahunIni = date('Y');

        // Helper untuk query status
        $baseQuery = Absensi::where('peserta_id', $peserta->id)
            ->whereMonth('tanggal', $bulanIni)
            ->whereYear('tanggal', $tahunIni);

        $hadirCount = (clone $baseQuery)->whereRaw('LOWER(status) = ?', ['hadir'])->count();
        $izinCount  = (clone $baseQuery)->whereRaw('LOWER(status) = ?', ['izin'])->count();
        $sakitCount = (clone $baseQuery)->whereRaw('LOWER(status) = ?', ['sakit'])->count();
        $alpaCount  = (clone $baseQuery)->whereRaw('LOWER(status) = ?', ['alfa'])->count();

        // 4. Perhitungan Hari Efektif (Menghindari Alpa manual yang tidak terinput)
        $startDate = \Carbon\Carbon::now()->startOfMonth();
        $endDate = \Carbon\Carbon::now();
        $hariEfektif = 0;

        $currentDate = $startDate->copy();
        while ($currentDate->lte($endDate)) {
            // Asumsi hari minggu libur
            if ($currentDate->dayOfWeek !== \Carbon\Carbon::SUNDAY) {
                $hariEfektif++;
            }
            $currentDate->addDay();
        }

        $totalTercatat = $hadirCount + $izinCount + $sakitCount + $alpaCount;
        $calculatedAlpa = max(0, $hariEfektif - $totalTercatat);

        $rekap = [
            'hadir' => $hadirCount,
            'izin'  => $izinCount,
            'sakit' => $sakitCount,
            'alpa'  => $alpaCount + $calculatedAlpa,
        ];

        return view('absensi.riwayat-kehadiran', compact('riwayat', 'rekap', 'peserta'));
    }

    public function absensiHarian()
    {
        $user = Auth::user();
        if ($user->role !== 'tutor') {
            return redirect()->route('dashboard')->with('error', 'Hanya tutor yang dapat mengakses halaman ini.');
        }

        $tutor = Tutor::where('user_id', $user->id)->first();
        if (!$tutor) {
            return redirect()->route('dashboard')->with('error', 'Data tutor tidak ditemukan.');
        }

        // Bahasa Indonesia Days Mapping
        $days = [
            'Sunday' => 'Minggu',
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu'
        ];
        $hariIniStr = $days[date('l')];

        // Cari jadwal mengajar tutor ini HARI INI
        $jadwalHariIni = Jadwal::where('tutor_id', $tutor->id)
            ->where('hari', $hariIniStr)
            ->with('kelas')
            ->get();

        $kelasIds = $jadwalHariIni->pluck('kelas_id')->unique();

        // Ambil semua peserta di kelas tersebut berserta absensi HARI INI
        $pesertas = Peserta::whereIn('kelas_id', $kelasIds)
            ->with(['kelas', 'absensis' => function ($q) {
                $q->whereDate('tanggal', date('Y-m-d'));
            }])
            ->orderBy('nama')
            ->paginate(10);

        return view('absensi.harian', compact('tutor', 'jadwalHariIni', 'pesertas', 'hariIniStr'));
    }

    public function updateHarian(Request $request)
    {
        $request->validate([
            'peserta_id' => 'required|exists:pesertas,id',
            'status' => 'required|in:Hadir,Izin,Sakit,Alfa',
            'keterangan' => 'nullable|string'
        ]);

        $tanggalInfo = date('Y-m-d');

        $absensi = Absensi::where('peserta_id', $request->peserta_id)
            ->whereDate('tanggal', $tanggalInfo)
            ->first();

        if ($absensi) {
            $absensi->update([
                'status' => $request->status,
                'keterangan' => $request->keterangan ?? 'Diperbarui otomatis oleh tutor'
            ]);
        } else {
            Absensi::create([
                'peserta_id' => $request->peserta_id,
                'tanggal' => $tanggalInfo,
                'jam_masuk' => date('H:i:s'),
                'status' => $request->status,
                'keterangan' => $request->keterangan ?? 'Ditambahkan otomatis oleh tutor',
            ]);
        }

        return redirect()->back()->with('success', 'Status absensi berhasil diperbarui.');
    }

    public function rekapAbsensi(Request $request)
    {
        $bulan = $request->bulan ?? date('m');
        $tahun = $request->tahun ?? date('Y');

        $pesertas = Peserta::with('kelas')
            ->orderByDesc(
                Absensi::selectRaw('MAX(tanggal)')
                    ->whereColumn('peserta_id', 'pesertas.id')
                    ->whereMonth('tanggal', $bulan)
                    ->whereYear('tanggal', $tahun)
            )
            ->orderBy('nama')
            ->get();

        // Hitung hari efektif (kecuali Minggu) di bulan tersebut sampai hari ini atau akhir bulan
        $startDate = \Carbon\Carbon::createFromDate($tahun, $bulan, 1)->startOfMonth();
        $endDate = ($bulan == date('m') && $tahun == date('Y'))
            ? \Carbon\Carbon::now()
            : \Carbon\Carbon::createFromDate($tahun, $bulan, 1)->endOfMonth();

        $hariEfektif = 0;
        $currentDate = $startDate->copy();
        while ($currentDate->lte($endDate)) {
            if ($currentDate->dayOfWeek !== \Carbon\Carbon::SUNDAY) {
                $hariEfektif++;
            }
            $currentDate->addDay();
        }

        $rekapAll = [];
        foreach ($pesertas as $s) {
            $hadir = Absensi::where('peserta_id', $s->id)
                ->whereMonth('tanggal', $bulan)->whereYear('tanggal', $tahun)
                ->whereRaw('LOWER(status) = ?', ['hadir'])->count();
            $izin = Absensi::where('peserta_id', $s->id)
                ->whereMonth('tanggal', $bulan)->whereYear('tanggal', $tahun)
                ->whereRaw('LOWER(status) = ?', ['izin'])->count();
            $sakit = Absensi::where('peserta_id', $s->id)
                ->whereMonth('tanggal', $bulan)->whereYear('tanggal', $tahun)
                ->whereRaw('LOWER(status) = ?', ['sakit'])->count();
            $alfaDb = Absensi::where('peserta_id', $s->id)
                ->whereMonth('tanggal', $bulan)->whereYear('tanggal', $tahun)
                ->whereRaw('LOWER(status) = ?', ['alfa'])->count();

            $totalTercatat = $hadir + $izin + $sakit + $alfaDb;
            $alpa = $alfaDb + max(0, $hariEfektif - $totalTercatat);

            $rekapAll[] = [
                'peserta' => $s,
                'hadir' => $hadir,
                'izin' => $izin,
                'sakit' => $sakit,
                'alpa' => $alpa,
            ];
        }

        // Paginate array
        $page = $request->get('page', 1);
        $perPage = 10;
        $totalPeserta = count($rekapAll);
        $rekap = new LengthAwarePaginator(
            array_slice($rekapAll, ($page - 1) * $perPage, $perPage),
            $totalPeserta,
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $namaBulan = \Carbon\Carbon::createFromDate($tahun, $bulan, 1)->isoFormat('MMMM Y');

        return view('absensi.rekap-absensi', compact('rekap', 'bulan', 'tahun', 'namaBulan', 'hariEfektif', 'totalPeserta'));
    }

    public function rekapPerKelas(Request $request)
    {
        $bulan = $request->bulan ?? date('m');
        $tahun = $request->tahun ?? date('Y');

        $kelasList = \App\Models\Kelas::withCount('pesertas')->orderBy('nama_kelas')->get();

        // Buat daftar tanggal efektif (kecuali Minggu)
        $startDate = \Carbon\Carbon::createFromDate($tahun, $bulan, 1)->startOfMonth();
        $endDate = ($bulan == date('m') && $tahun == date('Y'))
            ? \Carbon\Carbon::now()
            : \Carbon\Carbon::createFromDate($tahun, $bulan, 1)->endOfMonth();

        $tanggalList = [];
        $currentDate = $startDate->copy();
        while ($currentDate->lte($endDate)) {
            if ($currentDate->dayOfWeek !== \Carbon\Carbon::SUNDAY) {
                $tanggalList[] = $currentDate->format('Y-m-d');
            }
            $currentDate->addDay();
        }

        // Urutkan tanggal terbaru di atas
        $tanggalList = array_reverse($tanggalList);

        // Build rekap: tiap tanggal -> tiap kelas -> count status
        $rekapAll = [];
        foreach ($tanggalList as $tgl) {
            foreach ($kelasList as $kelas) {
                $pesertaIds = Peserta::where('kelas_id', $kelas->id)->pluck('id');
                $totalPeserta = $pesertaIds->count();

                $hadir = Absensi::whereIn('peserta_id', $pesertaIds)
                    ->whereDate('tanggal', $tgl)
                    ->whereRaw('LOWER(status) = ?', ['hadir'])->count();
                $izin = Absensi::whereIn('peserta_id', $pesertaIds)
                    ->whereDate('tanggal', $tgl)
                    ->whereRaw('LOWER(status) = ?', ['izin'])->count();
                $sakit = Absensi::whereIn('peserta_id', $pesertaIds)
                    ->whereDate('tanggal', $tgl)
                    ->whereRaw('LOWER(status) = ?', ['sakit'])->count();
                $alfaDb = Absensi::whereIn('peserta_id', $pesertaIds)
                    ->whereDate('tanggal', $tgl)
                    ->whereRaw('LOWER(status) = ?', ['alfa'])->count();

                $tercatat = $hadir + $izin + $sakit + $alfaDb;
                $alpa = $alfaDb + max(0, $totalPeserta - $tercatat);

                $rekapAll[] = [
                    'tanggal' => $tgl,
                    'kelas' => $kelas->nama_kelas,
                    'total_peserta' => $totalPeserta,
                    'hadir' => $hadir,
                    'izin' => $izin,
                    'sakit' => $sakit,
                    'alpa' => $alpa,
                ];
            }
        }

        // Paginate array
        $page = $request->get('page', 1);
        $perPage = 10;
        $totalBaris = count($rekapAll);
        $rekap = new LengthAwarePaginator(
            array_slice($rekapAll, ($page - 1) * $perPage, $perPage),
            $totalBaris,
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $namaBulan = \Carbon\Carbon::createFromDate($tahun, $bulan, 1)->isoFormat('MMMM Y');

        return view('absensi.rekap-kelas', compact('rekap', 'bulan', 'tahun', 'namaBulan', 'totalBaris'));
    }
}
