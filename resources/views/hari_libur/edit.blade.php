<form action="{{ route('hariLibur.update', ['id' => $hariLibur->id]) }}" method="POST" id="frmEditHariLibur">
    @csrf
    <div class="grid grid-cols-1 gap-4">
        <div>
            <label for="tanggal_edit" class="block text-sm font-medium text-dark-700 mb-1.5">Tanggal</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class='bx bx-calendar text-dark-400 text-lg'></i>
                </div>
                <input type="date" name="tanggal" id="tanggal_edit" value="{{ $hariLibur->tanggal }}"
                    class="block w-full pl-10 pr-3 py-2.5 border border-dark-200 rounded-xl bg-white focus:outline-none focus:ring-2 focus:ring-primary-500/30 focus:border-primary-500"
                    required>
            </div>
        </div>
        <div>
            <label for="keterangan_edit" class="block text-sm font-medium text-dark-700 mb-1.5">Keterangan</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class='bx bx-detail text-dark-400 text-lg'></i>
                </div>
                <input type="text" name="keterangan" id="keterangan_edit" value="{{ $hariLibur->keterangan }}"
                    class="block w-full pl-10 pr-3 py-2.5 border border-dark-200 rounded-xl bg-white focus:outline-none focus:ring-2 focus:ring-primary-500/30 focus:border-primary-500"
                    placeholder="Keterangan Hari Libur" required>
            </div>
        </div>
    </div>

    <div class="mt-8 flex items-center justify-end gap-3">
        <button type="button" onclick="closeModal('modal-editharilibur')"
            class="px-4 py-2 border border-dark-200 rounded-xl text-dark-600 bg-white hover:bg-dark-50 font-medium transition-colors duration-200">
            Batal
        </button>
        <button type="submit"
            class="px-4 py-2 bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 text-white font-medium rounded-xl shadow-lg transition-all duration-200 flex items-center gap-2">
            <i class='bx bx-save text-lg'></i>
            Perbarui Data
        </button>
    </div>
</form>
