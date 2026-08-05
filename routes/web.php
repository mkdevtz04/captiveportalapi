<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PaymentController::class, 'voucher'])->name('voucher');
Route::get('/portal', [PaymentController::class, 'index'])->name('portal');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/admin/api/chart', [AdminController::class, 'chartData'])->name('admin.chart');
    Route::get('/mikrotik', [App\Http\Controllers\MikroTikController::class, 'index'])->name('mikrotik.index');
    Route::post('/mikrotik/settings', [App\Http\Controllers\MikroTikController::class, 'storeSettings'])->name('mikrotik.settings');
    Route::post('/mikrotik/test', [App\Http\Controllers\MikroTikController::class, 'testConnection'])->name('mikrotik.test');
    Route::post('/mikrotik/voucher', [App\Http\Controllers\MikroTikController::class, 'createVoucher'])->name('mikrotik.voucher');
    Route::delete('/mikrotik/voucher/{id}', [App\Http\Controllers\MikroTikController::class, 'destroyVoucher'])->name('mikrotik.voucher.destroy');
});

Route::get('/admin', function () {
    return redirect()->route('dashboard');
})->middleware('auth');

Route::get('/dashboard', [AdminController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

require __DIR__.'/auth.php';
