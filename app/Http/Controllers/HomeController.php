<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event; 
use App\Models\Category; // Tambah Import Model Category
use App\Models\Partner;  // Tambah Import Model Partner

class HomeController extends Controller
{
    // SESUAIKAN: Menambahkan parameter Request agar bisa membaca query parameter di URL
    public function index(Request $request)
    {
        // 1. Buat query dasar untuk mengambil semua event beserta relasinya
        $query = Event::with(['category', 'partner'])->latest();

        // TAMBAHKAN LOGIKA FILTER: Jika URL memiliki parameter 'category' dan nilainya tidak kosong
        if ($request->has('category') && $request->category != '') {
            $query->where('category_id', $request->category);
        }

        // Eksekusi query untuk mendapatkan hasil akhir data event
        $events = $query->get();

        // 2. Ambil semua data kategori untuk tombol filter di bagian atas
        $categories = Category::all();

        // 3. Ambil semua data partner untuk section sponsor di bagian bawah
        $partners = Partner::all();

        // 4. Return ke view 'home' (sesuai nama di web.php) dengan membawa semua data
        return view('home', compact('events', 'categories', 'partners'));
    }
}