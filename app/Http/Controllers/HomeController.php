<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event; 
use App\Models\Category;
use App\Models\Partner;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        // 1. Query Dasar: Ambil event dari Tenant yang berstatus VERIFIED *ATAU* milik Admin sendiri (tenant_id kosong/NULL)
        $query = Event::with(['category', 'partner', 'tenant'])
            ->where(function ($q) {
                $q->whereHas('tenant', function ($tenantQuery) {
                    $tenantQuery->where('status', 'verified');
                })
                ->orWhereNull('tenant_id'); // Menjamin event lama/asli milik Admin tetap muncul
            })
            ->latest();

        // 2. Fitur Pencarian / Filter Kategori (Jika parameter 'category' tersedia di URL)
        if ($request->has('category') && $request->category != '') {
            $query->where('category_id', $request->category);
        }

        // 3. Eksekusi query dengan sistem Paginasi (Menampilkan 9 event per halaman)
        $events = $query->paginate(9);

        // 4. Ambil data master pendukung untuk komponen UI di halaman utama
        $categories = Category::all();
        $partners = Partner::all();

        // 5. Kirim semua kumpulan data ke dalam view landing page 'home'
        return view('home', compact('events', 'categories', 'partners'));
    }
}