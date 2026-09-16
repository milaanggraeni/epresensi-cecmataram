<form action="{{ route('peserta.update', ['id' => $peserta->id]) }}" method="POST" id="frmEditPeserta" enctype="multipart/form-data">
    @csrf
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

        <div>
            <label for="nama_edit" class="block text-sm font-medium text-dark-700 mb-1.5">Nama Lengkap</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class='bx bx-user text-dark-400 text-lg'></i>
                </div>
                <input type="text" name="nama" id="nama_edit" value="{{ $peserta->nama }}"
                    class="block w-full pl-10 pr-3 py-2.5 border border-dark-200 rounded-xl bg-white focus:bg-white text-dark-800 focus:outline-none focus:ring-2 focus:ring-primary-500/30 focus:border-primary-500 transition-all duration-200"
                    placeholder="Nama Lengkap" required autocomplete="off">
            </div>
        </div>

        <div>
            <label for="jenis_kelamin_edit" class="block text-sm font-medium text-dark-700 mb-1.5">Jenis Kelamin</label>
            <select name="jenis_kelamin" id="jenis_kelamin_edit" required
                class="block w-full px-3 py-2.5 border border-dark-200 rounded-xl bg-white focus:bg-white text-dark-800 focus:outline-none focus:ring-2 focus:ring-primary-500/30 focus:border-primary-500 transition-all duration-200">
                <option value="L" {{ $peserta->jenis_kelamin == 'L' ? 'selected' : '' }}>Laki-laki</option>
                <option value="P" {{ $peserta->jenis_kelamin == 'P' ? 'selected' : '' }}>Perempuan</option>
            </select>
        </div>

        <div>
            <label for="kelas_id_edit" class="block text-sm font-medium text-dark-700 mb-1.5">Kelas</label>
            <select name="kelas_id" id="kelas_id_edit" required
                class="block w-full px-3 py-2.5 border border-dark-200 rounded-xl bg-white focus:bg-white text-dark-800 focus:outline-none focus:ring-2 focus:ring-primary-500/30 focus:border-primary-500 transition-all duration-200">
                @foreach ($kelas as $k)
                    <option value="{{ $k->id }}" {{ $peserta->kelas_id == $k->id ? 'selected' : '' }}>
                        {{ $k->nama_kelas }}</option>
                @endforeach
            </select>
        </div>

        <div class="md:col-span-2">
            <label for="alamat_edit" class="block text-sm font-medium text-dark-700 mb-1.5">Alamat</label>
            <textarea name="alamat" id="alamat_edit" rows="3"
                class="block w-full px-3 py-2.5 border border-dark-200 rounded-xl bg-white focus:bg-white text-dark-800 focus:outline-none focus:ring-2 focus:ring-primary-500/30 focus:border-primary-500 transition-all duration-200"
                placeholder="Alamat lengkap (opsional)">{{ $peserta->alamat }}</textarea>
        </div>

        <div>
            <label for="nama_wali_edit" class="block text-sm font-medium text-dark-700 mb-1.5">Nama Wali</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class='bx bx-user-circle text-dark-400 text-lg'></i>
                </div>
                <input type="text" name="nama_wali" id="nama_wali_edit" value="{{ $peserta->nama_wali }}"
                    class="block w-full pl-10 pr-3 py-2.5 border border-dark-200 rounded-xl bg-white focus:bg-white text-dark-800 focus:outline-none focus:ring-2 focus:ring-primary-500/30 focus:border-primary-500 transition-all duration-200"
                    placeholder="Nama Orang Tua/Wali (opsional)">
            </div>
        </div>

        <div>
            <label for="nomor_hp_wali_edit" class="block text-sm font-medium text-dark-700 mb-1.5">No. HP Wali (WhatsApp)</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class='bx bxl-whatsapp text-dark-400 text-lg'></i>
                </div>
                <input type="text" name="nomor_hp_wali" id="nomor_hp_wali_edit" value="{{ $peserta->nomor_hp_wali }}"
                    class="block w-full pl-10 pr-3 py-2.5 border border-dark-200 rounded-xl bg-white focus:bg-white text-dark-800 focus:outline-none focus:ring-2 focus:ring-primary-500/30 focus:border-primary-500 transition-all duration-200"
                    placeholder="Contoh: 08123456789 (opsional)">
            </div>
        </div>

        <div class="md:col-span-2">
            <label for="foto_edit" class="block text-sm font-medium text-dark-700 mb-1.5">Foto Peserta</label>
            <div class="flex items-end gap-3">
                <div class="flex-1">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class='bx bx-image text-dark-400 text-lg'></i>
                        </div>
                        <input type="file" name="foto" id="foto_edit" accept="image/*"
                            class="block w-full pl-10 pr-3 py-2.5 border border-dark-200 rounded-xl bg-white focus:bg-white text-dark-800 focus:outline-none focus:ring-2 focus:ring-primary-500/30 focus:border-primary-500 transition-all duration-200"
                            placeholder="Pilih foto...">
                    </div>
                    <p class="text-xs text-dark-400 mt-1">Format: JPG, PNG, GIF | Max: 2MB</p>
                </div>
                @if ($peserta->foto)
                    <div class="flex-shrink-0">
                        <img src="{{ asset('storage/' . $peserta->foto) }}" alt="Foto" class="w-12 h-12 rounded-lg object-cover">
                    </div>
                @endif
            </div>
        </div>

        <div class="md:col-span-2 mt-2">
            <hr class="border-dark-200 mb-4">
            <h4 class="text-sm font-semibold text-dark-600 mb-4">Akun Login Peserta</h4>
        </div>

        <div>
            <label for="email_edit" class="block text-sm font-medium text-dark-700 mb-1.5">Email</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class='bx bx-envelope text-dark-400 text-lg'></i>
                </div>
                <input type="email" name="email" id="email_edit" value="{{ $peserta->user->email ?? '' }}"
                    class="block w-full pl-10 pr-3 py-2.5 border border-dark-200 rounded-xl bg-white focus:bg-white text-dark-800 focus:outline-none focus:ring-2 focus:ring-primary-500/30 focus:border-primary-500 transition-all duration-200"
                    placeholder="Email" required autocomplete="off">
            </div>
        </div>

        <div>
            <label for="password_edit" class="block text-sm font-medium text-dark-700 mb-1.5">Password <span
                    class="text-xs text-dark-400">(Kosongkan jika tidak diubah)</span></label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class='bx bx-lock-alt text-dark-400 text-lg'></i>
                </div>
                <input type="password" name="password" id="password_edit"
                    class="block w-full pl-10 pr-3 py-2.5 border border-dark-200 rounded-xl bg-white focus:bg-white text-dark-800 focus:outline-none focus:ring-2 focus:ring-primary-500/30 focus:border-primary-500 transition-all duration-200"
                    placeholder="Minimal 6 karakter" autocomplete="off">
            </div>
        </div>
    </div>

    {{-- Actions --}}
    <div class="mt-8 flex items-center justify-end gap-3">
        <button type="button" onclick="closeModal('modal-editpeserta')"
            class="px-4 py-2 border border-dark-200 rounded-xl text-dark-600 bg-white hover:bg-dark-50 hover:text-dark-800 font-medium transition-colors duration-200">
            Batal
        </button>
        <button type="submit"
            class="px-4 py-2 bg-gradient-to-r from-primary-600 to-primary-700 hover:from-primary-700 hover:to-primary-800 text-white font-medium rounded-xl shadow-lg shadow-primary-500/30 hover:shadow-primary-500/40 transition-all duration-200 flex items-center gap-2">
            <i class='bx bx-save text-lg'></i>
            Perbarui Data
        </button>
    </div>
</form>
