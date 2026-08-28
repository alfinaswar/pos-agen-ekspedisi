<?php

use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DivisiController;
use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\MasterPaketHargaController;
use App\Http\Controllers\PekerjaanKurirController;
use App\Http\Controllers\PendaftaranTenantController;
use App\Http\Controllers\PengumumanController;
use App\Http\Controllers\ReimbursementController;
use App\Http\Controllers\TagihanPembayaranController;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\TransaksiController;
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

Route::get('/', function () {
    return view('welcome');
});
Route::get('/landing-page', [LandingPageController::class, 'index'])->name('landing-page');
Route::get('/daftar', [LandingPageController::class, 'daftar'])->name('daftar');
Route::post('/pendaftaran-tenant/kirim', [PendaftaranTenantController::class, 'store'])->name('pendaftaran-tenant.store');

Auth::routes();

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
    Route::get('/manajemen-tenant', [DashboardController::class, 'indexTenant'])->name('dashboard.manajemen-tenant');
    Route::get('/', [DashboardController::class, 'index'])->name('home');
    Route::get('/ekspedisi', [App\Http\Controllers\EkspedisiController::class, 'index'])->name('ekspedisi.index');
    Route::get('/ekspedisi/data', [App\Http\Controllers\EkspedisiController::class, 'data'])->name('ekspedisi.data');
    Route::get('/ekspedisi/create', [App\Http\Controllers\EkspedisiController::class, 'create'])->name('ekspedisi.create');
    Route::post('/ekspedisi', [App\Http\Controllers\EkspedisiController::class, 'store'])->name('ekspedisi.store');
    Route::get('/ekspedisi/{id}', [App\Http\Controllers\EkspedisiController::class, 'show'])->name('ekspedisi.show');
    Route::get('/ekspedisi/{id}/edit', [App\Http\Controllers\EkspedisiController::class, 'edit'])->name('ekspedisi.edit');
    Route::put('/ekspedisi/{id}', [App\Http\Controllers\EkspedisiController::class, 'update'])->name('ekspedisi.update');
    Route::delete('/ekspedisi/{id}', [App\Http\Controllers\EkspedisiController::class, 'destroy'])->name('ekspedisi.destroy');

    //route transaksi
    Route::get('/transaksi/export', [TransaksiController::class, 'export'])->name('transaksi.export');
    Route::patch('/transaksi/{transaksi}/update-status', [TransaksiController::class, 'updateStatus'])->name('transaksi.updateStatus');
    Route::post('/transaksi/bulk-update-status', [TransaksiController::class, 'bulkUpdateStatus'])->name('transaksi.bulkUpdateStatus');
    Route::resource('transaksi', TransaksiController::class);
    //route absensi
    Route::get('/absensi/export', [AbsensiController::class, 'export'])->name('absensi.export');
    Route::post('/absensi/{absensi}/approve', [AbsensiController::class, 'approve'])->name('absensi.approve');
    Route::post('/absensi/bulk-approve', [AbsensiController::class, 'bulkApprove'])->name('absensi.bulkApprove');
    Route::resource('absensi', AbsensiController::class);
    // route rembes
    Route::get('/reimbursement/export', [ReimbursementController::class, 'export'])->name('reimbursement.export');
    Route::resource('reimbursement', ReimbursementController::class);


        Route::get('pekerjaan-kurir/export', [PekerjaanKurirController::class, 'Export'])->name('pekerjaan-kurir.export');
        Route::resource('pekerjaan-kurir', PekerjaanKurirController::class);
        Route::post('pekerjaan-kurir/bulk-verify', [PekerjaanKurirController::class, 'BulkVerify'])->name('pekerjaan-kurir.bulkVerify');

    Route::resource('divisi', DivisiController::class);
    Route::resource('pengumuman', PengumumanController::class);
    Route::resource('tenant', TenantController::class);
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::resource('users', UserController::class);
    Route::resource('master-paket-harga', MasterPaketHargaController::class);
    Route::get('/laporan/export', [LaporanController::class, 'exportExcel'])->name('laporan.export');

    // ROUTE UNTUK MANAJEMEN TENANT\
    Route::resource('pendaftaran-tenant', PendaftaranTenantController::class);

    Route::post('pendaftaran-tenant/{PendaftaranTenant}/verifikasi', [PendaftaranTenantController::class, 'Verifikasi'])->name('pendaftaran-tenant.verifikasi');

    Route::get('tagihan-pembayaran', [TagihanPembayaranController::class, 'Index'])->name('tagihan-pembayaran.index');
    Route::get('tagihan-pembayaran/create', [TagihanPembayaranController::class, 'Create'])->name('tagihan-pembayaran.create');
    Route::post('tagihan-pembayaran', [TagihanPembayaranController::class, 'Store'])->name('tagihan-pembayaran.store');
    Route::get('tagihan-pembayaran/{TagihanPembayaran}', [TagihanPembayaranController::class, 'Show'])->name('tagihan-pembayaran.show'); // <-- Route Show
    Route::get('tagihan-pembayaran/{TagihanPembayaran}/edit', [TagihanPembayaranController::class, 'Edit'])->name('tagihan-pembayaran.edit');
    Route::put('tagihan-pembayaran/{TagihanPembayaran}', [TagihanPembayaranController::class, 'Update'])->name('tagihan-pembayaran.update');
    Route::delete('tagihan-pembayaran/{TagihanPembayaran}', [TagihanPembayaranController::class, 'Destroy'])->name('tagihan-pembayaran.destroy');

    Route::get('tagihan-pembayaran/{TagihanPembayaran}/konfirmasi', [TagihanPembayaranController::class, 'KonfirmasiForm'])->name('tagihan-pembayaran.konfirmasi');
    Route::post('tagihan-pembayaran/{TagihanPembayaran}/konfirmasi', [TagihanPembayaranController::class, 'KonfirmasiProses'])->name('tagihan-pembayaran.konfirmasi.proses');

    // ✅ Route Bulk Approve
    Route::post('tagihan-pembayaran/bulk-approve', [TagihanPembayaranController::class, 'BulkApprove'])->name('tagihan-pembayaran.bulkApprove');
});
