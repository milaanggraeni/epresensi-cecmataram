<?php

namespace App\Http\Controllers;


use App\Exports\PesertaExport;
use App\Models\Kelas;
use App\Models\Peserta;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class PesertaController extends Controller
{
    public function index(Request $request)
    {
        $query = Peserta::with(['kelas', 'user']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'LIKE', '%' . $search . '%');
            });
        }

        if ($request->filled('kelas_id')) {
            $query->where('kelas_id', $request->kelas_id);
        }

        $peserta = $query->orderBy('nama')->paginate(10)->appends($request->query());
        $kelas = Kelas::all();
        return view('peserta.index', compact('peserta', 'kelas') + ['search' => $request->search ?? '', 'kelas_id' => $request->kelas_id ?? '']);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:L,P',
            'kelas_id' => 'required|exists:kelas,id',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'alamat' => 'nullable|string',
            'nama_wali' => 'nullable|string|max:255',
            'nomor_hp_wali' => 'nullable|string|max:20',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = User::create([
            'name' => $request->nama,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'peserta',
        ]);

        $qrCodeString = Str::random(15);
        $qrCodeFileName = $qrCodeString . '.svg';
        $qrCodePath = public_path('qrcodes');

        if (!File::exists($qrCodePath)) {
            File::makeDirectory($qrCodePath, 0755, true);
        }

        QrCode::format('svg')->size(300)->generate($qrCodeString, $qrCodePath . '/' . $qrCodeFileName);

        // Handle photo upload only when the column exists
        $fotoPath = null;
        if (Schema::hasColumn('pesertas', 'foto') && $request->hasFile('foto')) {
            $fotoFile = $request->file('foto');
            $fotoPath = $fotoFile->store('peserta', 'public');
        }

        $pesertaData = [
            'user_id' => $user->id,
            'nama' => $request->nama,
            'jenis_kelamin' => $request->jenis_kelamin,
            'kelas_id' => $request->kelas_id,
            'qrcode' => $qrCodeFileName,
            'alamat' => $request->alamat,
            'nama_wali' => $request->nama_wali,
            'nomor_hp_wali' => $request->nomor_hp_wali,
        ];

        if (Schema::hasColumn('pesertas', 'foto') && $fotoPath !== null) {
            $pesertaData['foto'] = $fotoPath;
        }

        Peserta::create($pesertaData);

        return redirect()->back()->with('success', 'Data Peserta berhasil ditambahkan');
    }

    public function edit(Request $request)
    {
        $peserta = Peserta::with('user')->findOrFail($request->id);
        $kelas = Kelas::all();
        return view('peserta.edit', compact('peserta', 'kelas'));
    }

    public function update(Request $request, $id)
    {
        $peserta = Peserta::findOrFail($id);

        $request->validate([
            'nama' => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:L,P',
            'kelas_id' => 'required|exists:kelas,id',
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($peserta->user_id)],
            'password' => 'nullable|min:6',
            'alamat' => 'nullable|string',
            'nama_wali' => 'nullable|string|max:255',
            'nomor_hp_wali' => 'nullable|string|max:20',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $userData = [
            'name' => $request->nama,
            'email' => $request->email,
        ];

        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->password);
        }

        $peserta->user->update($userData);

        $pesertaData = [
            'nama' => $request->nama,
            'jenis_kelamin' => $request->jenis_kelamin,
            'kelas_id' => $request->kelas_id,
            'alamat' => $request->alamat,
            'nama_wali' => $request->nama_wali,
            'nomor_hp_wali' => $request->nomor_hp_wali,
        ];

        if (Schema::hasColumn('pesertas', 'foto')) {
            $fotoPath = $peserta->foto;
            if ($request->hasFile('foto')) {
                // Delete old photo if exists
                if ($peserta->foto && File::exists(storage_path('app/public/' . $peserta->foto))) {
                    File::delete(storage_path('app/public/' . $peserta->foto));
                }
                $fotoFile = $request->file('foto');
                $fotoPath = $fotoFile->store('peserta', 'public');
            }
            $pesertaData['foto'] = $fotoPath;
        }

        $peserta->update($pesertaData);

        return redirect()->back()->with('success', 'Data Peserta berhasil diperbarui');
    }

    public function destroy($id)
    {
        $peserta = Peserta::findOrFail($id);

        $qrCodeFile = public_path('qrcodes/' . $peserta->qrcode);
        if (File::exists($qrCodeFile) && !empty($peserta->qrcode)) {
            File::delete($qrCodeFile);
        }

        if ($peserta->user) {
            $peserta->user->delete(); // This should cascade and delete Siswa as well based on migration
        } else {
            $peserta->delete();
        }

        return redirect()->back()->with('success', 'Data Peserta berhasil dihapus');
    }

    /**
     * Export peserta data to PDF
     */
    public function exportPdf(Request $request)
    {
        $query = Peserta::with(['kelas', 'user']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'LIKE', '%' . $search . '%');
            });
        }

        if ($request->filled('kelas_id')) {
            $query->where('kelas_id', $request->kelas_id);
        }

        $peserta = $query->orderBy('nama')->get();
        $search = $request->search ?? '';
        $filterKelas = '';
        if ($request->filled('kelas_id')) {
            $kelas = Kelas::find($request->kelas_id);
            $filterKelas = $kelas ? $kelas->nama_kelas : '';
        }

        $pdf = Pdf::loadView('exports.peserta-pdf', compact('peserta', 'search', 'filterKelas'))
            ->setPaper('a4', 'landscape');

        return $pdf->stream('Data_Peserta.pdf');
    }


    public function exportExcel(Request $request)
    {
        return Excel::download(
            new PesertaExport($request->search ?? '', $request->kelas_id ?? ''),
            'Data_Peserta.xlsx'
        );
    }
}
