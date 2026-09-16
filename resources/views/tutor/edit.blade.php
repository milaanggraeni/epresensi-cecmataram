<form action="{{ route('tutor.update', ['id' => $tutor->id]) }}" method="POST" id="frmEditTutor" enctype="multipart/form-data">
    @csrf
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

        <div>
            <label for="nama_edit" class="block text-sm font-medium text-dark-700 mb-1.5">Nama Lengkap</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class='bx bx-user text-dark-400 text-lg'></i>
                </div>
                <input type="text" name="nama" id="nama_edit" value="{{ $tutor->nama }}"
                    class="block w-full pl-10 pr-3 py-2.5 border border-dark-200 rounded-xl bg-white focus:bg-white text-dark-800 focus:outline-none focus:ring-2 focus:ring-primary-500/30 focus:border-primary-500 transition-all duration-200"
                    placeholder="Nama Lengkap" required autocomplete="off">
            </div>
        </div>

        <div>
            <label for="mapel_edit" class="block text-sm font-medium text-dark-700 mb-1.5">Mata Pelajaran</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class='bx bx-book text-dark-400 text-lg'></i>
                </div>
                <textarea name="mapel" id="mapel_edit" rows="4"
                    class="block w-full pl-10 pr-3 py-2.5 border border-dark-200 rounded-xl bg-white focus:bg-white text-dark-800 focus:outline-none focus:ring-2 focus:ring-primary-500/30 focus:border-primary-500 transition-all duration-200"
                    placeholder="Contoh: Matematika, IPA, Bahasa Inggris&#10;(Pisahkan dengan koma jika lebih dari satu)" required>{{ $tutor->mapel }}</textarea>
            </div>
            <p class="text-xs text-dark-400 mt-1">💡 Tutor bisa mengajar beberapa mata pelajaran. Pisahkan dengan koma (,) untuk multiple mapel</p>
        </div>

        <div>
            <label for="jenis_kelamin_edit" class="block text-sm font-medium text-dark-700 mb-1.5">Jenis Kelamin</label>
            <select name="jenis_kelamin" id="jenis_kelamin_edit" required
                class="block w-full px-3 py-2.5 border border-dark-200 rounded-xl bg-white focus:bg-white text-dark-800 focus:outline-none focus:ring-2 focus:ring-primary-500/30 focus:border-primary-500 transition-all duration-200">
                <option value="L" {{ $tutor->jenis_kelamin == 'L' ? 'selected' : '' }}>Laki-laki</option>
                <option value="P" {{ $tutor->jenis_kelamin == 'P' ? 'selected' : '' }}>Perempuan</option>
            </select>
        </div>

        <div class="md:col-span-2">
            <label for="alamat_edit" class="block text-sm font-medium text-dark-700 mb-1.5">Alamat</label>
            <textarea name="alamat" id="alamat_edit" rows="3"
                class="block w-full px-3 py-2.5 border border-dark-200 rounded-xl bg-white focus:bg-white text-dark-800 focus:outline-none focus:ring-2 focus:ring-primary-500/30 focus:border-primary-500 transition-all duration-200"
                placeholder="Alamat lengkap" required>{{ $tutor->alamat }}</textarea>
        </div>

        <div>
            <label for="nomor_hp_edit" class="block text-sm font-medium text-dark-700 mb-1.5">Nomor HP</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class='bx bx-phone text-dark-400 text-lg'></i>
                </div>
                <input type="text" name="nomor_hp" id="nomor_hp_edit" value="{{ $tutor->nomor_hp }}"
                    class="block w-full pl-10 pr-3 py-2.5 border border-dark-200 rounded-xl bg-white focus:bg-white text-dark-800 focus:outline-none focus:ring-2 focus:ring-primary-500/30 focus:border-primary-500 transition-all duration-200"
                    placeholder="08xxxxxxxxxx" autocomplete="off">
            </div>
        </div>

        <div class="md:col-span-2">
            <label for="foto_edit" class="block text-sm font-medium text-dark-700 mb-1.5">Foto Tutor</label>
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
                @if ($tutor->foto)
                    <div class="flex-shrink-0">
                        <img src="{{ asset('storage/' . $tutor->foto) }}" alt="Foto" class="w-12 h-12 rounded-lg object-cover">
                    </div>
                @endif
            </div>
        </div>

        <div class="md:col-span-2 mt-2">
            <hr class="border-dark-200 mb-4">
            <h4 class="text-sm font-semibold text-dark-600 mb-4">Akun Login Tutor</h4>
        </div>

        <div>
            <label for="email_edit" class="block text-sm font-medium text-dark-700 mb-1.5">Email</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class='bx bx-envelope text-dark-400 text-lg'></i>
                </div>
                <input type="email" name="email" id="email_edit" value="{{ $tutor->user->email ?? '' }}"
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
        <button type="button" onclick="closeModal('modal-edittutor')"
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
