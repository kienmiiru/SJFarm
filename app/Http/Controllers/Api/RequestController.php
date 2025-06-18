<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request as HttpRequest; // Agar tidak konflik dengan model Request
use App\Models\Request;
use App\Models\Fruit;
use Illuminate\Support\Facades\Validator;

class RequestController extends Controller
{
    public function index(HttpRequest $httpRequest)
    {
        $distributorId = session('distributor_id');

        $query = Request::with('fruit', 'status')
            ->where('distributor_id', $distributorId)
            ->orderBy('requested_date', 'desc');

        if ($httpRequest->has('status') && in_array($httpRequest->input('status'), ['pending', 'approved', 'rejected'])) {
            $query->whereHas('status', function ($q) use ($httpRequest) {
                $q->where('status', $httpRequest->input('status'));
            });
        }

        $requests = $query->paginate(10);

        return response()->json([
            'status' => 'success',
            'data' => $requests->items(),
            'pagination' => [
                'current_page' => $requests->currentPage(),
                'last_page' => $requests->lastPage(),
                'per_page' => $requests->perPage(),
                'total' => $requests->total(),
            ]
        ]);
    }

    public function show($id)
    {
        $distributorId = session('distributor_id');

        $request = Request::with('fruit', 'status')
            ->where('id', $id)
            ->where('distributor_id', $distributorId)
            ->firstOrFail();

        return response()->json($request);
    }

    public function store(HttpRequest $httpRequest)
    {
        $validator = Validator::make($httpRequest->all(), [
            'fruit_id' => 'required|exists:fruits,id',
            'requested_stock_in_kg' => 'required|integer|min:1'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Isi data dengan benar.',
                'data' => $validator->errors(),
            ], 422);
        }

        $distributorId = session('distributor_id');
        $fruit = Fruit::findOrFail($httpRequest->fruit_id);

        $stock = $httpRequest->requested_stock_in_kg;

        if ($fruit->stock_in_kg < $stock) {
            return response()->json([
                'status' => 'error',
                'message' => 'Stok buah tidak mencukupi.'
            ], 422);
        }

        $totalPrice = $fruit->price_per_kg * $stock;

        $request = Request::create([
            'requested_stock_in_kg' => $stock,
            'total_price' => $totalPrice,
            'requested_date' => now(),
            'fruit_id' => $fruit->id,
            'distributor_id' => $distributorId,
            'status_id' => 1 // status: pending
        ]);

        return response()->json([
            'status' => 'success',
            'data' => $request
        ], 201);
    }
}
