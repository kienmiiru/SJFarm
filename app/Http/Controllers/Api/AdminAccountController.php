<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AdminAccountController extends Controller
{
    public function update(Request $request)
    {
        $adminId = session('admin_id');

        if (!$adminId) {
            return response()->json([
                'status' => 'error',
                'message' => 'Sesi tidak valid.'
            ], 401);
        }

        $messages = [
            'username.unique' => 'Username sudah digunakan.',
            'password.min' => 'Password harus memiliki minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak sesuai.',
        ];

        $validator = Validator::make($request->all(), [
            'username' => 'sometimes|string|max:15|unique:admins,username,' . $adminId,
            'password' => 'sometimes|string|min:8|confirmed',
        ], $messages);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Isi data dengan benar.',
                'data' => $validator->errors(),
            ], 422);
        }

        $admin = Admin::find($adminId);

        if (!$admin) {
            return response()->json([
                'status' => 'error',
                'message' => 'Admin tidak ditemukan.'
            ], 404);
        }

        $admin->username = $request->input('username');

        if ($request->filled('password')) {
            $admin->password_hash = Hash::make($request->input('password'));
        }

        $admin->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Data berhasil diperbaharui.'
        ]);
    }
}