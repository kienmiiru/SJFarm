<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Harvest;
use App\Models\Fruit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class HarvestController extends Controller
{
    public function index()
    {
        $harvests = Harvest::with('fruit')
            ->orderBy('harvest_date', 'desc')
            ->get()
            ->map(function ($harvest) {
                return [
                    'id' => $harvest->id,
                    'fruit' => $harvest->fruit->name,
                    'amount_in_kg' => $harvest->amount_in_kg,
                    'harvest_date' => $harvest->harvest_date,
                ];
            });

        return response()->json([
            'status' => 'success',
            'data' => $harvests,
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fruit_id' => 'required|exists:fruits,id',
            'amount_in_kg' => 'required|numeric|min:0',
            'harvest_date' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Isi data dengan benar.',
                'data' => $validator->errors(),
            ], 422);
        }

        $harvest = Harvest::create($request->only('fruit_id', 'amount_in_kg', 'harvest_date'));

        // Update stok buah
        $fruit = Fruit::find($request->fruit_id);
        $fruit->stock_in_kg += $request->amount_in_kg;
        $fruit->save();

        return response()->json([
            'status' => 'success',
            'data' => $harvest,
            'message' => 'Data panen berhasil ditambahkan.',
        ]);
    }

    public function update(Request $request, $id)
    {
        $harvest = Harvest::findOrFail($id);
        $validator = Validator::make($request->all(), [
            'amount_in_kg' => 'required|numeric|min:0',
            'harvest_date' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Isi data dengan benar.',
                'data' => $validator->errors(),
            ], 422);
        }

        // Update stok buah jika jumlah panen berubah
        $selisih = $request->amount_in_kg - $harvest->amount_in_kg;
        $fruit = $harvest->fruit;
        $fruit->stock_in_kg += $selisih;
        $fruit->save();

        $harvest->update($request->only('amount_in_kg', 'harvest_date'));

        return response()->json([
            'status' => 'success',
            'data' => $harvest,
            'message' => 'Data panen berhasil diperbarui.',
        ]);
    }

    public function destroy($id)
    {
        $harvest = Harvest::findOrFail($id);

        // Kurangi stok buah sesuai panen yang dihapus
        $fruit = $harvest->fruit;
        $fruit->stock_in_kg -= $harvest->amount_in_kg;
        $fruit->save();

        $harvest->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Data panen berhasil dihapus.',
            'data' => null,
        ]);
    }
}
