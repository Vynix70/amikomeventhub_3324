<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    // Fungsi untuk mengarahkan pengguna ke halaman login Google
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    // Fungsi untuk menangani kembalian data (Callback) dari Google
    public function handleGoogleCallback()
    {
        try {
            // Mengambil data user dari server Google
            $googleUser = Socialite::driver('google')->user();
            
            // Cek apakah email user sudah terdaftar di database lokal
            $user = User::where('email', $googleUser->getEmail())->first();

            if ($user) {
                // Jika user sudah ada, perbarui data Google ID dan Avatar-nya (jika belum ada)
                $user->update([
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                ]);
            } else {
                // Jika user belum terdaftar, otomatis buatkan akun baru (Instant Register)
                $user = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                    // Isi password acak yang aman karena login utama menggunakan Google SSO
                    'password' => Hash::make(Str::random(24)), 
                ]);
            }

            // Loginkan pengguna ke dalam sesi sistem Laravel
            Auth::login($user);

            // Redirect ke halaman utama / katalog event untuk memesan tiket
            return redirect()->route('home')->with('success', 'Selamat Datang! Login via Google Berhasil.');

        } catch (\Exception $e) {
            // Jika terjadi kegagalan sistem (koneksi dibatalkan, dll)
            return redirect()->route('login')->with('error', 'Gagal melakukan login via Google, silakan coba lagi.');
        }
    }
}