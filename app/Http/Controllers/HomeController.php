<?php

namespace App\Http\Controllers;

use App\Models\Partner;
use App\Models\Category; // Impor model Category
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // Mengambil semua data partner
        $partners = Partner::latest()->get();

        // Mengambil semua data kategori untuk ditampilkan di beranda publik
        $categories = Category::latest()->get();

        // Oper data ke view halaman depan (biasanya bernama 'welcome' atau 'home')
        return view('welcome', compact('partners', 'categories'));
    }
}