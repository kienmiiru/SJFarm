<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Distributor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class DistributorAccountController extends Controller
{
    public function update(Request $request)
    {
        $distributorId = session('distributor_id');

        if (!$distributorId) {
            return response()->json([
                'status' => 'error',
                'message' => 'Sesi tidak valid.'
            ], 401);
        }

        $messages = [
            'name.unique' => 'Nama distributor sudah digunakan.',
            'username.unique' => 'Username sudah digunakan.',
            'email.unique' => 'Email sudah digunakan.',
            'phone_number.unique' => 'Nomor telepon sudah digunakan.'
        ];

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255|unique:distributors,name,' . $distributorId,
            'address' => 'sometimes|string|max:500',
            'email' => 'sometimes|email|max:255|unique:distributors,email,' . $distributorId,
            'phone_number' => 'sometimes|string|max:20|unique:distributors,phone_number,' . $distributorId,
            'username' => 'sometimes|string|max:20|unique:distributors,username,' . $distributorId,
            'password' => 'sometimes|string|min:8|confirmed',
        ], $messages);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Isi data dengan benar.',
                'data' => $validator->errors(),
            ], 422);
        }

        $distributor = Distributor::find($distributorId);

        if (!$distributor) {
            return response()->json([
                'status' => 'error',
                'message' => 'Distributor not found.'
            ], 404);
        }

        $distributor->fill($request->only([
            'name', 'address', 'email', 'phone_number', 'username'
        ]));

        if ($request->filled('password')) {
            $distributor->password_hash = Hash::make($request->password);
        }

        $distributor->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Data berhasil diperbaharui.',
            'distributor' => $distributor->only(['id', 'name', 'username', 'email'])
        ]);
    }
}
