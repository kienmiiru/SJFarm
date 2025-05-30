<?php

use App\Http\Controllers\Api\AdminRequestController;
use App\Http\Controllers\Api\DistributorController;
use App\Http\Controllers\Api\FruitController;
use App\Http\Controllers\Api\HarvestController;
use App\Http\Controllers\Api\RequestController;
use App\Http\Controllers\PrediksiController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/admin/dashboard', function () {
    return view('admin.dashboard');
});

Route::get('/admin/prediksi', [PrediksiController::class, 'index'])->name('prediksi.index');
Route::view('/admin/stok', 'admin.stok');
Route::view('/admin/permintaan-pembelian', 'admin.permintaan-pembelian');
Route::view('/admin/distributor', 'admin.distributor');
Route::view('/admin/panen', 'admin.panen');

Route::get('/admin/api/fruits', [FruitController::class, 'index']);
Route::post('/admin/api/fruits', [FruitController::class, 'store']);
Route::put('/admin/api/fruits/{id}', [FruitController::class, 'update']);

Route::get('/admin/api/harvests', [HarvestController::class, 'index']);
Route::post('/admin/api/harvests', [HarvestController::class, 'store']);
Route::put('/admin/api/harvests/{id}', [HarvestController::class, 'update']);
Route::delete('/admin/api/harvests/{id}', [HarvestController::class, 'destroy']);

Route::view('/distributor/dashboard', 'distributor.dashboard');
Route::view('/distributor/buah', 'distributor.buah');
Route::view('/distributor/pemesanan', 'distributor.pemesanan');
Route::view('/distributor/riwayat', 'distributor.riwayat');

Route::get('/distributor/api/fruits', [FruitController::class, 'index']);

Route::get('/distributor/api/requests', [RequestController::class, 'index']);
Route::get('/distributor/api/requests/{id}', [RequestController::class, 'show']);
Route::post('/distributor/api/requests', [RequestController::class, 'store']);

Route::get('/admin/api/requests', [AdminRequestController::class, 'index']);
Route::patch('/admin/api/requests/{id}/approve', [AdminRequestController::class, 'approve']);
Route::patch('/admin/api/requests/{id}/reject', [AdminRequestController::class, 'reject']);

Route::get('/admin/api/distributors', [DistributorController::class, 'index']);
Route::post('/admin/api/distributors', [DistributorController::class, 'store']);
Route::get('/admin/api/distributors/{id}', [DistributorController::class, 'show']);
Route::put('/admin/api/distributors/{id}', [DistributorController::class, 'update']);
Route::delete('/admin/api/distributors/{id}', [DistributorController::class, 'destroy']);