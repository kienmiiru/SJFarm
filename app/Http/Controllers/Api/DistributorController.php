<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Distributor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class DistributorController extends Controller
{
    // Menampilkan semua distributor
    public function index()
    {
        $distributors = Distributor::withCount(['request as request_count' => function ($query) {
            $query->where('status_id', 1);
        }])->get();

        return response()->json([
            'status' => 'success',
            'data' => $distributors
        ]);
    }

    // Menyimpan distributor baru
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'          => 'required|string|max:255',
            'address'       => 'required|string|max:255',
            'email'         => 'required|email|max:255|unique:distributors,email',
            'phone_number'  => 'required|string|max:255',
            'username'      => 'required|string|max:255|unique:distributors,username',
            'password'      => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $distributor = Distributor::create([
            'name'          => $request->name,
            'address'       => $request->address,
            'email'         => $request->email,
            'phone_number'  => $request->phone_number,
            'username'      => $request->username,
            'password_hash' => Hash::make($request->password),
            'created_at'    => now(),
            'last_access'    => now()
        ]);

        return response()->json($distributor, 201);
    }

    // Menampilkan data distributor berdasarkan ID
    public function show($id)
    {
        $distributor = Distributor::findOrFail($id);
        return response()->json($distributor);
    }

    // Mengupdate data distributor
    public function update(Request $request, $id)
    {
        $distributor = Distributor::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name'          => 'required|string|max:255',
            'address'       => 'required|string|max:255',
            'email'         => 'required|email|max:255|unique:distributors,email,' . $id,
            'phone_number'  => 'required|string|max:255',
            'username'      => 'required|string|max:255|unique:distributors,username,' . $id,
            'password'      => 'nullable|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Isi data dengan benar.',
                'data' => $validator->errors(),
            ], 422);
        }

        $distributor->update([
            'name'          => $request->name ?? $distributor->name,
            'address'       => $request->address ?? $distributor->address,
            'email'         => $request->email ?? $distributor->email,
            'phone_number'  => $request->phone_number ?? $distributor->phone_number,
            'username'      => $request->username ?? $distributor->username,
            'password_hash' => $request->password ? Hash::make($request->password) : $distributor->password_hash,
            'updated_at'    => now(),
        ]);

        return response()->json([
            'status' => 'success',
            'data' => $distributor
        ]);
    }

    // Menghapus data distributor (soft delete)
    public function destroy($id)
    {
        $distributor = Distributor::findOrFail($id);
        $distributor->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'Distributor deleted successfully.'
        ]);
    }
}
