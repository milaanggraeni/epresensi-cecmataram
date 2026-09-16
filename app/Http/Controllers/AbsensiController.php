<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Jadwal;
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

        $peserta = Peserta::with('kelas')->where('user_id', $user->id)->first();

        if (!$peserta) {
            return redirect()->route('dashboard')->with('error', 'Data peserta tidak ditemukan.');
        }

        // Cek absensi hari ini
        $absenHariIni = Absensi::where('peserta_id', $peserta->id)
            ->whereDate('tanggal', date('Y-m-d'))
            ->first();

        // Riwayat kehadiran bulan ini
        $riwayatBulanIni = Absensi::where('peserta_id', $peserta->id)
            ->whereMonth('tanggal', date('m'))
            ->whereYear('tanggal', date('Y'))
            ->orderBy('tanggal', 'desc')
            ->get();

        // Statistik bulan ini
        $bulanIni = date('m');
        $tahunIni = date('Y');
        $baseQuery = Absensi::where('peserta_id', $peserta->id)
            ->whereMonth('tanggal', $bulanIni)
            ->whereYear('tanggal', $tahunIni);

        $hadirCount = (clone $baseQuery)->whereRaw('LOWER(status) = ?', ['hadir'])->count();
        $izinCount  = (clone $baseQuery)->whereRaw('LOWER(status) = ?', ['izin'])->count();
        $sakitCount = (clone $baseQuery)->whereRaw('LOWER(status) = ?', ['sakit'])->count();
        $alpaCount  = (clone $baseQuery)->whereRaw('LOWER(status) = ?', ['alfa'])->count();

        $rekap = [
            'hadir' => $hadirCount,
            'izin'  => $izinCount,
            'sakit' => $sakitCount,
            'alpa'  => $alpaCount,
        ];

        return view('absensi.index', compact('peserta', 'absenHariIni', 'riwayatBulanIni', 'rekap'));
    }

    /**
     * Store absensi via QR scan (called by tutor)
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        // Hanya tutor yang bisa scan absensi
        if ($user->role !== 'tutor') {
            return response()->json(['success' => false, 'message' => 'Hanya tutor yang dapat melakukan absensi.'], 403);
        }

        $scannedQr = $request->qrcode;
        $jadwalId = $request->jadwal_id;

        if (!$scannedQr) {
            return response()->json(['success' => false, 'message' => 'QR Code wajib dipindai.'], 400);
        }

        if (!$jadwalId) {
            return response()->json(['success' => false, 'message' => 'Jadwal belum dipilih.'], 400);
        }

        // Cari peserta berdasarkan QR code
        $peserta = Peserta::where('qrcode', $scannedQr . '.svg')
            ->orWhere('qrcode', $scannedQr)
            ->first();

        if (!$peserta) {
            return response()->json(['success' => false, 'message' => 'QR Code tidak valid. Peserta tidak ditemukan.'], 400);
        }

        // Cek apakah sudah absen di jadwal ini
        $cekAbsen = Absensi::where('peserta_id', $peserta->id)
            ->where('jadwal_id', $jadwalId)
            ->exists();

        if ($cekAbsen) {
            return response()->json([
                'success' => false,
                'message' => 'Peserta sudah melakukan absensi pada jadwal ini.'
            ], 400);
        }

        $absensi = Absensi::create([
            'peserta_id' => $peserta->id,
            'jadwal_id' => $jadwalId,
            'tanggal' => date('Y-m-d'),
            'jam_masuk' => date('H:i:s'),
            'status' => 'Hadir',
            'keterangan' => 'Hadir via scan tutor',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Absensi ' . $peserta->nama . ' berhasil tercatat.',
            'peserta_id' => $peserta->id,
            'peserta_nama' => $peserta->nama,
            'peserta_kelas' => $peserta->kelas->nama_kelas ?? '-',
            'jam_masuk' => $absensi->jam_masuk,
            'keterangan' => $absensi->keterangan,
        ]);
    }


    public function riwayatKehadiran(Request $request)
    {
        $user = Auth::user();
        $peserta = null;

        $peserta = \App\Models\Peserta::where('user_id', $user->id)->first();

        if (!$peserta) {
            return redirect()->route('dashboard')->with('error', 'Data peserta tidak ditemukan.');
        }

        $bulanIni = $request->bulan ?? date('m');
        $tahunIni = $request->tahun ?? date('Y');

        // 2. Ambil Riwayat Keseluruhan berdasarkan filter
        $riwayat = Absensi::with(['jadwal.kelas', 'jadwal.tutor'])
            ->where('peserta_id', $peserta->id)
            ->whereMonth('tanggal', $bulanIni)
            ->whereYear('tanggal', $tahunIni)
            ->orderBy('tanggal', 'desc')
            ->get();

        // 3. Statistik Bulanan
        // Helper untuk query status
        $baseQuery = Absensi::where('peserta_id', $peserta->id)
            ->whereMonth('tanggal', $bulanIni)
            ->whereYear('tanggal', $tahunIni);

        $hadirCount = (clone $baseQuery)->whereRaw('LOWER(status) = ?', ['hadir'])->count();
        $izinCount  = (clone $baseQuery)->whereRaw('LOWER(status) = ?', ['izin'])->count();
        $sakitCount = (clone $baseQuery)->whereRaw('LOWER(status) = ?', ['sakit'])->count();
        $alpaCount  = (clone $baseQuery)->whereRaw('LOWER(status) = ?', ['alfa'])->count();

        // 4. Perhitungan Alpa: hanya dari hari yang sudah terjadi
        // Tidak perlu calculate alpa, gunakan nilai dari DB saja
        $rekap = [
            'hadir' => $hadirCount,
            'izin'  => $izinCount,
            'sakit' => $sakitCount,
            'alpa'  => $alpaCount,
        ];

        return view('absensi.riwayat-kehadiran', compact('riwayat', 'rekap', 'peserta', 'bulanIni', 'tahunIni'));
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

        // Ambil semua kelas yang diampu tutor ini (tidak hanya hari ini)
        $semuaJadwal = Jadwal::where('tutor_id', $tutor->id)
            ->with('kelas')
            ->get();
        $kelasIds = $semuaJadwal->pluck('kelas_id')->unique();

        // Jika tidak ada kelas sama sekali, gunakan kelas dari jadwal hari ini
        if ($kelasIds->isEmpty()) {
            $kelasIds = $jadwalHariIni->pluck('kelas_id')->unique();
        }

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

    public function deleteHarian(Request $request)
    {
        $request->validate([
            'peserta_id' => 'required|exists:pesertas,id',
        ]);

        $tanggalInfo = date('Y-m-d');

        $absensi = Absensi::where('peserta_id', $request->peserta_id)
            ->whereDate('tanggal', $tanggalInfo)
            ->first();

        if ($absensi) {
            $absensi->delete();
            return redirect()->back()->with('success', 'Data absensi berhasil dihapus.');
        }

        return redirect()->back()->with('error', 'Data absensi tidak ditemukan.');
    }

    public function rekapAbsensi(Request $request)
    {
        $query = Absensi::with(['peserta.kelas', 'jadwal.tutor', 'jadwal.kelas']);

        if ($request->filled('kelas_id')) {
            $query->whereHas('peserta', function ($q) use ($request) {
                $q->where('kelas_id', $request->kelas_id);
            });
        }

        if ($request->filled('tutor_id')) {
            $query->whereHas('jadwal', function ($q) use ($request) {
                $q->where('tutor_id', $request->tutor_id);
            });
        }

        if ($request->filled('jadwal_id')) {
            $query->where('jadwal_id', $request->jadwal_id);
        }

        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal', $request->tanggal);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $rekap = $query->orderBy('tanggal', 'desc')
                       ->orderBy('jam_masuk', 'desc')
                       ->paginate(15)
                       ->withQueryString();

        $kelasList = \App\Models\Kelas::orderBy('nama_kelas')->get();
        $tutorList = \App\Models\Tutor::orderBy('nama')->get();
        $jadwalList = \App\Models\Jadwal::with(['kelas', 'tutor'])->get();

        return view('absensi.rekap-absensi', compact('rekap', 'kelasList', 'tutorList', 'jadwalList'));
    }



    public function rekapPerKelas(Request $request)
    {
        $bulan = $request->bulan ?? date('m');
        $tahun = $request->tahun ?? date('Y');

        $kelasList = \App\Models\Kelas::withCount('pesertas')->orderBy('nama_kelas')->get();

        // Buat daftar tanggal efektif (kecuali Minggu)
        $systemStartDate = \Carbon\Carbon::create(2026, 6, 1)->startOfDay();
        
        $requestedMonthStart = \Carbon\Carbon::createFromDate($tahun, $bulan, 1)->startOfMonth();
        $requestedMonthEnd = \Carbon\Carbon::createFromDate($tahun, $bulan, 1)->endOfMonth();
        $now = \Carbon\Carbon::now()->endOfDay();

        $startDate = $requestedMonthStart->copy()->max($systemStartDate);
        $endDate = $requestedMonthEnd->copy()->min($now);

        $tanggalList = [];
        
        if ($startDate->lte($endDate)) {
            $currentDate = $startDate->copy();
            while ($currentDate->lte($endDate)) {
                if ($currentDate->dayOfWeek !== \Carbon\Carbon::SUNDAY) {
                    $tanggalList[] = $currentDate->format('Y-m-d');
                }
                $currentDate->addDay();
            }
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
