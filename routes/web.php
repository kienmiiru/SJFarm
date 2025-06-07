<?php

use App\Http\Controllers\Api\AdminAccountController;
use App\Http\Controllers\Api\AdminRequestController;
use App\Http\Controllers\Api\DistributorAccountController;
use App\Http\Controllers\Api\DistributorController;
use App\Http\Controllers\Api\FruitController;
use App\Http\Controllers\Api\HarvestController;
use App\Http\Controllers\Api\RequestController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\PrediksiController;
use App\Http\Controllers\RequestImportController;
use App\Http\Middleware\EnsureUserHasRole;
use App\Models\Admin;
use App\Models\Distributor;
use App\Models\Request;
use App\Models\Fruit;
use Illuminate\Support\Facades\Route;

Route::get('/', [LoginController::class, 'showLogin'])->name('login');
Route::post('/', [LoginController::class, 'login']);
Route::get('/logout', [LoginController::class, 'logout']);

Route::middleware(EnsureUserHasRole::class.':admin')->group(function () {
    // Route::view('/admin/dashboard', 'admin.dashboard');
    Route::get('/admin/dashboard', function () {
        $admin = Admin::find(session('admin_id'));
        $unconfirmedRequestsCount = Request::where('status_id', 1)->count();
        $incomeThisMonth = Request::whereMonth('requested_date', now()->month)
            ->whereYear('requested_date', now()->year)
            ->where('status_id', 2)
            ->sum('total_price');
        $distributorsCount = Distributor::count();
        $fruits = Fruit::all();
        return view('admin.dashboard', compact([
            'admin',
            'unconfirmedRequestsCount',
            'incomeThisMonth',
            'distributorsCount',
            'fruits'
        ]));
    });

    Route::get('/admin/prediksi', [PrediksiController::class, 'index'])->name('prediksi.index');
    Route::view('/admin/stok', 'admin.stok');
    Route::view('/admin/permintaan-pembelian', 'admin.permintaan-pembelian');
    Route::view('/admin/distributor', 'admin.distributor');
    Route::view('/admin/panen', 'admin.panen');
    Route::get('/admin/akun', function () {
        $admin = Admin::find(session('admin_id'));
        return view('admin.akun', compact(['admin']));
    });

    Route::get('/admin/api/fruits', [FruitController::class, 'index']);
    Route::post('/admin/api/fruits', [FruitController::class, 'store']);
    Route::put('/admin/api/fruits/{id}', [FruitController::class, 'update']);

    Route::get('/admin/api/harvests', [HarvestController::class, 'index']);
    Route::post('/admin/api/harvests', [HarvestController::class, 'store']);
    Route::put('/admin/api/harvests/{id}', [HarvestController::class, 'update']);
    Route::delete('/admin/api/harvests/{id}', [HarvestController::class, 'destroy']);

    Route::get('/admin/api/requests', [AdminRequestController::class, 'index']);
    Route::patch('/admin/api/requests/{id}/approve', [AdminRequestController::class, 'approve']);
    Route::patch('/admin/api/requests/{id}/reject', [AdminRequestController::class, 'reject']);

    Route::post('/admin/api/requests/validate-xlsx', [RequestImportController::class, 'validateXlsx']);
    Route::post('/admin/api/requests/import-xlsx', [RequestImportController::class, 'importXlsx']);

    Route::get('/admin/api/distributors', [DistributorController::class, 'index']);
    Route::post('/admin/api/distributors', [DistributorController::class, 'store']);
    Route::get('/admin/api/distributors/{id}', [DistributorController::class, 'show']);
    Route::put('/admin/api/distributors/{id}', [DistributorController::class, 'update']);
    Route::delete('/admin/api/distributors/{id}', [DistributorController::class, 'destroy']);
   
    Route::put('/admin/api/account', [AdminAccountController::class, 'update']);
});

Route::middleware(EnsureUserHasRole::class.':distributor')->group(function () {
    // Route::view('/distributor/dashboard', 'distributor.dashboard');
    Route::get('/distributor/dashboard', function () {
        $distributor = Distributor::find(session('distributor_id'));
        $unconfirmedRequestsCount = Request::where('status_id', 1)
            ->where('distributor_id', session('distributor_id'))
            ->count();
        $finishedRequestsCount = Request::where('status_id', 2)
            ->where('distributor_id', session('distributor_id'))
            ->count();
        return view('distributor.dashboard', compact(['distributor', 'unconfirmedRequestsCount', 'finishedRequestsCount']));
    });
    
    Route::view('/distributor/buah', 'distributor.buah');
    Route::view('/distributor/pemesanan', 'distributor.pemesanan');
    Route::view('/distributor/riwayat', 'distributor.riwayat');
    Route::get('/distributor/akun', function () {
        $distributor = Distributor::find(session('distributor_id'));
        return view('distributor.akun', compact(['distributor']));
    });

    Route::get('/distributor/api/fruits', [FruitController::class, 'index']);

    Route::get('/distributor/api/requests', [RequestController::class, 'index']);
    Route::get('/distributor/api/requests/{id}', [RequestController::class, 'show']);
    Route::post('/distributor/api/requests', [RequestController::class, 'store']);
   
    Route::put('/distributor/api/account', [DistributorAccountController::class, 'update']);
});
