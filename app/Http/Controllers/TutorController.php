<?php

namespace App\Http\Controllers;

use App\Models\Tutor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\File;

class TutorController extends Controller
{
    public function index(Request $request)
    {
        $query = Tutor::with(['user']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'LIKE', '%' . $search . '%')
                    ->orWhere('mapel', 'LIKE', '%' . $search . '%');
            });
        }

        $tutor = $query->orderBy('nama')->paginate(10)->appends($request->query());
        return view('tutor.index', compact('tutor') + ['search' => $request->search ?? '']);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'mapel' => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:L,P',
            'alamat' => 'required|string|max:500',
            'nomor_hp' => 'nullable|string|max:25',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = User::create([
            'name' => $request->nama,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'tutor',
        ]);

        // Handle photo upload
        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $fotoFile = $request->file('foto');
            $fotoPath = $fotoFile->store('tutor', 'public');
        }

        Tutor::create([
            'user_id' => $user->id,
            'nama' => $request->nama,
            'mapel' => $request->mapel,
            'jenis_kelamin' => $request->jenis_kelamin,
            'alamat' => $request->alamat,
            'nomor_hp' => $request->nomor_hp,
            'foto' => $fotoPath,
        ]);

        return redirect()->back()->with('success', 'Data Tutor berhasil ditambahkan');
    }

    public function edit(Request $request)
    {
        $tutor = Tutor::with('user')->findOrFail($request->id);
        return view('tutor.edit', compact('tutor'));
    }

    public function update(Request $request, $id)
    {
        $tutor = Tutor::findOrFail($id);

        $request->validate([
            'nama' => 'required|string|max:255',
            'mapel' => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:L,P',
            'alamat' => 'required|string|max:500',
            'nomor_hp' => 'nullable|string|max:25',
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($tutor->user_id)],
            'password' => 'nullable|min:6',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $userData = [
            'name' => $request->nama,
            'email' => $request->email,
        ];

        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->password);
        }

        $tutor->user->update($userData);

        // Handle photo upload
        $fotoPath = $tutor->foto;
        if ($request->hasFile('foto')) {
            // Delete old photo if exists
            if ($tutor->foto && File::exists(storage_path('app/public/' . $tutor->foto))) {
                File::delete(storage_path('app/public/' . $tutor->foto));
            }
            $fotoFile = $request->file('foto');
            $fotoPath = $fotoFile->store('tutor', 'public');
        }

        $tutor->update([
            'nama' => $request->nama,
            'mapel' => $request->mapel,
            'jenis_kelamin' => $request->jenis_kelamin,
            'alamat' => $request->alamat,
            'nomor_hp' => $request->nomor_hp,
            'foto' => $fotoPath,
        ]);

        return redirect()->back()->with('success', 'Data tutor berhasil diperbarui');
    }

    public function destroy($id)
    {
        $tutor = Tutor::findOrFail($id);
        if ($tutor->user) {
            $tutor->user->delete(); // This should cascade
        } else {
            $tutor->delete();
        }

        return redirect()->back()->with('success', 'Data Tutor berhasil dihapus');
    }
}
