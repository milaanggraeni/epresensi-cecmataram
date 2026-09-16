<?php

namespace App\Http\Controllers;

use App\Models\Jadwal;
use App\Models\Kelas;
use App\Models\Tutor;
use Illuminate\Http\Request;

class JadwalController extends Controller
{
    public function index(Request $request)
    {
        $query = Jadwal::with(['kelas', 'tutor']);

        if ($request->filled('hari')) {
            $query->where('hari', $request->hari);
        }

        if ($request->filled('kelas_id')) {
            $query->where('kelas_id', $request->kelas_id);
        }

        $jadwal = $query->orderBy('hari')->orderBy('jam_mulai')->paginate(10)->appends($request->query());
        $kelas = Kelas::all();
        $tutor = Tutor::all();
        return view('jadwal.index', compact('jadwal', 'kelas', 'tutor') + [
            'filter_hari' => $request->hari ?? '',
            'filter_kelas' => $request->kelas_id ?? '',
        ]);
    }
    public function store(Request $request)
    {
        $request->validate([
            'tutor_id' => 'required|exists:tutors,id',
            'kelas_id' => 'required|exists:kelas,id',
            'mata_pelajaran' => 'required|string|max:255',
            'tanggal' => 'nullable|date',
            'hari' => 'required|string',
            'ruangan' => 'nullable|string|max:255',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
        ], [
            'jam_selesai.after' => 'Jam selesai harus lebih besar dari jam mulai.',
            'jam_mulai.date_format' => 'Format jam mulai tidak valid.',
        ]);

        // Check konflik jadwal
        if (Jadwal::hasConflict($request->hari, $request->jam_mulai, $request->jam_selesai, $request->kelas_id, $request->tutor_id)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Tidak bisa tambah data, jadwal sudah ada');
        }

        Jadwal::create($request->all());

        return redirect()->back()->with('success', 'Jadwal berhasil ditambahkan!');
    }

    // Menampilkan Form Edit via AJAX
    public function edit(Request $request)
    {
        $jadwal = Jadwal::findOrFail($request->id);
        $kelas = Kelas::all();
        $tutor = Tutor::all();

        return view('jadwal.edit', compact('jadwal', 'kelas', 'tutor'));
    }

    // Proses Update ke Database
    public function update(Request $request, $id)
    {
        $request->validate([
            'kelas_id' => 'required',
            'tanggal' => 'nullable|date',
            'hari' => 'required',
            'ruangan' => 'nullable|string|max:255',
            'tutor_id' => 'required',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required',
        ]);

        try {
            $jadwal = Jadwal::findOrFail($id);

            // Check konflik jadwal (exclude jadwal yang sedang diedit)
            if (Jadwal::hasConflict($request->hari, $request->jam_mulai, $request->jam_selesai, $request->kelas_id, $request->tutor_id, $id)) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Tidak bisa update data, jadwal sudah ada');
            }

            $jadwal->update([
                'kelas_id' => $request->kelas_id,
                'tanggal' => $request->tanggal,
                'hari' => $request->hari,
                'ruangan' => $request->ruangan,
                'tutor_id' => $request->tutor_id,
                'mata_pelajaran' => $request->mata_pelajaran,
                'jam_mulai' => $request->jam_mulai,
                'jam_selesai' => $request->jam_selesai,
            ]);

            return redirect()->back()->with('success', 'Jadwal berhasil diperbarui!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memperbarui jadwal: ' . $e->getMessage());
        }
    }
    public function destroy($id)
    {
        try {
            $jadwal = Jadwal::findOrFail($id);
            $jadwal->delete();

            return redirect()->back()->with('success', 'Jadwal berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }
}
