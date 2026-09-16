<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selamat datang di ePresensi CEC Kampung Pare Mataram</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />
    <link rel="stylesheet" href="{{ asset('css/landing.css') }}">
</head>
<body class="bg-black text-white overflow-x-hidden">
    <!-- Cursor Glow Effect -->
    <div class="cursor-glow"></div>

    <!-- Navbar -->
    <nav class="fixed top-0 left-0 right-0 z-50 navbar-container">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <!-- Logo -->
                <div class="flex items-center gap-2 cursor-pointer hover:scale-105 transition">
                    <div class="w-10 h-10 bg-gradient-to-r from-red-600 to-yellow-500 rounded-lg flex items-center justify-center font-bold text-lg">
                        CEC
                    </div>
                    <span class="text-xl font-bold bg-gradient-to-r from-red-600 via-yellow-500 to-red-600 bg-clip-text text-transparent">ePresensi</span>
                </div>

                <!-- Menu Desktop -->
                <ul class="hidden md:flex items-center gap-8">
                    <li><a href="#home" class="nav-link font-medium hover:text-yellow-500 transition">Home</a></li>
                    <li><a href="#about" class="nav-link font-medium hover:text-yellow-500 transition">Tentang</a></li>
                    <li><a href="#features" class="nav-link font-medium hover:text-yellow-500 transition">Fitur</a></li>
                    <li><a href="#gallery" class="nav-link font-medium hover:text-yellow-500 transition">Galeri</a></li>
                    <li><a href="#contact" class="nav-link font-medium hover:text-yellow-500 transition">Kontak</a></li>
                </ul>

                <!-- Login Button -->
                <a href="/login" class="hidden md:block px-6 py-2 bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 rounded-lg font-semibold transition transform hover:scale-105 shadow-lg shadow-red-600/50">
                    Masuk
                </a>

                <!-- Mobile Menu Toggle -->
                <button class="md:hidden text-2xl" id="mobile-menu-btn">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div id="mobile-menu" class="hidden md:hidden bg-black/95 backdrop-blur-md border-t border-red-600/30">
            <ul class="flex flex-col items-center gap-4 py-6">
                <li><a href="#home" class="font-medium hover:text-yellow-500 transition">Home</a></li>
                <li><a href="#about" class="font-medium hover:text-yellow-500 transition">Tentang</a></li>
                <li><a href="#features" class="font-medium hover:text-yellow-500 transition">Fitur</a></li>
                <li><a href="#gallery" class="font-medium hover:text-yellow-500 transition">Galeri</a></li>
                <li><a href="#contact" class="font-medium hover:text-yellow-500 transition">Kontak</a></li>
                <li><a href="/login" class="px-6 py-2 bg-gradient-to-r from-red-600 to-red-700 rounded-lg font-semibold">Login</a></li>
            </ul>
        </div>
    </nav>

    <!-- Particle Background -->
    <div class="fixed inset-0 z-0 pointer-events-none">
        <canvas id="particle-canvas"></canvas>
    </div>

    <!-- Hero Section -->
    <section id="home" class="relative min-h-screen flex items-center justify-center overflow-hidden pt-20">
        <!-- Hero Background with Image -->
        <div class="absolute inset-0 z-0">
            <!-- Background Image -->
            <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('{{ asset('img/hero-bg.jpg') }}'); background-attachment: fixed;">
            </div>
            <!-- Dark Overlay -->
            <div class="absolute inset-0 bg-gradient-to-br from-black/85 via-red-950/50 to-black/85 backdrop-blur-sm"></div>
            <!-- Additional Gradient Overlay -->
            <div class="absolute inset-0 bg-gradient-to-br from-black via-red-950/30 to-black opacity-70"></div>
            <!-- Decorative Blobs -->
            <div class="absolute top-0 left-1/4 w-96 h-96 bg-red-600/20 rounded-full blur-3xl opacity-20"></div>
            <div class="absolute bottom-0 right-1/4 w-96 h-96 bg-yellow-500/20 rounded-full blur-3xl opacity-20"></div>
        </div>

        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid md:grid-cols-2 gap-8 items-center">
            <!-- Left Content -->
            <div data-aos="fade-right" data-aos-duration="1000">
                <h1 class="text-5xl md:text-7xl font-bold mb-6 leading-tight">
                    Sistem Presensi Digital <span class="text-red-500">CEC kampung inggris pare mataram</span>
                </h1>
                <p class="text-white-300 text-lg mb-8 leading-relaxed">
                   Sistem presensi digital berbasis QR Code untuk mendukung kegiatan belajar mengajar di CEC Kampung Pare Mataram menjadi lebih cepat, modern, dan terintegrasi.
                </p>
                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="/login" class="btn-primary px-8 py-3 rounded-lg font-semibold flex items-center justify-center gap-2 group">
                        <span>Mulai Sekarang</span>
                        <i class="fas fa-arrow-right group-hover:translate-x-1 transition"></i>
                    </a>
                    <button class="btn-secondary px-8 py-3 rounded-lg font-semibold flex items-center justify-center gap-2">
                        <i class="fas fa-play-circle"></i>
                        <span>Pelajari Lebih Lanjut</span>
                    </button>
                </div>
                <div class="flex gap-6 mt-8">
                    <div class="text-center">
                        <div class="text-3xl font-bold text-yellow-500">100+</div>
                        <div class="text-gray-400">Pengguna Aktif</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-red-500">90%</div>
                        <div class="text-gray-400">Akurasi</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-yellow-500">24/7</div>
                        <div class="text-gray-400">Support</div>
                    </div>
                </div>
            </div>

            <!-- Right - Mockup -->
            <div data-aos="fade-left" data-aos-duration="1000" class="hidden md:flex justify-center">
                <div class="relative w-full max-w-md">
                    <div class="absolute inset-0 bg-gradient-to-r from-red-600 to-yellow-500 rounded-3xl blur-2xl opacity-20"></div>
                    <div class="relative bg-gradient-to-br from-gray-900 to-black rounded-3xl p-2 border border-red-600/30">
                        <div class="bg-black rounded-2xl p-6 space-y-4 h-96 flex flex-col justify-center items-center">
                            <i class="fas fa-qrcode text-6xl text-red-500"></i>
                            <p class="text-center text-gray-300">Scan untuk Presensi</p>
                            <div class="w-24 h-24 bg-gradient-to-br from-red-600/20 to-yellow-500/20 rounded-lg border border-yellow-500/30"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Scroll Indicator -->
        <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 animate-bounce">
            <i class="fas fa-chevron-down text-2xl text-red-500"></i>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="relative py-20 px-4">
        <div class="max-w-7xl mx-auto">
            <h2 class="text-4xl md:text-5xl font-bold mb-4 text-center">
                Tentang <span class="text-red-500">Kami</span>
            </h2>
            <p class="text-center text-gray-400 mb-16 max-w-2xl mx-auto">
                Merupakan kursus bahasa Inggris terbaik di Lombok, yang mengadopsi pendidikan dari Pare Kediri.

            <div class="grid md:grid-cols-2 gap-12 items-center">
                <div data-aos="fade-right">
                    <div class="space-y-6">
                        <div class="glass-card p-6 rounded-2xl border border-red-600/30 hover:border-yellow-500/50 transition">
                            <div class="flex items-start gap-4">
                                <div class="w-12 h-12 bg-gradient-to-r from-red-600 to-yellow-500 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-shield text-white"></i>
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold mb-2">Aman & Terintegrasi
                                    </h3>
                                    <p class="text-gray-400">Sistem dirancang untuk mendukung pengelolaan data yang lebih tertata dan terorganisir.</p>
                                </div>
                            </div>
                        </div>

                        <div class="glass-card p-6 rounded-2xl border border-red-600/30 hover:border-yellow-500/50 transition">
                            <div class="flex items-start gap-4">
                                <div class="w-12 h-12 bg-gradient-to-r from-red-600 to-yellow-500 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-rocket text-white"></i>
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold mb-2">Presensi Cepat</h3>
                                    <p class="text-gray-400">
