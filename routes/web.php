<?php

use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\ReimbursementController;
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

Auth::routes();

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
Route::middleware(['auth'])->group(function () {
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
    Route::resource('transaksi', TransaksiController::class);
    //route absensi
    Route::get('/absensi/export', [AbsensiController::class, 'export'])->name('absensi.export');
    Route::resource('absensi', AbsensiController::class);
    // route rembes
    Route::get('/reimbursement/export', [ReimbursementController::class, 'export'])->name('reimbursement.export');
    Route::resource('reimbursement', ReimbursementController::class);
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::resource('users', UserController::class);
    Route::get('/laporan/export', [LaporanController::class, 'exportExcel'])->name('laporan.export');

});
