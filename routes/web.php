<?php

use App\Http\Controllers\Api\FruitController;
use App\Http\Controllers\Api\HarvestController;
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
Route::view('/admin/panen', 'admin.panen');

Route::get('/admin/api/fruits', [FruitController::class, 'index']);
Route::post('/admin/api/fruits', [FruitController::class, 'store']);
Route::put('/admin/api/fruits/{id}', [FruitController::class, 'update']);

Route::get('/admin/api/harvests', [HarvestController::class, 'index']);
Route::post('/admin/api/harvests', [HarvestController::class, 'store']);
Route::put('/admin/api/harvests/{id}', [HarvestController::class, 'update']);
Route::delete('/admin/api/harvests/{id}', [HarvestController::class, 'destroy']);