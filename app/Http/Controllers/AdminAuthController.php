<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminAuthController extends Controller
{
    // Hardcoded admin credentials
    private const ADMIN_USERNAME = 'admin';
    private const ADMIN_PASSWORD = 'admin123';

    /**
     * Tampilkan halaman login admin
     */
    public function showLoginForm()
    {
        return view('admin.login_admin');
    }

    /**
     * Proses login admin (simple hardcoded check)
     */
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $username = $request->input('username');
        $password = $request->input('password');

        // Simple hardcoded validation (seperti Flutter)
        if ($username === self::ADMIN_USERNAME && $password === self::ADMIN_PASSWORD) {
            // Login berhasil, redirect ke dashboard
            return redirect()->route('admin.index_admin')->with('success', 'Selamat datang, Admin!');
        } else {
            // Login gagal, redirect back dengan error
            return redirect()->back()
                ->withInput($request->only('username'))
                ->with('error', 'Username atau password salah!');
        }
    }

    public function logout()
    {
        return redirect()->route('admin.login')->with('success', 'Berhasil logout!');
    }
}