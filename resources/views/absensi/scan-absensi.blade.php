@extends('layouts.app')

@section('title', 'Scan Absensi')
@section('page-title', 'Scan Absensi')
@section('page-subtitle', 'Scan QR Code yang ditampilkan oleh Tutor')

@section('breadcrumb')
    <li><i class='bx bx-chevron-right text-dark-400'></i></li>
    <li><a href="{{ route('absensi') }}" class="text-dark-400 hover:text-primary-600 transition-colors">Absensi</a></li>
    <li><i class='bx bx-chevron-right text-dark-400'></i></li>
    <li class="text-primary-600 font-medium">Scan QR</li>
@endsection

@section('content')
    <div class="max-w-3xl mx-auto">
        <div class="glass-card rounded-2xl border border-dark-200/50 p-6 sm:p-8 text-center relative overflow-hidden">
            <div class="absolute -right-10 -top-10 w-40 h-40 bg-primary-500/10 rounded-full blur-3xl"></div>
            <div class="absolute -left-10 -bottom-10 w-40 h-40 bg-accent-500/10 rounded-full blur-3xl"></div>

            <div class="relative z-10">
                <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-primary-500 to-primary-700 text-white flex items-center justify-center text-3xl mx-auto mb-6 shadow-lg shadow-primary-500/30">
                    <i class='bx bx-scan'></i>
                </div>
                
                <h2 class="text-2xl font-bold text-dark-800 mb-2">Scan QR Code Sesi</h2>
                <p class="text-dark-500 mb-8 max-w-md mx-auto">Arahkan kamera ke QR Code yang ditampilkan oleh tutor di depan kelas untuk mencatat kehadiran Anda.</p>

                <div id="reader-container" class="max-w-sm mx-auto bg-dark-50 rounded-2xl overflow-hidden border-2 border-dashed border-primary-500/50 mb-8 relative">
                    <div id="reader" width="100%"></div>
                    <div id="scan-overlay" class="absolute inset-0 bg-white/80 backdrop-blur-sm z-20 flex flex-col items-center justify-center hidden">
                        <div class="animate-spin rounded-full h-12 w-12 border-4 border-primary-200 border-t-primary-600 mb-4"></div>
                        <p class="text-dark-800 font-bold">Memproses absensi...</p>
                    </div>
                </div>

                <div id="result-message" class="hidden max-w-md mx-auto p-4 rounded-xl border mb-6"></div>

                <a href="{{ route('absensi') }}" class="inline-flex items-center gap-2 px-6 py-2.5 bg-white border border-dark-200 text-dark-700 hover:bg-dark-50 font-medium rounded-xl transition-all duration-200">
                    <i class='bx bx-arrow-back text-lg'></i>
                    Kembali
                </a>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://unpkg.com/html5-qrcode"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const resultMessage = document.getElementById('result-message');
        const overlay = document.getElementById('scan-overlay');
        let html5QrcodeScanner;
        let isProcessing = false;

        function onScanSuccess(decodedText, decodedResult) {
            if (isProcessing) return;
            
            isProcessing = true;
            html5QrcodeScanner.pause(true);
            overlay.classList.remove('hidden');
            resultMessage.classList.add('hidden');

            fetch('{{ route("scan.absensi.process") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ qr_data: decodedText })
            })
            .then(response => response.json())
            .then(data => {
                overlay.classList.add('hidden');
                resultMessage.classList.remove('hidden', 'bg-emerald-50', 'border-emerald-200', 'text-emerald-700', 'bg-rose-50', 'border-rose-200', 'text-rose-700');
                
                if (data.success) {
                    resultMessage.classList.add('bg-emerald-50', 'border-emerald-200', 'text-emerald-700');
                    resultMessage.innerHTML = `
                        <div class="flex flex-col items-center justify-center">
                            <i class='bx bx-check-circle text-4xl mb-2 text-emerald-500'></i>
                            <h4 class="font-bold text-lg mb-1">${data.message}</h4>
                            <p class="text-sm">Selamat belajar di kelas ${data.data.kelas} (${data.data.mata_pelajaran})</p>
                        </div>
                    `;
                    
                    // Stop scanner and redirect after 3 seconds
                    setTimeout(() => {
                        html5QrcodeScanner.clear();
                        window.location.href = '{{ route("absensi") }}';
                    }, 3000);
                } else {
                    resultMessage.classList.add('bg-rose-50', 'border-rose-200', 'text-rose-700');
                    resultMessage.innerHTML = `
                        <div class="flex flex-col items-center justify-center">
                            <i class='bx bx-x-circle text-4xl mb-2 text-rose-500'></i>
                            <h4 class="font-bold text-lg mb-1">Gagal!</h4>
                            <p class="text-sm">${data.message}</p>
                            <button onclick="resumeScanning()" class="mt-4 px-4 py-1.5 bg-rose-600 text-white rounded-lg text-sm hover:bg-rose-700">Coba Lagi</button>
                        </div>
                    `;
                }
            })
            .catch(error => {
                overlay.classList.add('hidden');
                resultMessage.classList.remove('hidden');
                resultMessage.classList.add('bg-rose-50', 'border-rose-200', 'text-rose-700');
                resultMessage.innerHTML = `
                    <div class="flex flex-col items-center justify-center">
                        <i class='bx bx-wifi-off text-4xl mb-2 text-rose-500'></i>
                        <h4 class="font-bold text-lg mb-1">Terjadi Kesalahan</h4>
                        <p class="text-sm">Pastikan koneksi internet stabil.</p>
                        <button onclick="resumeScanning()" class="mt-4 px-4 py-1.5 bg-rose-600 text-white rounded-lg text-sm hover:bg-rose-700">Coba Lagi</button>
                    </div>
                `;
            });
        }

        window.resumeScanning = function() {
            isProcessing = false;
            resultMessage.classList.add('hidden');
            html5QrcodeScanner.resume();
        }

        function onScanFailure(error) {
            // handle scan failure, usually better to ignore and keep scanning.
        }

        html5QrcodeScanner = new Html5QrcodeScanner(
            "reader",
            { fps: 10, qrbox: {width: 250, height: 250} },
            /* verbose= */ false);
        html5QrcodeScanner.render(onScanSuccess, onScanFailure);
    });
</script>
<style>
    #reader button {
        background-color: var(--primary-600, #4f46e5);
        color: white;
        border: none;
        padding: 8px 16px;
        border-radius: 8px;
        font-weight: 500;
        cursor: pointer;
        margin: 10px 0;
    }
    #reader button:hover {
        background-color: var(--primary-700, #4338ca);
    }
    #reader select {
        padding: 8px;
        border-radius: 8px;
        border: 1px solid #e5e7eb;
        margin-bottom: 10px;
        width: 80%;
    }
    #reader__dashboard_section_csr span {
        display: none;
    }
    #reader__dashboard_section_swaplink {
        color: var(--primary-600, #4f46e5);
        text-decoration: none;
        margin: 10px 0;
        display: inline-block;
    }
</style>
@endpush
