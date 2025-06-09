<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Fruit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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
        $messages = [
            'name.unique' => 'Nama buah sudah ada.'
        ];

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:fruits,name',
            'stock_in_kg' => 'required|numeric|min:0',
            'price_per_kg' => 'required|integer|min:0',
        ], $messages);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Isi data dengan benar.',
                'data' => $validator->errors(),
            ], 422);
        }

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

        $messages = [
            'name.unique' => 'Nama buah sudah ada.',
            'price_per_kg.gt' => 'Harga buah harus lebih dari 0.'
        ];

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:fruits,name,' . $fruit->id,
            'stock_in_kg' => 'required|numeric|min:0',
            'price_per_kg' => 'required|integer|gt:0',
        ], $messages);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Isi data dengan benar.',
                'data' => $validator->errors(),
            ], 422);
        }

        $fruit->update($request->only(['name', 'stock_in_kg', 'price_per_kg']));

        return response()->json([
            'status' => 'success',
            'message' => 'Data buah berhasil diperbarui.',
            'data' => $fruit
        ]);
    }
}