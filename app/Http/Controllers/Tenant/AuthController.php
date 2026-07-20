<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    // Menampilkan Form Register HIMA
    public function showRegister()
    {
        return view('tenant.auth.register');
    }

    // Memproses Registrasi HIMA Baru
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:tenants,name',
            'email' => 'required|string|email|max:255|unique:tenants,email',
            'password' => 'required|string|min:6|confirmed',
            'description' => 'nullable|string',
        ]);

        Tenant::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'description' => $request->description,
        ]);

        return redirect()->route('tenant.login')->with('success', 'Registrasi Organisasi berhasil! Silakan login.');
    }

    // Menampilkan Form Login HIMA
    public function showLogin()
    {
        return view('tenant.auth.login');
    }

    // Memproses Login HIMA
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // Menggunakan guard tenant untuk memisahkan session dari user biasa
        if (Auth::guard('tenant')->attempt($credentials, $request->remember)) {
            $request->session()->regenerate();
            return redirect()->intended(route('tenant.dashboard'));
        }

        return back()->withErrors([
            'email' => 'Email atau password yang Anda masukkan salah.',
        ])->onlyInput('email');
    }

    // Memproses Logout HIMA
    public function logout(Request $request)
    {
        Auth::guard('tenant')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('tenant.login')->with('success', 'Berhasil keluar dari dashboard organisasi.');
    }
}