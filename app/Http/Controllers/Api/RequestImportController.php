<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request as HttpRequest; // agar tidak konflik dengan model Request
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Storage;
use App\Models\Request as Request;
use App\Models\Fruit;
use App\Models\Distributor;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RequestImportController extends Controller
{
    public function validateXlsx(HttpRequest $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls',
        ]);

        $file = $request->file('file');

        $spreadsheet = IOFactory::load($file->getRealPath());
        $worksheet = $spreadsheet->getActiveSheet();
        $rows = $worksheet->toArray();

        $errors = [];

        foreach (array_slice($rows, 1) as $index => $row) {
            $rowNum = $index + 2; // considering headers in row 1
            [$stock, $price, $date, $fruitName, $distributorName] = $row;

            if (!is_numeric($stock) || !is_numeric($price)) {
                $errors[] = [
                    'row' => $rowNum,
                    'error' => 'Stok dan harga harus angka.'
                ];
            }

            if (!$fruitName || !Fruit::where('name', $fruitName)->exists()) {
                $errors[] = [
                    'row' => $rowNum,
                    'error' => "Buah '{$fruitName}' tidak ditemukan."
                ];
            }

            if (!$distributorName || !Distributor::where('name', $distributorName)->exists()) {
                $errors[] = [
                    'row' => $rowNum,
                    'error' => "Distributor '{$distributorName}' tidak ditemukan."
                ];
            }

            if (!$date || !strtotime($date)) {
                $errors[] = [
                    'row' => $rowNum,
                    'error' => "Format tanggal tidak valid: '{$date}'"
                ];
            }
        }

        if (count($errors)) {
            return response()->json([
                'status' => 'error',
                'errors' => $errors
            ], 200);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Validasi berhasil.'
        ], 200);
    }

    public function importXlsx(HttpRequest $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls',
        ]);

        $file = $request->file('file');

        $spreadsheet = IOFactory::load($file->getRealPath());
        $worksheet = $spreadsheet->getActiveSheet();
        $rows = $worksheet->toArray();

        DB::beginTransaction();
        try {
            foreach (array_slice($rows, 1) as $row) {
                [$stock, $price, $date, $fruitName, $distributorName] = $row;

                $fruit = Fruit::where('name', $fruitName)->first();
                $distributor = Distributor::where('name', $distributorName)->first();

                Request::create([
                    'requested_stock_in_kg' => (int) $stock,
                    'total_price' => (int) $price,
                    'requested_date' => \Carbon\Carbon::parse($date),
                    'status_changed_date' => now(),
                    'status_changed_message' => 'Diimpor dari XLSX',
                    'fruit_id' => $fruit->id,
                    'distributor_id' => $distributor->id,
                    'status_id' => 2,
                ]);
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Data berhasil diimpor.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal: ' . $e->getMessage(),
            ], 500);
        }
    }
}
