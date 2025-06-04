<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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

        $request->validate([
            'username' => 'sometimes|string|max:255|unique:admins,username,' . $adminId,
            'password' => 'sometimes|string|min:8|confirmed',
        ]);

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