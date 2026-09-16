@extends('layouts.app')

@section('title', 'Data Jadwal Pelajaran')
@section('page-title', 'Data Jadwal Pelajaran')
@section('page-subtitle', 'Kelola data Jadwal Pelajaran')

@section('breadcrumb')
    <li><i class='bx bx-chevron-right text-dark-400'></i></li>
    <li class="text-primary-600 font-medium">Data Jadwal Pelajaran</li>
@endsection

@section('page-header')
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-dark-800">Daftar Jadwal Pelajaran</h2>
            <p class="text-sm text-dark-400 mt-1">Kelola data Jadwal Pelajaran yang ada di sekolah.</p>
        </div>
        <button onclick="openModal('modal-inputjadwal')"
            class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-primary-600 to-primary-700 hover:from-primary-700 hover:to-primary-800 text-white text-sm font-medium rounded-xl shadow-lg shadow-primary-500/30 hover:shadow-primary-500/40 transition-all duration-200 hover:-translate-y-0.5">
            <i class='bx bx-plus text-lg'></i>
            Tambah Data
        </button>
    </div>
@endsection

@section('content')
    <div class="glass-card rounded-2xl border border-dark-200/50 overflow-hidden">
        {{-- Filter Bar --}}
        <div class="px-6 py-4 border-b border-dark-100 bg-dark-50/30">
            <form action="{{ route('jadwal') }}" method="GET" class="flex flex-col sm:flex-row items-end gap-3">
                <div class="w-full sm:w-44">
                    <label class="block text-xs font-medium text-dark-600 mb-1">Hari</label>
                    <select name="hari"
                        class="block w-full px-3 py-2 border border-dark-200 rounded-xl bg-white text-sm text-dark-800 focus:outline-none focus:ring-2 focus:ring-primary-500/30 focus:border-primary-500 transition-all">
                        <option value="">Semua Hari</option>
                        @foreach (['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'] as $h)
                            <option value="{{ $h }}" {{ $filter_hari == $h ? 'selected' : '' }}>
                                {{ $h }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="w-full sm:w-44">
                    <label class="block text-xs font-medium text-dark-600 mb-1">Kelas</label>
                    <select name="kelas_id"
                        class="block w-full px-3 py-2 border border-dark-200 rounded-xl bg-white text-sm text-dark-800 focus:outline-none focus:ring-2 focus:ring-primary-500/30 focus:border-primary-500 transition-all">
                        <option value="">Semua Kelas</option>
                        @foreach ($kelas as $k)
                            <option value="{{ $k->id }}" {{ $filter_kelas == $k->id ? 'selected' : '' }}>
                                {{ $k->nama_kelas }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit"
                    class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-xl transition-colors flex items-center gap-1.5 whitespace-nowrap">
                    <i class='bx bx-filter-alt'></i> Filter
                </button>
                @if ($filter_hari || $filter_kelas)
                    <a href="{{ route('jadwal') }}"
                        class="px-4 py-2 border border-dark-200 rounded-xl text-sm text-dark-600 hover:bg-dark-50 transition-colors whitespace-nowrap">
                        Reset
                    </a>
                @endif
            </form>
        </div>

        {{-- Table Container --}}
        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gradient-to-r from-primary-600 to-primary-700 text-white border-b border-primary-800">
                        <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider w-16 text-center">
                            No</th>
                        <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider">Hari</th>
                        <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-center">Jam
                        </th>
                        <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider">Kelas</th>
                        <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-center">Tutor
                        </th>
                        <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-center">Mata Pelajaran</th>
                        <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-center">Ruangan</th>
                        <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-center w-28">
                            Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-dark-200/50">
                    @forelse ($jadwal as $s)
                        <tr class="hover:bg-gradient-to-r hover:from-primary-50 hover:to-blue-50 transition-all duration-200 border-l-4 border-l-primary-500">
                            <td class="px-6 py-4 text-sm font-semibold text-primary-600 text-center font-mono">
                                {{ $jadwal->firstItem() + $loop->index }}</td>
                            <td class="px-6 py-4 text-sm text-dark-800 font-medium whitespace-nowrap">
                                {{ $s->tanggal ? \Carbon\Carbon::parse($s->tanggal)->isoFormat('DD MMM YYYY') : '-' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-dark-600">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-amber-50 text-amber-700 text-xs font-medium border border-amber-200">
                                    <i class='bx bx-calendar text-sm'></i>
                                    {{ $s->hari }}
                                </span>
                            </td>

                            <td class="px-6 py-4 text-sm text-center">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-50 text-emerald-700 font-semibold text-xs border border-emerald-200 whitespace-nowrap">
                                    <i class='bx bx-time text-sm'></i>
                                    {{ \Carbon\Carbon::parse($s->jam_mulai)->format('H:i') }} -
                                    {{ \Carbon\Carbon::parse($s->jam_selesai)->format('H:i') }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm font-medium text-dark-800">
                                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-lg bg-blue-50 text-blue-700 border border-blue-200 text-xs font-semibold">
                                    <i class='bx bx-building text-sm'></i>
                                    {{ $s->kelas->nama_kelas }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm font-medium text-dark-800 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <div class="w-6 h-6 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center font-bold text-xs">
                                        {{ strtoupper(substr($s->tutor->nama, 0, 1)) }}
                                    </div>
                                    <span class="text-xs whitespace-nowrap">{{ $s->tutor->nama }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm font-medium text-dark-800 text-center">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-purple-50 text-purple-700 text-xs font-medium border border-purple-200">
                                    <i class='bx bx-book text-sm'></i>
                                    {{ $s->mata_pelajaran }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-dark-800 text-center">
                                {{ $s->ruangan ?? '-' }}
                            </td>

                            <td class="px-6 py-4 text-sm text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <button onclick="editJadwal('{{ $s->id }}')"
                                        class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-indigo-600 bg-indigo-50 hover:bg-indigo-100 hover:text-indigo-700 hover:shadow-md transition-all duration-200"
                                        title="Edit Data">
                                        <i class='bx bx-edit-alt text-lg'></i>
                                    </button>
                                    <form action="{{ route('jadwal.delete', ['id' => $s->id]) }}" method="POST"
                                        class="inline">
                                        @csrf
                                        <button type="button"
                                            class="delete-confirm inline-flex items-center justify-center w-8 h-8 rounded-lg text-rose-600 bg-rose-50 hover:bg-rose-100 hover:text-rose-700 hover:shadow-md transition-all duration-200"
                                            title="Hapus Data">
                                            <i class='bx bx-trash text-lg'></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-16 h-16 mb-4 rounded-full bg-dark-100 flex items-center justify-center">
                                        <i class='bx bx-calendar-x text-3xl text-dark-400'></i>
                                    </div>
                                    <h3 class="text-sm font-medium text-dark-900">Belum ada data jadwal</h3>
                                    <p class="mt-1 text-sm text-dark-500">Silakan tambah data jadwal baru.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($jadwal->hasPages())
            <div class="px-6 py-4 border-t border-dark-100 bg-dark-50/30">
                {{ $jadwal->links() }}
            </div>
        @endif
    </div>

    @push('modals')
        {{-- Modal Tambah jadwal --}}
        <div id="modal-inputjadwal" class="fixed inset-0 z-[60] hidden">
            {{-- Backdrop --}}
            <div class="fixed inset-0 bg-dark-950/50 backdrop-blur-sm transition-opacity"
                onclick="closeModal('modal-inputjadwal')"></div>

            {{-- Modal Panel --}}
            <div class="fixed inset-0 z-10 overflow-y-auto">
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <div
                        class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-2xl fade-in">
                        {{-- Header --}}
                        <div class="bg-dark-50/50 px-6 py-4 border-b border-dark-100 flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-dark-800 flex items-center gap-2">
                                <i class='bx bx-calendar-plus text-primary-600 text-xl'></i>
                                Tambah Data Jadwal Pelajaran
                            </h3>
                            <button type="button" onclick="closeModal('modal-inputjadwal')"
                                class="text-dark-400 hover:text-dark-600 transition-colors">
                                <i class='bx bx-x text-2xl'></i>
                            </button>
                        </div>

                        {{-- Body --}}
                        <div class="px-6 py-5">
                            <form action="{{ route('jadwal.store') }}" method="POST" id="frmJadwal">
                                @csrf
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    {{-- Pilih tutor --}}
                                    <div>
                                        <label for="tutor_id" class="block text-sm font-medium text-dark-700 mb-1.5">Tutor
                                            Pengajar</label>
                                        <select name="tutor_id" id="tutor_id" required onchange="updateMapel()"
                                            class="block w-full px-3 py-2.5 border border-dark-200 rounded-xl bg-dark-50/50 focus:bg-white text-dark-800 focus:outline-none focus:ring-2 focus:ring-primary-500/30 focus:border-primary-500 transition-all duration-200">
                                            <option value="" data-mapel="">-- Pilih Tutor --</option>
                                            @foreach ($tutor as $g)
                                                {{-- Simpan data mapel di atribut data-mapel --}}
                                                <option value="{{ $g->id }}" data-mapel="{{ $g->mapel }}">
                                                    {{ $g->nama }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    {{-- Mata Pelajaran (Otomatis terisi) --}}
                                    <div class="md:col-span-2">
                                        <label for="mata_pelajaran" class="block text-sm font-medium text-dark-700 mb-1.5">Mata
                                            Pelajaran</label>
                                        <select name="mata_pelajaran" id="mata_pelajaran" required
                                            class="block w-full px-3 py-2.5 border border-dark-200 rounded-xl bg-white focus:outline-none focus:ring-2 focus:ring-primary-500/30 focus:border-primary-500 transition-all duration-200">
                                            <option value="">Pilih tutor terlebih dahulu</option>
                                        </select>
                                    </div>

                                    {{-- Pilih Kelas --}}
                                    <div>
                                        <label for="kelas_id"
                                            class="block text-sm font-medium text-dark-700 mb-1.5">Kelas</label>
                                        <select name="kelas_id" id="kelas_id" required
                                            class="block w-full px-3 py-2.5 border border-dark-200 rounded-xl bg-dark-50/50 focus:bg-white text-dark-800 focus:outline-none focus:ring-2 focus:ring-primary-500/30 focus:border-primary-500 transition-all duration-200">
                                            <option value="">-- Pilih Kelas --</option>
                                            @foreach ($kelas as $k)
                                                <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    {{-- Tanggal --}}
                                    <div>
                                        <label for="tanggal"
                                            class="block text-sm font-medium text-dark-700 mb-1.5">Tanggal</label>
                                        <input type="text" name="tanggal" id="tanggal" placeholder="Pilih Tanggal"
                                            class="datepicker block w-full px-3 py-2.5 border border-dark-200 rounded-xl bg-dark-50/50 focus:bg-white text-dark-800 focus:outline-none focus:ring-2 focus:ring-primary-500/30 focus:border-primary-500 transition-all duration-200">
                                    </div>

                                    {{-- Hari --}}
                                    <div>
                                        <label for="hari"
                                            class="block text-sm font-medium text-dark-700 mb-1.5">Hari</label>
                                        <select name="hari" id="hari" required
                                            class="block w-full px-3 py-2.5 border border-dark-200 rounded-xl bg-dark-50/50 focus:bg-white text-dark-800 focus:outline-none focus:ring-2 focus:ring-primary-500/30 focus:border-primary-500 transition-all duration-200">
                                            <option value="">-- Pilih Hari --</option>
                                            <option value="Senin">Senin</option>
                                            <option value="Selasa">Selasa</option>
                                            <option value="Rabu">Rabu</option>
                                            <option value="Kamis">Kamis</option>
                                            <option value="Jumat">Jumat</option>
                                            <option value="Sabtu">Sabtu</option>
                                            <option value="Minggu">Minggu</option>
                                        </select>
                                    </div>

                                    {{-- Ruangan --}}
                                    <div class="md:col-span-2">
                                        <label for="ruangan"
                                            class="block text-sm font-medium text-dark-700 mb-1.5">Ruangan</label>
                                        <input type="text" name="ruangan" id="ruangan" placeholder="Contoh: A1, Lab Komputer"
                                            class="block w-full px-3 py-2.5 border border-dark-200 rounded-xl bg-white focus:outline-none focus:ring-2 focus:ring-primary-500/30 focus:border-primary-500 transition-all duration-200">
                                    </div>

                                    {{-- Jam Mulai --}}
                                    <div>
                                        <label for="jam_mulai" class="block text-sm font-medium text-dark-700 mb-1.5">Jam
                                            Mulai</label>
                                        <div class="relative">
                                            <input type="text" name="jam_mulai" id="jam_mulai" placeholder="--:--"
                                                readonly class="timepicker block w-full px-3 py-2.5 border ...">
                                        </div>
                                        <p class="mt-1 text-xs text-dark-400">*Format 24 Jam (Contoh: 07:30, 14:00)</p>
                                    </div>

                                    {{-- Jam Selesai --}}
                                    <div>
                                        <label for="jam_selesai" class="block text-sm font-medium text-dark-700 mb-1.5">Jam
                                            Selesai</label>
                                        <div class="relative">
                                            <input type="text" name="jam_selesai" id="jam_selesai" placeholder="--:--"
                                                readonly class="timepicker block w-full px-3 py-2.5 border ...">
                                        </div>
                                    </div>
                                </div>

                                {{-- Actions --}}
                                <div class="mt-8 flex items-center justify-end gap-3">
                                    <button type="button" onclick="closeModal('modal-inputjadwal')"
                                        class="px-4 py-2 border border-dark-200 rounded-xl text-dark-600 bg-white hover:bg-dark-50 hover:text-dark-800 font-medium transition-colors duration-200">
                                        Batal
                                    </button>
                                    <button type="submit"
                                        class="px-4 py-2 bg-gradient-to-r from-primary-600 to-primary-700 hover:from-primary-700 hover:to-primary-800 text-white font-medium rounded-xl shadow-lg transition-all duration-200 flex items-center gap-2">
                                        <i class='bx bx-save text-lg'></i>
                                        Simpan Jadwal
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Modal Edit jadwal --}}
        <div id="modal-editjadwal" class="fixed inset-0 z-[60] hidden">
            {{-- Backdrop --}}
            <div class="fixed inset-0 bg-dark-950/50 backdrop-blur-sm transition-opacity"
                onclick="closeModal('modal-editjadwal')"></div>

            {{-- Modal Panel --}}
            <div class="fixed inset-0 z-10 overflow-y-auto">
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <div
                        class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-2xl fade-in">
                        {{-- Header --}}
                        <div class="bg-dark-50/50 px-6 py-4 border-b border-dark-100 flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-dark-800 flex items-center gap-2">
                                <i class='bx bx-edit-alt text-primary-600 text-xl'></i>
                                Edit Data Jadwal
                            </h3>
                            <button type="button" onclick="closeModal('modal-editjadwal')"
                                class="text-dark-400 hover:text-dark-600 transition-colors">
                                <i class='bx bx-x text-2xl'></i>
                            </button>
                        </div>

                        {{-- Body (Loaded via AJAX) --}}
                        <div class="px-6 py-5" id="loadeditform">

                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endpush

@endsection

@push('scripts')
    <script>
        flatpickr(".datepicker", {
            dateFormat: "Y-m-d",
            allowInput: false
        });

        flatpickr(".timepicker", {
            enableTime: true,
            noCalendar: true,
            dateFormat: "H:i",
            time_24hr: true
        });

        function updateMapel() {
            const selectTutor = document.getElementById('tutor_id');
            const selectMapel = document.getElementById('mata_pelajaran');

            const selectedOption = selectTutor.options[selectTutor.selectedIndex];
            const mapelString = selectedOption.getAttribute('data-mapel') || '';

            // Clear existing options
            selectMapel.innerHTML = '<option value="">-- Pilih Mata Pelajaran --</option>';

            if (mapelString) {
                // Split by comma and remove whitespace
                const mapels = mapelString.split(',').map(m => m.trim()).filter(m => m !== '');
                mapels.forEach(mapel => {
                    const option = document.createElement('option');
                    option.value = mapel;
                    option.textContent = mapel;
                    selectMapel.appendChild(option);
                });
            }
        }

        function openModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';

                // Focus otomatis pada input pertama (Mata Pelajaran)
                if (modalId === 'modal-inputjadwal') {
                    setTimeout(() => {
                        document.getElementById('mata_pelajaran').focus();
                    }, 100);
                }
            }
        }

        function closeModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.add('hidden');
                document.body.style.overflow = '';
            }
        }

        // Validasi Form Tambah Jadwal
        document.getElementById('frmJadwal').addEventListener('submit', function(e) {
            const mapel = document.getElementById('mata_pelajaran').value;
            const tutor = document.getElementById('tutor_id').value;
            const kelas = document.getElementById('kelas_id').value;
            const jamMulai = document.getElementById('jam_mulai').value;
            const jamSelesai = document.getElementById('jam_selesai').value;

            if (!mapel || !tutor || !kelas || !jamMulai || !jamSelesai) {
                e.preventDefault();
                Swal.fire({
                    title: 'Peringatan!',
                    text: 'Semua data jadwal wajib diisi!',
                    icon: 'warning',
                    confirmButtonColor: '#10b981',
                    customClass: {
                        popup: 'font-inter rounded-2xl'
                    }
                });
                return;
            }

            if (jamSelesai <= jamMulai) {
                e.preventDefault();
                Swal.fire({
                    title: 'Input Tidak Valid',
                    text: 'Jam selesai harus lebih besar dari jam mulai!',
                    icon: 'error',
                    confirmButtonColor: '#ef4444'
                });
            }
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeModal('modal-inputjadwal');
            }
        });

        // Modal Edit Call
        function editJadwal(id) {
            // Tampilkan loading (opsional)
            document.getElementById('loadeditform').innerHTML =
                '<div class="text-center py-5"><i class="bx bx-loader-alt bx-spin text-3xl text-primary-600"></i></div>';
            openModal('modal-editjadwal');

            fetch('/jadwal/edit', { // Pastikan route ini sesuai di web.php
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        id: id
                    })
                })
                .then(response => response.text())
                .then(html => {
                    document.getElementById('loadeditform').innerHTML = html;

                    // Inisialisasi ulang flatpickr
                    flatpickr(".datepicker", {
                        dateFormat: "Y-m-d",
                        allowInput: false
                    });
                    
                    flatpickr(".timepicker", {
                        enableTime: true,
                        noCalendar: true,
                        dateFormat: "H:i",
                        time_24hr: true
                    });
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Gagal memuat data.'
                    });
                });
        }

        function attachEditFormListener(form) {
            form.addEventListener('submit', function(e) {
                const namaKelas = document.getElementById('nama_kelas_edit').value;
                const waliKelas = document.getElementById('wali_kelas_edit').value;

                if (!namaKelas || !waliKelas) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Oops!',
                        text: 'Nama Kelas dan Wali Kelas wajib diisi!',
                        icon: 'warning',
                        confirmButtonColor: '#6366f1',
                        customClass: {
                            popup: 'font-inter rounded-2xl'
                        }
                    });
                }
            });
        }

        document.addEventListener('click', function(e) {
            if (e.target.closest('.delete-confirm')) {
                e.preventDefault();
                const button = e.target.closest('.delete-confirm');
                const form = button.closest('form');

                Swal.fire({
                    title: 'Hapus Jadwal?',
                    text: "Data jadwal ini akan dihapus secara permanen!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444', // Warna Merah (Rose-600)
                    cancelButtonColor: '#64748b', // Warna Abu-abu
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal',
                    reverseButtons: true,
                    customClass: {
                        popup: 'font-inter rounded-2xl',
                        confirmButton: 'px-6 py-2.5 rounded-xl',
                        cancelButton: 'px-6 py-2.5 rounded-xl'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            }
        });

        document.addEventListener('change', function(e) {
            if (e.target && e.target.id === 'tutor_id_edit') {
                const selectTutor = e.target;
                const selectMapel = document.getElementById('mata_pelajaran_edit');

                // Ambil atribut data-mapel dari option yang dipilih
                const selectedOption = selectTutor.options[selectTutor.selectedIndex];
                const mapelString = selectedOption.getAttribute('data-mapel') || '';

                if (selectMapel) {
                    const currentSelectedMapel = selectMapel.value;
                    selectMapel.innerHTML = '<option value="">Pilih Mata Pelajaran</option>';

                    if (mapelString) {
                        const mapels = mapelString.split(',').map(m => m.trim()).filter(m => m !== '');
                        mapels.forEach(mapel => {
                            const option = document.createElement('option');
                            option.value = mapel;
                            option.textContent = mapel;
                            if (mapel === currentSelectedMapel) {
                                option.selected = true;
                            }
                            selectMapel.appendChild(option);
                        });
                    }
                }
            }
        });
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeModal('modal-inputkelas');
                closeModal('modal-editkelas');
            }
        });
    </script>
@endpush
