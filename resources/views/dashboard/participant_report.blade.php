@extends('layouts.app')

@section('title', 'Laporan Peserta')
@section('page-title', 'Laporan Peserta')
@section('page-subtitle', 'Filter dan lihat detail peserta')

@section('content')
<div class="glass-card p-6 rounded-2xl border border-dark-200/50">
    <form method="GET" action="{{ route('reports.participants') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div>
            <label class="block text-sm font-medium text-dark-700 mb-1" for="class_id">Kelas</label>
            <select name="class_id" id="class_id" class="w-full rounded-xl border border-dark-200 bg-dark-50/30 focus:bg-white focus:ring-2 focus:ring-primary-500/30 focus:border-primary-500 transition">
                <option value="">-- Semua Kelas --</option>
                @foreach(App\Models\Kelas::orderBy('nama')->get() as $kelas)
                    <option value="{{ $kelas->id }}" {{ request('class_id') == $kelas->id ? 'selected' : '' }}>{{ $kelas->nama }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-dark-700 mb-1" for="name">Nama Peserta</label>
            <input type="text" name="name" id="name" value="{{ request('name') }}" placeholder="Cari nama..." class="w-full rounded-xl border border-dark-200 bg-dark-50/30 focus:bg-white focus:ring-2 focus:ring-primary-500/30 focus:border-primary-500 transition" />
        </div>
        <div class="flex items-end">
            <button type="submit" class="w-full px-4 py-2 bg-gradient-to-r from-primary-600 to-primary-700 text-white rounded-xl hover:from-primary-700 hover:to-primary-800 transition">
                <i class='bx bx-search'></i> Cari
            </button>
        </div>
    </form>

    <table class="min-w-full bg-dark-50/30 rounded-xl overflow-hidden">
        <thead class="bg-primary-600 text-white">
            <tr>
                <th class="p-2 text-left">No</th>
                <th class="p-2 text-left">Nama</th>
                <th class="p-2 text-left">Hari</th>
                <th class="p-2 text-left">Kelas</th>
                <th class="p-2 text-left">Foto</th>
            </tr>
        </thead>
        <tbody>
            @forelse($participants as $index => $peserta)
                <tr class="border-b border-dark-200/30">
                    <td class="p-2">{{ $index + 1 }}</td>
                    <td class="p-2">{{ $peserta->nama }}</td>
                <td class="p-2">{{ $peserta->hari ?? '-' }}</td>
                    <td class="p-2">{{ $peserta->kelas->nama ?? '-' }}</td>
                    <td class="p-2">
                        @if($peserta->foto)
                            <img src="{{ asset('storage/' . $peserta->foto) }}" alt="Foto" class="h-12 w-12 object-cover rounded-full" />
                        @else
                            <span class="text-sm text-dark-500">-</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="p-4 text-center text-dark-500">Tidak ada data.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