Proses absensi hanya membutuhkan beberapa detik melalui scan QR Code.</p>
                                </div>
                            </div>
                        </div>

                        <div class="glass-card p-6 rounded-2xl border border-red-600/30 hover:border-yellow-500/50 transition">
                            <div class="flex items-start gap-4">
                                <div class="w-12 h-12 bg-gradient-to-r from-red-600 to-yellow-500 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-mobile-alt text-white"></i>
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold mb-2">Real-Time Monitoring</h3>
                                    <p class="text-gray-400">Data kehadiran langsung tersimpan dan dapat dipantau secara langsung.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div data-aos="fade-left">
                    <div class="space-y-4">
                        <p class="text-gray-300 leading-relaxed">
                            CEC Kampung Pare Mataram CEC Kampung Pare Mataram merupakan lembaga kursus bahasa Inggris yang berkomitmen menghadirkan lingkungan belajar yang aktif, disiplin, dan modern bagi para siswa. Dengan metode pembelajaran yang interaktif serta didukung tenaga pengajar profesional, CEC terus berupaya meningkatkan kualitas pendidikan dan pelayanan di era digital.
                        <p class="text-gray-300 leading-relaxed">
                            Cara Gila Belajar Bahasa Inggris dalam 1 bulan

                        <p class="text-gray-300 leading-relaxed">
                            Harga paling murah dengan jumlah pertemuan melimpah!!!
                        </p>
                        <button class="btn-secondary mt-6 px-8 py-3 rounded-lg font-semibold">
                            Selengkapnya
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Programs Section -->
    <section id="programs" class="relative py-20 px-4">
        <div class="max-w-7xl mx-auto">
            <h2 class="text-4xl md:text-5xl font-bold mb-4 text-center">
                Program <span class="text-gradient">CEC Kami</span>
            </h2>
            <p class="text-center text-gray-400 mb-16 max-w-2xl mx-auto">
                Berbagai program pembelajaran bahasa Inggris yang dirancang untuk memenuhi kebutuhan dan level siswa yang berbeda
            </p>

            <div class="grid md:grid-cols-3 gap-6 mb-8">
                <!-- Program 1: English Camp -->
                <div data-aos="zoom-in" class="group rounded-2xl overflow-hidden border border-red-600/30 hover:border-yellow-500/50 transition glass-card bg-black/50 backdrop-blur-md">
                    <!-- Image Container -->
                    <div class="relative h-56 overflow-hidden bg-gradient-to-br from-red-600/20 to-yellow-500/20">
                        <img src="{{ asset('img/program-basic.jpg') }}" alt="English Camp" class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
                        <div class="absolute inset-0 bg-black/40 group-hover:bg-black/20 transition duration-300"></div>
                        <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black to-transparent p-4">
                            <h3 class="text-xl font-bold text-white">English Camp</h3>
                        </div>
                    </div>
                    <!-- Content -->
                    <div class="p-6">
                        <p class="text-gray-400 text-sm leading-relaxed mb-4">
                            Program ini dirancang khusus selama 3 bulan bisa ngomong inggris dengan system pembelajaran mulai dari nol, dari dasar atau bahkan yang benci sekalipun dengan bahasa inggris dengan tinggal di asrama.
                        </p>
                        <ul class="space-y-2 text-xs text-gray-300 mb-4">
                            <li class="flex items-center gap-2"><i class="fas fa-check text-yellow-500"></i> Durasi: 3 Bulan</li>
                            <li class="flex items-center gap-2"><i class="fas fa-check text-yellow-500"></i> Tinggal di Asrama</li>
                            <li class="flex items-center gap-2"><i class="fas fa-check text-yellow-500"></i> Level: Pemula</li>
                        </ul>
                        <button class="w-full px-4 py-2 bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white text-sm font-semibold rounded-lg transition transform hover:scale-105">
                            Daftar Sekarang
                        </button>
                    </div>
                </div>

                <!-- Program 2: English for Kids -->
                <div data-aos="zoom-in" data-aos-delay="100" class="group rounded-2xl overflow-hidden border border-red-600/30 hover:border-yellow-500/50 transition glass-card bg-black/50 backdrop-blur-md">
                    <!-- Image Container -->
                    <div class="relative h-56 overflow-hidden bg-gradient-to-br from-red-600/20 to-yellow-500/20">
                        <img src="{{ asset('img/program-kids.jpg') }}" alt="English for Kids" class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
                        <div class="absolute inset-0 bg-black/40 group-hover:bg-black/20 transition duration-300"></div>
                        <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black to-transparent p-4">
                            <h3 class="text-xl font-bold text-white">English for Kids</h3>
                        </div>
                    </div>
                    <!-- Content -->
                    <div class="p-6">
                        <p class="text-gray-400 text-sm leading-relaxed mb-4">
                            Kelas ini dirancang khusus untuk anak-anak usia sekolah dasar. Materinya mulai penguasaan vocabulary hingga percakapan. Konsep belajarnya FUN banget - Belajar sambil bermain.
                        </p>
                        <ul class="space-y-2 text-xs text-gray-300 mb-4">
                            <li class="flex items-center gap-2"><i class="fas fa-check text-yellow-500"></i> Umur: 6-12 Tahun</li>
                            <li class="flex items-center gap-2"><i class="fas fa-check text-yellow-500"></i> Metode: Fun Learning</li>
                            <li class="flex items-center gap-2"><i class="fas fa-check text-yellow-500"></i> Belajar Sambil Bermain</li>
                        </ul>
                        <button class="w-full px-4 py-2 bg-gradient-to-r from-yellow-500 to-yellow-600 hover:from-yellow-600 hover:to-yellow-700 text-black text-sm font-semibold rounded-lg transition transform hover:scale-105">
                            Daftar Sekarang
                        </button>
                    </div>
                </div>

                <!-- Program 3: Speaking Academy -->
                <div data-aos="zoom-in" data-aos-delay="200" class="group rounded-2xl overflow-hidden border border-red-600/30 hover:border-yellow-500/50 transition glass-card bg-black/50 backdrop-blur-md">
                    <!-- Image Container -->
                    <div class="relative h-56 overflow-hidden bg-gradient-to-br from-red-600/20 to-yellow-500/20">
                        <img src="{{ asset('img/program-advanced.jpg') }}" alt="Speaking Academy" class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
                        <div class="absolute inset-0 bg-black/40 group-hover:bg-black/20 transition duration-300"></div>
                        <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black to-transparent p-4">
                            <h3 class="text-xl font-bold text-white">Speaking Academy</h3>
                        </div>
                    </div>
                    <!-- Content -->
                    <div class="p-6">
                        <p class="text-gray-400 text-sm leading-relaxed mb-4">
                            Jagonya English! Kelas ini dirancang khusus untuk kamu yang punya sedikit waktu luang. Dengan konsep belajar yang FUN banget untuk menguasai speaking skill.
                        </p>
                        <ul class="space-y-2 text-xs text-gray-300 mb-4">
                            <li class="flex items-center gap-2"><i class="fas fa-check text-yellow-500"></i> Jadwal Fleksibel</li>
                            <li class="flex items-center gap-2"><i class="fas fa-check text-yellow-500"></i> Fokus Speaking</li>
                            <li class="flex items-center gap-2"><i class="fas fa-check text-yellow-500"></i> Metode Interaktif</li>
                        </ul>
                        <button class="w-full px-4 py-2 bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white text-sm font-semibold rounded-lg transition transform hover:scale-105">
                            Daftar Sekarang
                        </button>
                    </div>
                </div>
            </div>

            <!-- Additional Programs Row -->
            <div class="grid md:grid-cols-2 gap-6">
                <!-- Program 4: Kelas Online Rasa Offline -->
                <div data-aos="zoom-in" data-aos-delay="300" class="group rounded-2xl overflow-hidden border border-red-600/30 hover:border-yellow-500/50 transition glass-card bg-black/50 backdrop-blur-md">
                    <!-- Image Container -->
                    <div class="relative h-48 overflow-hidden bg-gradient-to-br from-red-600/20 to-yellow-500/20">
                        <img src="{{ asset('img/program-intermediate.jpg') }}" alt="Kelas Online Rasa Offline" class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
                        <div class="absolute inset-0 bg-black/40 group-hover:bg-black/20 transition duration-300"></div>
                        <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black to-transparent p-4">
                            <h3 class="text-lg font-bold text-white">Kelas Online Rasa Offline</h3>
                        </div>
                    </div>
                    <!-- Content -->
                    <div class="p-6">
                        <p class="text-gray-400 text-sm leading-relaxed mb-4">
                            Ga punya banyak waktu belajar? Cobain kelas interaktif online ini. Dilengkapi module vocabulary harian, worksheet hingga praktek video call setiap hari. Cara FUN bisa bahasa Inggris.
                        </p>
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-yellow-500 font-semibold">💻 100% Online</span>
                            <button class="px-4 py-2 bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white text-xs font-semibold rounded-lg transition">
                                Daftar
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Program 5: Jago TOEFL Kilat -->
                <div data-aos="zoom-in" data-aos-delay="400" class="group rounded-2xl overflow-hidden border border-red-600/30 hover:border-yellow-500/50 transition glass-card bg-black/50 backdrop-blur-md">
                    <!-- Image Container -->
                    <div class="relative h-48 overflow-hidden bg-gradient-to-br from-red-600/20 to-yellow-500/20">
                        <img src="{{ asset('img/program-toefl.jpg') }}" alt="Jago TOEFL Kilat" class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
                        <div class="absolute inset-0 bg-black/40 group-hover:bg-black/20 transition duration-300"></div>
                        <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black to-transparent p-4">
                            <h3 class="text-lg font-bold text-white">Jago TOEFL Kilat</h3>
                        </div>
                    </div>
                    <!-- Content -->
                    <div class="p-6">
                        <p class="text-gray-400 text-sm leading-relaxed mb-4">
                            Mau kuliah di universitas impian? Mau dapat beasiswa dalam maupun luar negeri? Atau mau bekerja di BUMN atau CPNS? Kamu wajib ikut kelas TOEFL ini. Banyak yang berhasil loh!
                        </p>
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-yellow-500 font-semibold">🎯 Beasiswa & Karir</span>
                            <button class="px-4 py-2 bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white text-xs font-semibold rounded-lg transition">
                                Daftar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="relative py-20 px-4">
        <div class="max-w-7xl mx-auto">
            <h2 class="text-4xl md:text-5xl font-bold mb-4 text-center">
                Fitur CEC <span class="text-gradient">di Design Untuk Kamu</span>
            </h2>
            <p class="text-center text-gray-400 mb-16 max-w-2xl mx-auto">
                Di CEC Kamu langsung praktek bahasa Inggris dengan FUN, sehingga CEPAT BISA Ngomong Inggris.
            </p>

            <div class="grid md:grid-cols-3 gap-6">
                <!-- Feature 1 -->
                <div data-aos="zoom-in" class="feature-card glass-card p-8 rounded-2xl border border-red-600/30 hover:border-yellow-500/50 group transition cursor-pointer">
                    <div class="text-5xl mb-4 text-red-500 group-hover:text-yellow-500 transition">
                        <i class="fas fa-qrcode"></i>
                    </div>
                    <h3 class="text-2xl font-bold mb-3">Presensi QR Code</h3>
                    <p class="text-gray-400">Scan QR Code dengan mudah dari smartphone untuk presensi instan</p>
                </div>

                <!-- Feature 2 -->
                <div data-aos="zoom-in" data-aos-delay="100" class="feature-card glass-card p-8 rounded-2xl border border-red-600/30 hover:border-yellow-500/50 group transition cursor-pointer">
                    <div class="text-5xl mb-4 text-yellow-500 group-hover:text-red-500 transition">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h3 class="text-2xl font-bold mb-3">Monitoring Real-Time</h3>
                    <p class="text-gray-400">Pantau presensi peserta secara langsung dengan dashboard interaktif</p>
                </div>

                <!-- Feature 3 -->
                <div data-aos="zoom-in" data-aos-delay="200" class="feature-card glass-card p-8 rounded-2xl border border-red-600/30 hover:border-yellow-500/50 group transition cursor-pointer">
                    <div class="text-5xl mb-4 text-red-500 group-hover:text-yellow-500 transition">
                        <i class="fas fa-database"></i>
                    </div>
                    <h3 class="text-2xl font-bold mb-3">Data Real-Time</h3>
                    <p class="text-gray-400">Akses data presensi terkini kapan saja dari mana saja</p>
                </div>

                <!-- Feature 4 -->
                <div data-aos="zoom-in" data-aos-delay="300" class="feature-card glass-card p-8 rounded-2xl border border-red-600/30 hover:border-yellow-500/50 group transition cursor-pointer">
                    <div class="text-5xl mb-4 text-yellow-500 group-hover:text-red-500 transition">
                        <i class="fas fa-sync-alt"></i>
                    </div>
                    <h3 class="text-2xl font-bold mb-3">Rekap Otomatis</h3>
                    <p class="text-gray-400">Laporan presensi terekap otomatis dan siap diunduh</p>
                </div>

                <!-- Feature 5 -->
                <div data-aos="zoom-in" data-aos-delay="400" class="feature-card glass-card p-8 rounded-2xl border border-red-600/30 hover:border-yellow-500/50 group transition cursor-pointer">
                    <div class="text-5xl mb-4 text-red-500 group-hover:text-yellow-500 transition">
                        <i class="fas fa-mobile-alt"></i>
                    </div>
                    <h3 class="text-2xl font-bold mb-3">Responsive Mobile</h3>
                    <p class="text-gray-400">Aplikasi fully responsive untuk semua perangkat mobile</p>
                </div>

                <!-- Feature 6 -->
                <div data-aos="zoom-in" data-aos-delay="500" class="feature-card glass-card p-8 rounded-2xl border border-red-600/30 hover:border-yellow-500/50 group transition cursor-pointer">
                    <div class="text-5xl mb-4 text-yellow-500 group-hover:text-red-500 transition">
                        <i class="fas fa-lock"></i>
                    </div>
                    <h3 class="text-2xl font-bold mb-3">Sistem Aman</h3>
                    <p class="text-gray-400">Keamanan berlapis dengan enkripsi dan authentication modern</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Statistics Section -->
    <section class="relative py-20 px-4">
        <div class="max-w-7xl mx-auto">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                <div data-aos="fade-up" class="glass-card p-8 rounded-2xl border border-red-600/30 text-center">
                    <div class="text-4xl md:text-5xl font-bold text-red-500 mb-2">
                        <span class="counter" data-target="100">0</span>+
                    </div>
                    <p class="text-gray-400">Siswa Aktif</p>
                </div>

                <div data-aos="fade-up" data-aos-delay="100" class="glass-card p-8 rounded-2xl border border-yellow-500/30 text-center">
                    <div class="text-4xl md:text-5xl font-bold text-yellow-500 mb-2">
                        <span class="counter" data-target="10">0</span>+
                    </div>
                    <p class="text-gray-400">Pengajar</p>
                </div>

                <div data-aos="fade-up" data-aos-delay="200" class="glass-card p-8 rounded-2xl border border-red-600/30 text-center">
                    <div class="text-4xl md:text-5xl font-bold text-red-500 mb-2">
                        <span class="counter" data-target="100">0</span>+
                    </div>
                    <p class="text-gray-400">Presensi Tercatat</p>
                </div>

                <div data-aos="fade-up" data-aos-delay="300" class="glass-card p-8 rounded-2xl border border-yellow-500/30 text-center">
                    <div class="text-4xl md:text-5xl font-bold text-yellow-500 mb-2">
                        <span class="counter" data-target="90">0</span>%
                    </div>
                    <p class="text-gray-400">Kepuasan Pengguna</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Gallery Section -->
    <section id="gallery" class="relative py-20 px-4">
        <div class="max-w-7xl mx-auto">
            <h2 class="text-4xl md:text-5xl font-bold mb-4 text-center">
                Galeri <span class="text-gradient">Para member </span>
            </h2>
            <p class="text-center text-gray-400 mb-16 max-w-2xl mx-auto">
                Keseruan Member CEC kampung pare mataram.


            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div data-aos="zoom-in" class="gallery-item relative rounded-2xl overflow-hidden cursor-pointer group">
                    <div class="absolute inset-0 bg-gradient-to-br from-red-600/30 to-yellow-500/20 opacity-0 group-hover:opacity-100 transition duration-500 z-10"></div>
                    <img src="{{ asset('img/cec-kursus-bahasa-inggris.jpg') }}" alt="Kegiatan Belajar" class="w-full h-64 object-cover group-hover:scale-105 transition duration-700">
                    <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/80 to-transparent p-4 z-20">
                        <p class="font-semibold text-white">Kegiatan Belajar</p>
                    </div>
                </div>

                <div data-aos="zoom-in" data-aos-delay="100" class="gallery-item relative rounded-2xl overflow-hidden cursor-pointer group">
                    <div class="absolute inset-0 bg-gradient-to-br from-red-600/30 to-yellow-500/20 opacity-0 group-hover:opacity-100 transition duration-500 z-10"></div>
                    <img src="{{ asset('img/cec-kursus-bahasa-inggris-angkatan-69.jpg') }}" alt="Angkatan 69" class="w-full h-64 object-cover group-hover:scale-105 transition duration-700">
                    <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/80 to-transparent p-4 z-20">
                        <p class="font-semibold text-white">Angkatan 69</p>
                    </div>
                </div>

                <div data-aos="zoom-in" data-aos-delay="200" class="gallery-item relative rounded-2xl overflow-hidden cursor-pointer group">
                    <div class="absolute inset-0 bg-gradient-to-br from-red-600/30 to-yellow-500/20 opacity-0 group-hover:opacity-100 transition duration-500 z-10"></div>
                    <img src="{{ asset('img/bersama-mr-kalend-osen-pendiri-kampung-inggris-pare-kediri-2.jpg') }}" alt="Founder Pare" class="w-full h-64 object-cover group-hover:scale-105 transition duration-700">
                    <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/80 to-transparent p-4 z-20">
                        <p class="font-semibold text-white">Pertemuan Founder</p>
                    </div>
                </div>

                <div data-aos="zoom-in" data-aos-delay="300" class="gallery-item relative rounded-2xl overflow-hidden cursor-pointer group">
                    <div class="absolute inset-0 bg-gradient-to-br from-red-600/30 to-yellow-500/20 opacity-0 group-hover:opacity-100 transition duration-500 z-10"></div>
                    <img src="{{ asset('img/belajar-langsung-bahasa-inggris-dengan-bule.jpg') }}" alt="Belajar Langsung" class="w-full h-64 object-cover group-hover:scale-105 transition duration-700">
                    <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/80 to-transparent p-4 z-20">
                        <p class="font-semibold text-white">Belajar Langsung</p>
                    </div>
                </div>

                <div data-aos="zoom-in" data-aos-delay="400" class="gallery-item relative rounded-2xl overflow-hidden cursor-pointer group">
                    <div class="absolute inset-0 bg-gradient-to-br from-red-600/30 to-yellow-500/20 opacity-0 group-hover:opacity-100 transition duration-500 z-10"></div>
                    <img src="{{ asset('img/farewall-party-setiap-angkatan.jpg') }}" alt="Farewell Party" class="w-full h-64 object-cover group-hover:scale-105 transition duration-700">
                    <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/80 to-transparent p-4 z-20">
                        <p class="font-semibold text-white">Farewell Party</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="relative py-20 px-4">
        <div class="max-w-7xl mx-auto">
            <h2 class="text-4xl md:text-5xl font-bold mb-4 text-center">
                Hubungi <span class="text-gradient">Kami</span>
            </h2>
            <p class="text-center text-gray-400 mb-16 max-w-2xl mx-auto">
                Kami siap membantu Anda. Hubungi kami untuk informasi lebih lanjut
            </p>

            <div class="grid md:grid-cols-2 gap-12">
                <!-- Contact Form -->
                <div data-aos="fade-right">
                    <form class="space-y-6">
                        <div>
                            <label class="block text-sm font-medium mb-2">Nama Lengkap</label>
                            <input type="text" placeholder="Masukkan nama Anda" class="w-full bg-white/5 border border-red-600/30 rounded-lg px-4 py-3 focus:outline-none focus:border-yellow-500/50 focus:bg-white/10 transition">
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-2">Email</label>
                            <input type="email" placeholder="masukkan@email.com" class="w-full bg-white/5 border border-red-600/30 rounded-lg px-4 py-3 focus:outline-none focus:border-yellow-500/50 focus:bg-white/10 transition">
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-2">Pesan</label>
                            <textarea placeholder="Tulis pesan Anda di sini..." rows="5" class="w-full bg-white/5 border border-red-600/30 rounded-lg px-4 py-3 focus:outline-none focus:border-yellow-500/50 focus:bg-white/10 transition resize-none"></textarea>
                        </div>

                        <button type="submit" class="w-full btn-primary py-3 rounded-lg font-semibold">
                            Kirim Pesan
                        </button>
                    </form>
                </div>

                <!-- Contact Info & Map -->
                <div data-aos="fade-left">
                    <div class="space-y-6 mb-8">
                        <!-- Info Card -->
                        <div class="glass-card p-6 rounded-2xl border border-red-600/30">
                            <div class="flex items-start gap-4">
                                <div class="w-12 h-12 bg-gradient-to-r from-red-600 to-yellow-500 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-map-marker-alt text-white"></i>
                                </div>
                                <div>
                                    <h3 class="font-bold mb-1">Alamat</h3>
                                    <p class="text-gray-400">Jl. Abdul Kadir Munsyi Gang Dahlia No. 16, Punia, Kec. Mataram, Kota Mataram, Nusa Tenggara Barat. 83115</p>
                                </div>
                            </div>
                        </div>

                        <div class="glass-card p-6 rounded-2xl border border-red-600/30">
                            <div class="flex items-start gap-4">
                                <div class="w-12 h-12 bg-gradient-to-r from-red-600 to-yellow-500 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-phone text-white"></i>
                                </div>
                                <div>
                                    <h3 class="font-bold mb-1">Telepon</h3>
                                    <p class="text-gray-400">+62 823 4031 1694</p>
                                </div>
                            </div>
                        </div>

                        <div class="glass-card p-6 rounded-2xl border border-red-600/30">
                            <div class="flex items-start gap-4">
                                <div class="w-12 h-12 bg-gradient-to-r from-red-600 to-yellow-500 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-envelope text-white"></i>
                                </div>
                                <div>
                                    <h3 class="font-bold mb-1">Email</h3>
                                    <p class="text-gray-400">cecoffice9@gmail.com></p>
                                </div>
                    
                            </div>
                        </div>

                        <div class="glass-card p-6 rounded-2xl border border-red-600/30">
                            <div class="flex items-start gap-4">
                                <div class="w-12 h-12 bg-gradient-to-r from-red-600 to-yellow-500 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-clock text-white"></i>
                                </div>
                                <div>
                                    <h3 class="font-bold mb-1">Jam Operasional</h3>
                                    <p class="text-gray-400">Monday - Friday (09:00 - 17:00 WITA)
