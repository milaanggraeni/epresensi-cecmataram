<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Peserta;
use App\Models\Tutor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class ReportController extends Controller
{
    /**
     * Return participant (student) report data.
     * Supports filtering by class_id and name (partial match).
     */
    public function participantReport(Request $request)
    {
        $query = Peserta::with('kelas');

        if ($request->filled('class_id')) {
            $query->where('kelas_id', $request->input('class_id'));
        }
        if ($request->filled('name')) {
            $query->where('nama', 'like', '%' . $request->input('name') . '%');
        }

        $participants = $query->orderBy('nama')->get();

        // If request expects JSON (e.g., DataTables AJAX)
        if ($request->wantsJson()) {
            return Response::json(['data' => $participants]);
        }

        return view('dashboard.participant_report', compact('participants'));
    }

    /**
     * Return tutor attendance report data.
     * Supports filtering by date range, class (via jadwal), and mapel.
     */
    public function tutorReport(Request $request)
    {
        $query = Tutor::with(['jadwals.kelas', 'jadwals.kelas']);

        // Date range filter (expects start_date and end_date in Y-m-d format)
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereHas('jadwals', function ($q) use ($request) {
                $q->whereBetween('tanggal', [$request->input('start_date'), $request->input('end_date')]);
            });
        }
        if ($request->filled('class_id')) {
            $query->whereHas('jadwals', function ($q) use ($request) {
                $q->where('kelas_id', $request->input('class_id'));
            });
        }
        if ($request->filled('mapel')) {
            $query->where('mapel', 'like', '%' . $request->input('mapel') . '%');
        }

        $tutors = $query->orderBy('nama')->get();

        if ($request->wantsJson()) {
            return Response::json(['data' => $tutors]);
        }

        return view('dashboard.tutor_report', compact('tutors'));
    }

    /**
     * Export participant report as CSV or PDF.
     */
    public function exportParticipant(Request $request, $format)
    {
        $participants = Peserta::with('kelas');
        if ($request->filled('class_id')) {
            $participants = $participants->where('kelas_id', $request->input('class_id'));
        }
        if ($request->filled('name')) {
            $participants = $participants->where('nama', 'like', '%' . $request->input('name') . '%');
        }
        $participants = $participants->orderBy('nama')->get();
        if (strtolower($format) === 'csv') {
            $filename = 'participants_' . now()->format('Ymd_His') . '.csv';
            $columns = ['Nama', 'Kelas', 'Email'];
            $callback = function () use ($participants, $columns) {
                $file = fopen('php://output', 'w');
                fputcsv($file, $columns);
                foreach ($participants as $p) {
                    fputcsv($file, [
                        $p->nama,
                        $p->kelas->nama ?? '-',
                        $p->email ?? '-',
                    ]);
                }
                fclose($file);
            };
            return response()->streamDownload($callback, $filename, ['Content-Type' => 'text/csv']);
        }
        // Placeholder for PDF export
        return back()->with('error', 'PDF export not implemented yet');
    }

    /**
     * Export tutor report as CSV or PDF.
     */
    public function exportTutor(Request $request, $format)
    {
        $query = Tutor::with(['jadwals.kelas', 'jadwals.kelas']);
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereHas('jadwals', function ($q) use ($request) {
                $q->whereBetween('tanggal', [$request->input('start_date'), $request->input('end_date')]);
            });
        }
        if ($request->filled('class_id')) {
            $query->whereHas('jadwals', function ($q) use ($request) {
                $q->where('kelas_id', $request->input('class_id'));
            });
        }
        if ($request->filled('mapel')) {
            $query->where('mapel', 'like', '%' . $request->input('mapel') . '%');
        }
        $tutors = $query->orderBy('nama')->get();
        if (strtolower($format) === 'csv') {
            $filename = 'tutors_' . now()->format('Ymd_His') . '.csv';
            $columns = ['Nama Tutor', 'Mata Pelajaran', 'Kelas', 'Tanggal', 'Jam'];
            $callback = function () use ($tutors, $columns) {
                $file = fopen('php://output', 'w');
                fputcsv($file, $columns);
                foreach ($tutors as $tutor) {
                    foreach ($tutor->jadwals as $jadwal) {
                        fputcsv($file, [
                            $tutor->nama,
                            $tutor->mapel,
                            $jadwal->kelas->nama ?? '-',
                            $jadwal->tanggal,
                            $jadwal->jam_mulai . ' - ' . $jadwal->jam_selesai,
                        ]);
                    }
                }
                fclose($file);
            };
            return response()->streamDownload($callback, $filename, ['Content-Type' => 'text/csv']);
        }
        return back()->with('error', 'PDF export not implemented yet');
    }

}

?>
