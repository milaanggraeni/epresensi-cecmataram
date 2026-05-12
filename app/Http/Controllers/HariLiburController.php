<?php

namespace App\Http\Controllers;

use App\Models\HariLibur;
use Illuminate\Http\Request;

class HariLiburController extends Controller
{
    public function index(Request $request)
    {
        $query = HariLibur::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('keterangan', 'LIKE', '%' . $search . '%');
            });
        }

        $hariLibur = $query->orderBy('created_at')->paginate(10)->appends($request->query());

        return view('hari_libur.index', compact('hariLibur') + ['search' => $request->search ?? '']);
    }

    public function store(Request $request)
    {
        $request->validate([
            'keterangan' => 'required|string|max:255',
            'tanggal' => 'required|date',
        ]);

        HariLibur::create([
            'keterangan' => $request->keterangan,
            'tanggal' => $request->tanggal,
        ]);

        return redirect()->route('hariLibur')->with('success', 'Data Hari Libur berhasil ditambahkan.');
    }

    public function edit(Request $request)
    {
        $hariLibur = HariLibur::findOrFail($request->id);
        return view('hari_libur.edit', compact('hariLibur'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'keterangan' => 'required|string|max:255',
            'tanggal' => 'required|date',
        ]);

        $hariLibur = HariLibur::findOrFail($id);
        $hariLibur->update([
            'keterangan' => $request->keterangan,
            'tanggal' => $request->tanggal,
        ]);

        return redirect()->route('hariLibur')->with('success', 'Data Hari Libur berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $hariLibur = HariLibur::findOrFail($id);
        $hariLibur->delete();

        return redirect()->route('hariLibur')->with('success', 'Data Hari Libur berhasil dihapus.');
    }
}