(Phone until 17:00 WITA)
</p>
                                    <p class="text-gray-400"></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Map -->
                    <div class="rounded-2xl overflow-hidden border border-red-600/30 h-64">
                        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3945.0461328670453!2d116.09958527401875!3d-8.591563191453309!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2dcdbf69646240fb%3A0xec825e84fac3f35f!2sCEC%20Kampung%20Pare%20Mataram!5e0!3m2!1sid!2sid!4v1779160809245!5m2!1sid!2sid" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="relative py-12 px-4 border-t border-red-600/30">
        <div class="max-w-7xl mx-auto">
            <div class="grid md:grid-cols-4 gap-8 mb-8">
                <!-- Brand -->
                <div>
                    <div class="flex items-center gap-2 mb-4">
                        <div class="w-10 h-10 bg-gradient-to-r from-red-600 to-yellow-500 rounded-lg flex items-center justify-center font-bold">
                            EP
                        </div>
                        <span class="text-lg font-bold">ePresensi</span>
                    </div>
                    <p class="text-gray-400 text-sm">Sistem presensi digital futuristik untuk lembaga pendidikan modern.</p>
                </div>

                <!-- Quick Links -->
                <div>
                    <h3 class="font-bold mb-4">Navigasi</h3>
                    <ul class="space-y-2 text-gray-400 text-sm">
                        <li><a href="#home" class="hover:text-yellow-500 transition">Home</a></li>
                        <li><a href="#about" class="hover:text-yellow-500 transition">Tentang</a></li>
                        <li><a href="#features" class="hover:text-yellow-500 transition">Fitur</a></li>
                        <li><a href="#contact" class="hover:text-yellow-500 transition">Kontak</a></li>
                    </ul>
                </div>

                <!-- Services -->
                <div>
                    <h3 class="font-bold mb-4">Layanan</h3>
                    <ul class="space-y-2 text-gray-400 text-sm">
                        <li><a href="#" class="hover:text-yellow-500 transition">Presensi QR</a></li>
                        <li><a href="#" class="hover:text-yellow-500 transition">Dashboard Admin</a></li>
                        <li><a href="#" class="hover:text-yellow-500 transition">Laporan Real-Time</a></li>
                        <li><a href="#" class="hover:text-yellow-500 transition">Support 24/7</a></li>
                    </ul>
                </div>

                <!-- Social Media -->
                <div>
                    <h3 class="font-bold mb-4">Ikuti Kami</h3>
                    <div class="flex gap-4">
                        <a href="#" class="w-10 h-10 bg-white/10 border border-red-600/30 rounded-lg flex items-center justify-center hover:bg-red-600 hover:border-red-600 transition">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-white/10 border border-red-600/30 rounded-lg flex items-center justify-center hover:bg-red-600 hover:border-red-600 transition">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-white/10 border border-red-600/30 rounded-lg flex items-center justify-center hover:bg-red-600 hover:border-red-600 transition">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-white/10 border border-red-600/30 rounded-lg flex items-center justify-center hover:bg-red-600 hover:border-red-600 transition">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                    </div>
                </div>
            </div>

            <div class="border-t border-red-600/30 pt-8">
                <div class="flex flex-col md:flex-row justify-between items-center gap-4 text-gray-400 text-sm">
                    <p>&copy; 2026 ePresensi. Semua hak dilindungi.</p>
                    <div class="flex gap-6">
                        <a href="#" class="hover:text-yellow-500 transition">Kebijakan Privasi</a>
                        <a href="#" class="hover:text-yellow-500 transition">Syarat & Ketentuan</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Neon Line -->
        <div class="absolute top-0 left-0 right-0 h-px bg-gradient-to-r from-transparent via-red-600 to-transparent"></div>
    </footer>

    <!-- Back to Top Button -->
    <button id="back-to-top" class="back-to-top fixed bottom-8 right-8 w-12 h-12 bg-gradient-to-r from-red-600 to-yellow-500 rounded-full flex items-center justify-center opacity-0 pointer-events-none transition hover:scale-110">
        <i class="fas fa-arrow-up"></i>
    </button>

    <!-- Scripts -->
    <script src="https://unpkg.com/aos@next/dist/aos.js"></script>
    <script src="{{ asset('js/landing.js') }}"></script>
</body>
</html>
