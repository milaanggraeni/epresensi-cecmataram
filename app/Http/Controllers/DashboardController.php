<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Kelas;
use App\Exports\AbsensiExport;
use App\Models\Peserta;
use App\Models\Tutor;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $data = [];

        if ($user->role === 'admin') {
            $data['totalPeserta'] = Peserta::count();
            $data['totalTutor'] = Tutor::count();
            $data['totalKelas'] = Kelas::count();

            // Absensi hari ini
            $data['hadirHariIni'] = Absensi::whereDate('tanggal', date('Y-m-d'))
                ->whereRaw('LOWER(status) = ?', ['hadir'])->count();

            // Filter
            $bulan = $request->bulan ?? date('m');
            $tahun = $request->tahun ?? date('Y');
            $search = $request->search ?? '';

            $query = Absensi::with('peserta.kelas')
                ->whereMonth('tanggal', $bulan)
                ->whereYear('tanggal', $tahun)
                ->orderBy('tanggal', 'desc')
                ->orderBy('id', 'desc');

            if ($search) {
                $query->whereHas('peserta', function ($q) use ($search) {
                    $q->where('nama', 'LIKE', '%' . $search . '%')
                        ->orWhere('nis', 'LIKE', '%' . $search . '%');
                });
            }

            $data['absensi'] = $query->paginate(10)->appends($request->query());
            $data['bulan'] = $bulan;
            $data['tahun'] = $tahun;
            $data['search'] = $search;
        }


        if ($user->role === 'peserta') {
            // $peserta = Peserta::where('user_id', $user->id)->first();
            $peserta = Peserta::with('kelas')->where('user_id', $user->id)->first();
            $data['peserta'] = $peserta;

            if ($peserta) {
                // Cek status absensi hari ini
                $data['absenHariIni'] = Absensi::where('peserta_id', $peserta->id)
                    ->whereDate('tanggal', date('Y-m-d'))
                    ->first();

                // Tambahkan data waktu untuk jam digital di view
                $data['tanggalHariIni'] = \Carbon\Carbon::now()->isoFormat('dddd, D MMMM Y');
            }
        }


        if ($user->role === 'tutor') {
            $tutor = Tutor::where('user_id', $user->id)->first();
            $data['tutor'] = $tutor;

            if ($tutor) {
                $now = \Carbon\Carbon::now();
                $data['tanggalHariIni'] = $now->isoFormat('dddd, D MMMM Y');
                $data['jamSekarang'] = $now->format('H:i');

                // Ambil nama hari dalam Bahasa Indonesia untuk filter database
                $hariIni = $now->locale('id')->dayName;

                $data['jadwalHariIni'] = \App\Models\Jadwal::with('kelas')
                    ->where('tutor_id', $tutor->id)
                    ->where('hari', $hariIni)
                    ->orderBy('jam_mulai', 'asc')
                    ->get();
            }
        }
        return view('dashboard.index', $data);
    }

    public function deleteAbsensi($id)
    {
        $absensi = Absensi::findOrFail($id);
        $absensi->delete();

        return redirect()->back()->with('success', 'Data absensi berhasil dihapus.');
    }

    public function updateAbsensi(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Hadir,Izin,Sakit,Alfa',
            'keterangan' => 'nullable|string'
        ]);

        $absensi = Absensi::findOrFail($id);
        $absensi->update([
            'status' => $request->status,
            'keterangan' => $request->keterangan,
        ]);

        return redirect()->back()->with('success', 'Data absensi berhasil diperbarui.');
    }

    /**
     * Export absensi data to PDF
     */
    public function exportPdf(Request $request)
    {
        $bulan = $request->bulan ?? date('m');
        $tahun = $request->tahun ?? date('Y');
        $search = $request->search ?? '';

        $query = Absensi::with('peserta.kelas')
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->orderBy('tanggal', 'desc')
            ->orderBy('id', 'desc');

        if ($search) {
            $query->whereHas('peserta', function ($q) use ($search) {
                $q->where('nama', 'LIKE', '%' . $search . '%')
                    ->orWhere('nis', 'LIKE', '%' . $search . '%');
            });
        }

        $absensi = $query->get();
        $namaBulan = \Carbon\Carbon::createFromDate(null, $bulan, 1)->isoFormat('MMMM');

        $pdf = Pdf::loadView('exports.absensi-pdf', compact('absensi', 'bulan', 'tahun', 'search'))
            ->setPaper('a4', 'landscape');

        return $pdf->download("Rekap_Absensi_{$namaBulan}_{$tahun}.pdf");
    }

    /**
     * Export absensi data to Excel
     */
    public function exportExcel(Request $request)
    {
        $bulan = $request->bulan ?? date('m');
        $tahun = $request->tahun ?? date('Y');
        $search = $request->search ?? '';

        $namaBulan = \Carbon\Carbon::createFromDate(null, $bulan, 1)->isoFormat('MMMM');

        return Excel::download(
            new AbsensiExport($bulan, $tahun, $search),
            "Rekap_Absensi_{$namaBulan}_{$tahun}.xlsx"
        );
    }

    /**
     * Download ID Card PDF for Peserta
     */
    public function downloadIdCard()
    {
        $user = Auth::user();
        if ($user->role !== 'peserta') {
            return redirect()->back()->with('error', 'Akses ditolak.');
        }

        $peserta = Peserta::with('kelas')->where('user_id', $user->id)->firstOrFail();

        // Ukuran kertas custom (lebar 240pt, tinggi 370pt) menyesuaikan desain CSS
        $pdf = Pdf::loadView('exports.id-card-pdf', compact('peserta'))
            ->setPaper([0, 0, 240, 370], 'portrait');

        return $pdf->download("ID_Card_" . \Illuminate\Support\Str::slug($peserta->nama) . ".pdf");
    }
}
