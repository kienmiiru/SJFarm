<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Distributor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    // Show login form
    public function showLogin()
    {
        if (session('is_authenticated', false)) {
            if (session('admin_id')) {
                return redirect('/admin/dashboard');
            } elseif (session('distributor_id')) {
                return redirect('/distributor/dashboard');
            }
        }
        return view('login');
    }

    // Handle login
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ]);

        $distributor = Distributor::where('username', $credentials['login'])
            ->orWhere('username', $credentials['login'])
            ->first();
        
        if ($distributor && Hash::check($credentials['password'], $distributor->password_hash)) {
            session(['is_authenticated' => true, 'distributor_id' => $distributor->id]);
            return redirect('/distributor/dashboard');
        }

        $admin = Admin::where('username', $credentials['login'])->first();

        if ($admin && Hash::check($credentials['password'], $admin->password_hash)) {
            session(['is_authenticated' => true, 'admin_id' => $admin->id]);
            return redirect('/admin/dashboard');
        }

        return view('login', ['message' => 'Data Tidak Sesuai!']);
    }

    // Logout
    public function logout()
    {
        session()->forget('is_authenticated');
        session()->forget('distributor_id');
        session()->forget('admin_id');
        return redirect('/');
    }
}
