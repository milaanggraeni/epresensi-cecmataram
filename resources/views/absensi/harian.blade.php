@extends('layouts.app')

@section('title', 'Absensi Harian Kelas')
@section('page-title', 'Absensi Harian')
@section('page-subtitle', 'Pantau kehadiran Peserta di kelas Anda hari ini')

@section('breadcrumb')
    <li><i class='bx bx-chevron-right text-dark-400'></i></li>
    <li class="text-primary-600 font-medium">Absensi Harian</li>
@endsection

@section('content')
    <div class="flex flex-col gap-6">

        {{-- Panel Jadwal & Info --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            {{-- Card Hari Ini --}}
            <div class="md:col-span-1">
                <div class="glass-card rounded-2xl border border-dark-200/50 p-6 h-full">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-12 h-12 rounded-xl bg-primary-50 text-primary-600 flex items-center justify-center">
                            <i class='bx bx-calendar-event text-2xl'></i>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-dark-500 uppercase tracking-widest">Hari Ini</p>
                            <h3 class="text-lg font-bold text-dark-800">{{ $hariIniStr }}</h3>
                        </div>
                    </div>
                    <p class="text-sm text-dark-600">{{ \Carbon\Carbon::now()->isoFormat('D MMMM Y') }}</p>
                </div>
            </div>

            {{-- Card Kelas Hari Ini --}}
            <div class="md:col-span-1">
                <div class="glass-card rounded-2xl border border-dark-200/50 p-6 h-full">
                    <h4 class="text-sm font-bold text-dark-800 mb-3 flex items-center gap-2">
                        <i class='bx bx-chalkboard text-primary-500'></i> Kelas Anda Hari Ini:
                    </h4>

                    @if ($jadwalHariIni->isEmpty())
                        <div class="p-3 bg-dark-50 rounded-xl border border-dark-100 text-center">
                            <p class="text-sm text-dark-500">Tidak ada jadwal hari ini.</p>
                        </div>
                    @else
                        <ul class="space-y-2">
                            @foreach ($jadwalHariIni as $jdwl)
                                <li
                                    class="p-2 bg-white border border-dark-100 rounded-xl flex justify-between items-center">
                                    <p class="text-sm font-bold text-dark-800">{{ $jdwl->kelas->nama_kelas }}</p>
                                    <span
                                        class="px-2 py-0.5 bg-dark-50 rounded text-[10px] text-dark-500">{{ $jdwl->mata_pelajaran }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>

            {{-- Card Scan QR --}}
            <div class="md:col-span-1">
                <div
                    class="glass-card rounded-2xl border border-primary-500/30 p-6 h-full bg-gradient-to-br from-primary-600/5 to-accent-500/5 flex flex-col items-center justify-center text-center">
                    <div
                        class="w-16 h-16 rounded-2xl bg-gradient-to-br from-primary-600 to-accent-500 flex items-center justify-center text-white shadow-lg shadow-primary-500/30 mb-4">
                        <i class='bx bx-qr-scan text-3xl'></i>
                    </div>
                    <h4 class="text-sm font-bold text-dark-800 mb-1">Scan Kartu Peserta</h4>
                    <p class="text-xs text-dark-500 mb-4">Scan QR code ID card peserta untuk mencatat kehadiran</p>
                    <button onclick="openScannerModal()"
                        class="w-full flex items-center justify-center gap-2 px-6 py-3 bg-gradient-to-r from-primary-600 to-primary-700 hover:from-primary-700 hover:to-primary-800 text-white font-bold rounded-xl shadow-lg shadow-primary-500/30 hover:shadow-primary-500/40 hover:-translate-y-0.5 transition-all duration-300">
                        <i class='bx bx-scan text-xl'></i>
                        <span>Mulai Scan</span>
                    </button>
                </div>
            </div>
        </div>

        {{-- Panel Daftar Peserta --}}
        <div class="w-full">
            <div class="glass-card rounded-2xl border border-dark-200/50 overflow-hidden">
                <div
                    class="px-6 py-5 border-b border-dark-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-dark-50/30">
                    <div>
                        <h3 class="text-lg font-bold text-dark-800">Daftar Kehadiran Peserta</h3>
                        <p class="text-sm text-dark-500 mt-1">Status rekam kehadiran Real-Time</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <button onclick="openScannerModal()"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-600 hover:to-emerald-700 text-white text-xs font-bold rounded-xl shadow-lg shadow-emerald-500/25 transition-all duration-200">
                            <i class='bx bx-qr-scan text-base'></i>
                            Scan QR
                        </button>
                        <div
                            class="flex items-center gap-2 px-4 py-2 bg-white border border-dark-200 rounded-xl text-sm font-medium text-dark-600 shadow-sm">
                            <i class='bx bxs-user-detail text-primary-500'></i>
                            {{ $pesertas->total() }} Peserta Aktif
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto custom-scrollbar">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-dark-50/50 border-b border-dark-200/50">
                                <th
                                    class="px-6 py-4 text-xs font-semibold text-dark-500 uppercase tracking-wider w-12 text-center">
                                    No</th>
                                <th class="px-6 py-4 text-xs font-semibold text-dark-500 uppercase tracking-wider">
                                    Nama Peserta</th>
                                <th class="px-6 py-4 text-xs font-semibold text-dark-500 uppercase tracking-wider">Gender
                                </th>
                                <th class="px-6 py-4 text-xs font-semibold text-dark-500 uppercase tracking-wider">Kelas
                                </th>
                                <th class="px-6 py-4 text-xs font-semibold text-dark-500 uppercase tracking-wider">Status
                                    Absensi (Hari Ini)</th>
                                <th class="px-6 py-4 text-xs font-semibold text-dark-500 uppercase tracking-wider">Jam /
                                    Info</th>
                                <th
                                    class="px-6 py-4 text-xs font-semibold text-dark-500 uppercase tracking-wider text-center">
                                    Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-dark-200/50">
                            @forelse ($pesertas as $s)
                                @php
                                    $absen = $s->absensis->first();
                                @endphp
                                <tr class="hover:bg-dark-50/50 transition-colors duration-200"
                                    id="peserta-row-{{ $s->id }}">
                                    <td class="px-6 py-4 text-sm text-dark-600 text-center">
                                        {{ $pesertas->firstItem() + $loop->index }}</td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="flex-shrink-0 w-8 h-8 rounded-full bg-primary-100 text-primary-600 flex items-center justify-center font-bold text-xs uppercase">
                                                {{ substr($s->nama, 0, 1) }}
                                            </div>
                                            <div>
                                                <p class="text-sm font-bold text-dark-800">{{ $s->nama }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-dark-600">
                                        {{ $s->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}
                                    </td>
                                    <td class="px-6 py-4 text-sm font-bold text-dark-700">
                                        {{ $s->kelas->nama_kelas }}
                                    </td>
                                    <td class="px-6 py-4" id="absensi-status-{{ $s->id }}">
                                        @if ($absen)
                                            @php $status = strtolower($absen->status); @endphp
                                            @if ($status == 'hadir')
                                                <span
                                                    class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                    <i class='bx bx-check-shield text-sm'></i> Hadir
                                                </span>
                                            @elseif($status == 'izin')
                                                <span
                                                    class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-bold bg-primary-50 text-primary-700 border border-primary-200">
                                                    <i class='bx bx-envelope text-sm'></i> Izin
                                                </span>
                                            @elseif($status == 'sakit')
                                                <span
                                                    class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                                    <i class='bx bx-plus-medical text-sm'></i> Sakit
                                                </span>
                                            @else
                                                <span
                                                    class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                                    <i class='bx bx-x-circle text-sm'></i> Alfa
                                                </span>
                                            @endif
                                        @else
                                            <span
                                                class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-bold bg-dark-100 text-dark-600 border border-dark-200">
                                                <i class='bx bx-time text-sm'></i> Belum Absen
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4" id="absensi-jam-{{ $s->id }}">
                                        @if ($absen && $absen->jam_masuk)
                                            <span
                                                class="inline-flex items-center gap-1 px-2 py-1 rounded bg-dark-50 text-xs font-mono text-dark-600 border border-dark-100">
                                                <i class='bx bx-time-five'></i>
                                                {{ date('H:i', strtotime($absen->jam_masuk)) }}
                                            </span>
                                        @else
                                            <span class="text-xs text-dark-400 italic">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <button type="button"
                                                data-peserta-id="{{ $s->id }}"
                                                data-status="{{ $absen ? $absen->status : '' }}"
                                                data-keterangan="{{ $absen ? addslashes($absen->keterangan) : '' }}"
                                                data-nama="{{ addslashes($s->nama) }}"
                                                onclick="openEditAbsensi(this)"
                                                class="edit-absensi-btn inline-flex items-center justify-center w-8 h-8 rounded-lg bg-primary-50 text-primary-600 hover:bg-primary-100 transition-colors"
                                                title="Edit Kehadiran">
                                                <i class='bx bx-edit-alt text-lg'></i>
                                            </button>
                                            <form action="{{ route('absensi.harian.delete') }}" method="POST" class="inline delete-harian-form" id="form-delete-{{ $s->id }}">
                                                @csrf
                                                <input type="hidden" name="peserta_id" value="{{ $s->id }}">
                                                <button type="button"
                                                    class="delete-confirm-harian inline-flex items-center justify-center w-8 h-8 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-100 transition-colors {{ !$absen ? 'opacity-50 cursor-not-allowed' : '' }}"
                                                    title="Hapus Kehadiran" {{ !$absen ? 'disabled' : '' }}>
                                                    <i class='bx bx-trash text-lg'></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center text-dark-500">Tidak ada peserta.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if ($pesertas->hasPages())
                    <div class="px-6 py-4 border-t border-dark-100 bg-dark-50/30">
                        {{ $pesertas->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Modal QR Scanner --}}
    <div id="modal-scanner" class="fixed inset-0 z-[100] hidden" style="position: fixed; z-index: 9999;">
        <div class="fixed inset-0 bg-dark-950/80 backdrop-blur-sm transition-opacity" onclick="closeScannerModal()">
        </div>
        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div
                    class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-md">
                    <div class="bg-dark-50/50 px-6 py-4 border-b border-dark-100 flex items-center justify-between">
                        <h3 class="text-lg font-bold text-dark-800 flex items-center gap-2">
                            <i class='bx bx-qr-scan text-primary-600 text-xl'></i>
                            Scan Kartu Peserta
                        </h3>
                        <button type="button" onclick="closeScannerModal()"
                            class="text-dark-400 hover:text-rose-500 transition-colors">
                            <i class='bx bx-x text-2xl'></i>
                        </button>
                    </div>
                    <div class="px-6 pt-4 pb-0 text-left">
                        <label class="block text-sm font-medium text-dark-700 mb-1.5">Pilih Jadwal Aktif</label>
                        <select id="scan_jadwal_id" class="block w-full px-3 py-2 border border-dark-200 rounded-xl bg-dark-50/50 focus:bg-white text-dark-800 focus:outline-none focus:ring-2 focus:ring-primary-500/30 focus:border-primary-500 transition-all duration-200 mb-2">
                            <option value="">-- Pilih Jadwal --</option>
                            @foreach ($jadwalHariIni as $jdwl)
                                <option value="{{ $jdwl->id }}">{{ $jdwl->kelas->nama_kelas }} - {{ $jdwl->mata_pelajaran }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="p-6 text-center pt-2">
                        <div id="qr-reader"
                            class="w-full mb-4 overflow-hidden rounded-xl border border-dark-200 shadow-inner"
                            style="min-height: 250px;"></div>
                        <p class="text-sm text-dark-500" id="qr-instructions">
                            Arahkan kamera ke QR code pada kartu ID peserta untuk mencatat kehadiran.
                        </p>
                        {{-- Scan result area --}}
                        <div id="scan-result" class="mt-4 hidden">
                            <div id="scan-result-content"
                                class="p-4 rounded-xl border flex items-center gap-3 text-left">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Edit Absensi --}}
    @push('modals')
        <div id="modal-editabsensi" class="fixed inset-0 z-[60] hidden">
            {{-- Backdrop --}}
            <div class="fixed inset-0 bg-dark-950/50 backdrop-blur-sm transition-opacity"
                onclick="closeModal('modal-editabsensi')"></div>

            {{-- Modal Panel --}}
            <div class="fixed inset-0 z-10 overflow-y-auto">
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <div
                        class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-md fade-in">
                        {{-- Header --}}
                        <div class="bg-dark-50/50 px-6 py-4 border-b border-dark-100 flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-dark-800 flex items-center gap-2">
                                <i class='bx bx-edit-alt text-primary-600 text-xl'></i>
                                Edit Absensi
                            </h3>
                            <button type="button" onclick="closeModal('modal-editabsensi')"
                                class="text-dark-400 hover:text-dark-600 transition-colors">
                                <i class='bx bx-x text-2xl'></i>
                            </button>
                        </div>

                        {{-- Body --}}
                        <div class="px-6 py-5">
                            <form action="{{ route('absensi.harian.update') }}" method="POST">
                                @csrf
                                <input type="hidden" name="peserta_id" id="edit_peserta_id">

                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-dark-700 mb-1.5">Nama Peserta</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class='bx bx-user text-dark-400 text-lg'></i>
                                        </div>
                                        <input type="text" id="edit_nama_peserta" readonly
                                            class="block w-full pl-10 pr-3 py-2.5 border border-dark-200 rounded-xl bg-dark-50/50 text-dark-800 focus:outline-none transition-all duration-200">
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-dark-700 mb-1.5">Status
                                        Kehadiran</label>
                                    <select name="status" id="edit_status" required
                                        class="block w-full px-3 py-2.5 border border-dark-200 rounded-xl bg-dark-50/50 focus:bg-white text-dark-800 focus:outline-none focus:ring-2 focus:ring-primary-500/30 focus:border-primary-500 transition-all duration-200">
                                        <option value="Hadir">Hadir</option>
                                        <option value="Izin">Izin</option>
                                        <option value="Sakit">Sakit</option>
                                        <option value="Alfa">Alfa</option>
                                    </select>
                                </div>

                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-dark-700 mb-1.5">Keterangan
                                        (Opsional)</label>
                                    <textarea name="keterangan" id="edit_keterangan" rows="3"
                                        class="block w-full px-3 py-2.5 border border-dark-200 rounded-xl bg-dark-50/50 focus:bg-white text-dark-800 focus:outline-none focus:ring-2 focus:ring-primary-500/30 focus:border-primary-500 transition-all duration-200"
                                        placeholder="Ketik alasan perubahan..."></textarea>
                                </div>

                                {{-- Actions --}}
                                <div class="mt-6 flex items-center justify-end gap-3">
                                    <button type="button" onclick="closeModal('modal-editabsensi')"
                                        class="px-4 py-2 border border-dark-200 rounded-xl text-dark-600 bg-white hover:bg-dark-50 hover:text-dark-800 font-medium transition-colors duration-200">
                                        Batal
                                    </button>
                                    <button type="submit"
                                        class="px-4 py-2 bg-gradient-to-r from-primary-600 to-primary-700 hover:from-primary-700 hover:to-primary-800 text-white font-medium rounded-xl shadow-lg shadow-primary-500/30 hover:shadow-primary-500/40 transition-all duration-200 flex items-center gap-2">
                                        <i class='bx bx-save text-lg'></i>
                                        Simpan Perubahan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endpush

@endsection

@push('scripts')
    <!-- HTML5 QR Code -->
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

    <script>
        let html5Qrcode = null;
        const modalScanner = document.getElementById('modal-scanner');

        // === QR Scanner Functions ===
        function openScannerModal() {
            modalScanner.classList.remove('hidden');
            document.getElementById('scan-result').classList.add('hidden');
            document.getElementById('qr-instructions').innerHTML =
                'Arahkan kamera ke QR code pada kartu ID peserta untuk mencatat kehadiran.';

            if (!html5Qrcode) {
                let isProcessingScan = false;

                function onScanSuccess(decodedText, decodedResult) {
                    if (isProcessingScan) return;
                    isProcessingScan = true;

                    if (html5Qrcode) {
                        try {
                            html5Qrcode.pause();
                        } catch (e) {}
                    }

                    let jadwalId = document.getElementById('scan_jadwal_id').value;
                    if (!jadwalId) {
                        document.getElementById('qr-instructions').innerHTML = `<span class="text-rose-500"><i class='bx bx-error-circle'></i> Pilih jadwal terlebih dahulu.</span>`;
                        isProcessingScan = false;
                        if (html5Qrcode) {
                            try { html5Qrcode.resume(); } catch (e) {}
                        }
                        return;
                    }

                    document.getElementById('qr-instructions').innerHTML =
                        `<i class='bx bx-loader-alt animate-spin text-xl'></i> Memverifikasi data...`;

                    // Send to backend
                    fetch("{{ route('absensi.store') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                    .getAttribute('content'),
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                qrcode: decodedText,
                                jadwal_id: jadwalId
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            const resultDiv = document.getElementById('scan-result');
                            const resultContent = document.getElementById('scan-result-content');
                            resultDiv.classList.remove('hidden');

                            if (data.success) {
                                resultContent.className =
                                    'p-4 rounded-xl border border-emerald-200 bg-emerald-50 flex items-center gap-3 text-left';
                                resultContent.innerHTML = `
                                    <div class="w-10 h-10 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center flex-shrink-0">
                                        <i class='bx bx-check-circle text-2xl'></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-emerald-700">${data.message}</p>
                                        <p class="text-xs text-emerald-600">Kelas: ${data.peserta_kelas}</p>
                                    </div>
                                `;

                                updateAbsensiRow(data.peserta_id, data.peserta_nama, data.peserta_kelas, data.jam_masuk, 'Hadir', data.keterangan);

                                document.getElementById('qr-instructions').innerHTML =
                                    '<span class="text-emerald-600 font-semibold"><i class=\'bx bx-check\'></i> Berhasil!</span> Klik scan lagi untuk peserta berikutnya.';

                                // Auto resume scanner after 2 seconds
                                setTimeout(() => {
                                    isProcessingScan = false;
                                    if (html5Qrcode) {
                                        try {
                                            html5Qrcode.resume();
                                        } catch (e) {}
                                    }
                                    document.getElementById('qr-instructions').innerHTML =
                                        'Arahkan kamera ke QR code peserta berikutnya.';
                                }, 2500);

                            } else {
                                resultContent.className =
                                    'p-4 rounded-xl border border-rose-200 bg-rose-50 flex items-center gap-3 text-left';
                                resultContent.innerHTML = `
                                    <div class="w-10 h-10 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center flex-shrink-0">
                                        <i class='bx bx-error-circle text-2xl'></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-rose-700">${data.message}</p>
                                    </div>
                                `;

                                document.getElementById('qr-instructions').innerHTML =
                                    '<span class="text-rose-500"><i class=\'bx bx-error\'></i> Gagal.</span> Coba scan kartu lain.';

                                setTimeout(() => {
                                    isProcessingScan = false;
                                    if (html5Qrcode) {
                                        try {
                                            html5Qrcode.resume();
                                        } catch (e) {}
                                    }
                                }, 2000);
                            }
                        })
                        .catch(error => {
                            console.error(error);
                            document.getElementById('qr-instructions').innerHTML =
                                `<span class="text-rose-500"><i class='bx bx-error-circle'></i> Terjadi kesalahan jaringan.</span>`;
                            isProcessingScan = false;
                            if (html5Qrcode) {
                                try {
                                    html5Qrcode.resume();
                                } catch (e) {}
                            }
                        });
                }

                html5Qrcode = new Html5Qrcode("qr-reader");
                html5Qrcode.start({
                    facingMode: "environment"
                }, {
                    fps: 20,
                    qrbox: {
                        width: 300,
                        height: 300
                    },
                    aspectRatio: 1.0,
                    formats: ["QR_CODE"]
                }, onScanSuccess).catch(err => {
                    console.error(err);
                    document.getElementById('qr-instructions').innerHTML =
                        `<span class="text-rose-500"><i class='bx bx-error-circle'></i> Akses kamera ditolak atau tidak ditemukan. Pastikan memberi izin kamera pada browser.</span>`;
                });
            }
        }

        function closeScannerModal() {
            modalScanner.classList.add('hidden');
            if (html5Qrcode) {
                html5Qrcode.stop().then(() => {
                    html5Qrcode.clear();
                    html5Qrcode = null;
                }).catch(err => console.error(err));
            }
        }

        // === Modal Edit Functions ===
        function openModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }
        }

        function closeModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.add('hidden');
                document.body.style.overflow = '';
            }
        }

        function updateAbsensiRow(pesertaId, pesertaNama, pesertaKelas, jamMasuk, status, keterangan) {
            const row = document.getElementById('peserta-row-' + pesertaId);
            if (!row) return;

            const statusCell = document.getElementById('absensi-status-' + pesertaId);
            const jamCell = document.getElementById('absensi-jam-' + pesertaId);
            const button = row.querySelector('.edit-absensi-btn');

            if (statusCell) {
                statusCell.innerHTML = `
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        <i class='bx bx-check-shield text-sm'></i> Hadir
                    </span>
                `;
            }

            if (jamCell) {
                jamCell.innerHTML = `
                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded bg-dark-50 text-xs font-mono text-dark-600 border border-dark-100">
                        <i class='bx bx-time-five'></i>
                        ${jamMasuk ? jamMasuk.substring(0,5) : '-'}
                    </span>
                `;
            }

            if (button) {
                button.dataset.status = status;
                button.dataset.keterangan = keterangan;
                button.dataset.nama = pesertaNama;
            }

            const deleteForm = document.getElementById('form-delete-' + pesertaId);
            if (deleteForm) {
                deleteForm.classList.remove('hidden');
                const deleteBtn = deleteForm.querySelector('button');
                if (deleteBtn) {
                    deleteBtn.removeAttribute('disabled');
                    deleteBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                }
            }
        }

        function openEditAbsensi(button) {
            const pesertaId = button.dataset.pesertaId;
            const status = button.dataset.status || '';
            const keterangan = button.dataset.keterangan || '';
            const namaPeserta = button.dataset.nama || '';

            document.getElementById('edit_peserta_id').value = pesertaId;
            document.getElementById('edit_nama_peserta').value = namaPeserta;

            if (status !== '') {
                let selectStatus = document.getElementById('edit_status');
                for (let i = 0; i < selectStatus.options.length; i++) {
                    if (selectStatus.options[i].value.toLowerCase() === status.toLowerCase()) {
                        selectStatus.selectedIndex = i;
                        break;
                    }
                }
            } else {
                document.getElementById('edit_status').value = 'Hadir';
            }

            document.getElementById('edit_keterangan').value = keterangan && keterangan !== '-' ? keterangan : '';

            openModal('modal-editabsensi');
        }

        // Close modal on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeModal('modal-editabsensi');
                closeScannerModal();
            }
        });

        // Delete Confirmation
        document.querySelectorAll('.delete-confirm-harian').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const form = this.closest('form');
                Swal.fire({
                    title: 'Hapus Data Absensi?',
                    text: "Data kehadiran peserta untuk hari ini akan dihapus!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal',
                    reverseButtons: true,
                    customClass: {
                        popup: 'font-inter rounded-2xl',
                        confirmButton: 'rounded-xl',
                        cancelButton: 'rounded-xl'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script>
@endpush
