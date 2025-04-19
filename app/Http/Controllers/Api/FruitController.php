<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Fruit;
use Illuminate\Http\Request;

class FruitController extends Controller
{
    // Melihat data buah
    public function index()
    {
        $fruits = Fruit::all();
        return response()->json([
            'status' => 'success',
            'data' => $fruits
        ]);
    }

    // Menambah data buah baru
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'stock_in_kg' => 'required|numeric|min:0',
            'price_per_kg' => 'required|integer|min:0',
        ]);

        $fruit = Fruit::create($request->only(['name', 'stock_in_kg', 'price_per_kg']));

        return response()->json([
            'status' => 'success',
            'message' => 'Data buah berhasil ditambahkan.',
            'data' => $fruit
        ], 201);
    }

    // Mengedit data buah
    public function update(Request $request, $id)
    {
        $fruit = Fruit::findOrFail($id);

        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'stock_in_kg' => 'sometimes|required|numeric|min:0',
            'price_per_kg' => 'sometimes|required|integer|min:0',
        ]);

        $fruit->update($request->only(['name', 'stock_in_kg', 'price_per_kg']));

        return response()->json([
            'status' => 'success',
            'message' => 'Data buah berhasil diperbarui.',
            'data' => $fruit
        ]);
    }
}