<?php

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

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::middleware(['auth'])->group(function () {
    Route::get('/ekspedisi', [App\Http\Controllers\EkspedisiController::class, 'index'])->name('ekspedisi.index');
    Route::get('/ekspedisi/data', [App\Http\Controllers\EkspedisiController::class, 'data'])->name('ekspedisi.data');
    Route::get('/ekspedisi/create', [App\Http\Controllers\EkspedisiController::class, 'create'])->name('ekspedisi.create');
    Route::post('/ekspedisi', [App\Http\Controllers\EkspedisiController::class, 'store'])->name('ekspedisi.store');
    Route::get('/ekspedisi/{id}', [App\Http\Controllers\EkspedisiController::class, 'show'])->name('ekspedisi.show');
    Route::get('/ekspedisi/{id}/edit', [App\Http\Controllers\EkspedisiController::class, 'edit'])->name('ekspedisi.edit');
    Route::put('/ekspedisi/{id}', [App\Http\Controllers\EkspedisiController::class, 'update'])->name('ekspedisi.update');
    Route::delete('/ekspedisi/{id}', [App\Http\Controllers\EkspedisiController::class, 'destroy'])->name('ekspedisi.destroy');
});
