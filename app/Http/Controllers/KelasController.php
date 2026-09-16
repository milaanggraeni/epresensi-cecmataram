<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class KelasController extends Controller
{
    public function index(Request $request)
    {
        $query = Kelas::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_kelas', 'LIKE', '%' . $search . '%')
                    ->orWhere('wali_kelas', 'LIKE', '%' . $search . '%');
            });
        }

        $kelas = $query->withCount('pesertas')->orderBy('nama_kelas')->paginate(10)->appends($request->query());

        return view('kelas.index', compact('kelas') + ['search' => $request->search ?? '']);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kelas' => 'required',
            'wali_kelas' => 'required|',

        ]);

        $kelas = Kelas::create([
            'nama_kelas' => $request->nama_kelas,
            'wali_kelas' => $request->wali_kelas,
            'jumlah_peserta' => '0',
        ]);


        return redirect()->back()->with('success', 'Data Kelas berhasil ditambahkan');
    }


    public function edit(Request $request)
    {
        $kelas = Kelas::findOrFail($request->id);
        return view('kelas.edit', compact('kelas'));
    }
    public function update(Request $request, $id)
    {
        // 1. Validasi Input
        $request->validate([
            'nama_kelas'   => 'required|string|max:255',
            'wali_kelas'   => 'required|string|max:255',
        ]);

        try {
            // 2. Cari data dan Update
            $kelas = Kelas::findOrFail($id);
            $kelas->update([
                'nama_kelas'   => $request->nama_kelas,
                'wali_kelas'   => $request->wali_kelas,
            ]);

            // 3. Redirect dengan pesan sukses
            return redirect()->route('kelas')->with('success', 'Data kelas berhasil diperbarui!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    public function destroy($id)
    {
        try {
            $kelas = Kelas::findOrFail($id);

            if ($kelas->pesertas()->exists()) {
                return redirect()->back()->with('error', 'Kelas tidak bisa dihapus karena masih memiliki data peserta.');
            }

            if ($kelas->jadwals()->exists()) {
                return redirect()->back()->with('error', 'Kelas tidak bisa dihapus karena masih terdaftar dalam jadwal pelajaran.');
            }

            $kelas->delete();

            return redirect()->route('kelas')->with('success', 'Data kelas berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }
}
