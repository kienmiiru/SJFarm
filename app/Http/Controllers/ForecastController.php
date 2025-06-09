<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class ForecastController extends Controller
{
    public function forecast()
    {
        // Ambil data permintaan gabung dengan nama buah
        $requests = DB::table('requests')
            ->join('fruits', 'requests.fruit_id', '=', 'fruits.id')
            ->select('fruits.name as fruit_name', 'requested_stock_in_kg', 'requested_date')
            ->get();

        // Kelompokkan berdasarkan nama buah
        $grouped = [];
        foreach ($requests as $request) {
            $grouped[$request->fruit_name][] = [
                'requested_stock_in_kg' => $request->requested_stock_in_kg,
                'requested_date' => $request->requested_date,
            ];
        }

        // Simpan input sebagai JSON sementara
        $inputPath = storage_path('app/temp_input.json');
        file_put_contents($inputPath, json_encode($grouped));

        // Jalankan script Python
        $scriptPath = base_path('scripts/arima_forecast.py');
        $process = new Process(['python', $scriptPath], null, null, file_get_contents($inputPath));
        $process->run();

        // Tangani error
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $forecastResults = json_decode($process->getOutput(), true);

        // Kirim ke view
        return view('admin.prediksi', ['results' => $forecastResults]);
    }
}
