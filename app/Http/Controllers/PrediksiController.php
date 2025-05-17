<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Carbon\Carbon;

class PrediksiController extends Controller
{
    public function index()
    {
        $rawData = DB::table('requests')
            ->select(DB::raw("fruit_id, DATE_FORMAT(requested_date, '%Y-%m') as month, SUM(requested_stock_in_kg) as total"))
            ->groupBy('fruit_id', DB::raw("DATE_FORMAT(requested_date, '%Y-%m')"))
            ->orderBy('month')
            ->get();

        $fruitNames = DB::table('fruits')->pluck('name', 'id');

        $historicalData = $rawData->groupBy('fruit_id')->map(function ($records) {
            return collect($this->normalizeTimeSeries($records));
        });

        // Le Python Binding
        $process = new Process(['python', base_path('scripts/predict.py'), json_encode($historicalData)]);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $predictions = json_decode($process->getOutput(), true);

        // dd(json_encode($historicalData), json_encode($predictions));
        return view('admin.prediksi', [
            'historicalData' => $historicalData,
            'predictions' => $predictions,
            'fruitNames' => $fruitNames
        ]);
    }
    public function normalizeTimeSeries($records)
    {
        $dates = $records->pluck('month')->map(fn($d) => Carbon::parse($d . '-01'))->sort();
        if ($dates->isEmpty()) return [];

        $start = $dates->first();
        $end = $dates->last();

        $fullMonths = [];
        while ($start <= $end) {
            $fullMonths[] = $start->format('Y-m');
            $start->addMonth();
        }

        // Map month to total, default 0 if doesn't exist
        $recordMap = $records->keyBy('month');
        $normalized = [];
        foreach ($fullMonths as $month) {
            $normalized[] = [
                'month' => $month,
                'total' => isset($recordMap[$month]) ? (float)$recordMap[$month]->total : 0.0
            ];
        }

        return $normalized;
    }
}
