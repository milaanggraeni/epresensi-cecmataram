<?php

use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HariLiburController;
use App\Http\Controllers\IzinController;
use App\Http\Controllers\JadwalController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\LokasiSekolahController;
use App\Http\Controllers\PesertaController;
use App\Http\Controllers\TutorController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::middleware(['guest:web'])->group(function () {

    Route::get('/', [AuthController::class, 'index'])->name('login');
    Route::post('/proseslogin', [AuthController::class, 'proseslogin'])->name('proseslogin');
});

Route::middleware(['auth:web'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/dashboard/absensi/{id}/update', [DashboardController::class, 'updateAbsensi'])->name('dashboard.absensi.update');
    Route::post('/dashboard/absensi/{id}/delete', [DashboardController::class, 'deleteAbsensi'])->name('dashboard.absensi.delete');
    Route::get('/dashboard/export-pdf', [DashboardController::class, 'exportPdf'])->name('dashboard.export.pdf');
    Route::get('/dashboard/export-excel', [DashboardController::class, 'exportExcel'])->name('dashboard.export.excel');
    Route::get('/dashboard/download-id-card', [DashboardController::class, 'downloadIdCard'])->name('dashboard.download.idcard');
    Route::get('/proseslogout', [AuthController::class, 'proseslogout'])->name('proseslogout');

    // Profile
    Route::get('/profile', [UserController::class, 'profile'])->name('profile');
    Route::post('/profile/update', [UserController::class, 'updateProfile'])->name('profile.update');
    Route::post('/profile/password', [UserController::class, 'updatePassword'])->name('profile.password');

    // peserta CRUD
    Route::get('/peserta', [PesertaController::class, 'index'])->name('peserta');
    Route::post('/peserta', [PesertaController::class, 'store'])->name('peserta.store');
    Route::post('/peserta/edit', [PesertaController::class, 'edit'])->name('peserta.edit');
    Route::post('/peserta/{id}', [PesertaController::class, 'update'])->name('peserta.update');
    Route::post('/peserta/delete/{id}', [PesertaController::class, 'destroy'])->name('peserta.delete');
    Route::get('/peserta/export-pdf', [PesertaController::class, 'exportPdf'])->name('peserta.export.pdf');
    Route::get('/peserta/export-excel', [PesertaController::class, 'exportExcel'])->name('peserta.export.excel');

    // Kelas
    Route::get('/kelas', [KelasController::class, 'index'])->name('kelas');
    Route::post('/kelas', [KelasController::class, 'store'])->name('kelas.store');
    Route::post('/kelas/edit', [KelasController::class, 'edit'])->name('kelas.edit');
    Route::post('/kelas/{id}', [KelasController::class, 'update'])->name('kelas.update');
    Route::post('/kelas/delete/{id}', [KelasController::class, 'destroy'])->name('kelas.delete');

    // Guru
    Route::get('/tutor', [TutorController::class, 'index'])->name('tutor');
    Route::post('/tutor', [TutorController::class, 'store'])->name('tutor.store');
    Route::post('/tutor/edit', [TutorController::class, 'edit'])->name('tutor.edit');
    Route::post('/tutor/{id}', [TutorController::class, 'update'])->name('tutor.update');
    Route::post('/tutor/delete/{id}', [TutorController::class, 'destroy'])->name('tutor.delete');

    // Jadwal
    Route::get('/jadwal', [JadwalController::class, 'index'])->name('jadwal');
    Route::post('/jadwal', [JadwalController::class, 'store'])->name('jadwal.store');
    Route::post('/jadwal/edit', [JadwalController::class, 'edit'])->name('jadwal.edit');
    Route::post('/jadwal/{id}', [JadwalController::class, 'update'])->name('jadwal.update');
    Route::post('/jadwal/delete/{id}', [JadwalController::class, 'destroy'])->name('jadwal.delete');



    // Lokasi Sekolah
    Route::get('/lokasi-sekolah', [LokasiSekolahController::class, 'index'])->name('lokasi_sekolah');
    Route::post('/lokasi-sekolah', [LokasiSekolahController::class, 'update'])->name('lokasi_sekolah.update');

    // Users
    Route::get('user', [UserController::class, 'index'])->name('user');
    Route::post('/user', [UserController::class, 'store'])->name('user');
    Route::post('/user/edit', [UserController::class, 'edit'])->name('user.edit');
    Route::post('/user/{id}', [UserController::class, 'update'])->name('user.update');
    Route::post('/user/delete/{id}', [UserController::class, 'destroy'])->name('user.delete');
    Route::get('/profile', [UserController::class, 'profile'])->name('profile');

    // Absensi
    Route::get('/absensi-siswa', [AbsensiController::class, 'index'])->name('absensi');
    Route::post('/absensi-siswa/store', [AbsensiController::class, 'store'])->name('absensi.store');
    Route::get('/riwayat-kehadiran', [AbsensiController::class, 'riwayatKehadiran'])->name('riwayatkehadiran');

    Route::get('/absensi-siswa/harian', [AbsensiController::class, 'absensiHarian'])->name('absensi.harian');
    Route::post('/absensi-siswa/harian/update', [AbsensiController::class, 'updateHarian'])->name('absensi.harian.update');

    Route::get('/rekap-absensi', [AbsensiController::class, 'rekapAbsensi'])->name('rekapabsensi');
    Route::get('/rekap-kelas', [AbsensiController::class, 'rekapPerKelas'])->name('rekapkelas');

    // Izin / Sakit
    Route::get('/izin-siswa', [IzinController::class, 'index'])->name('izin');
    Route::post('/izin-siswa', [IzinController::class, 'store'])->name('izin.store');

    // Hari Libur
    Route::get('/hari-libur', [HariLiburController::class, 'index'])->name('hariLibur');
    Route::post('/hari-libur', [HariLiburController::class, 'store'])->name('hariLibur.store');
    Route::post('/hari-libur/edit', [HariLiburController::class, 'edit'])->name('hariLibur.edit');
    Route::post('/hari-libur/{id}', [HariLiburController::class, 'update'])->name('hariLibur.update');
    Route::post('/hari-libur/delete/{id}', [HariLiburController::class, 'destroy'])->name('hariLibur.delete');
});
