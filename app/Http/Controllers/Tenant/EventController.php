<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage; // Menggunakan Facade Storage (Lebih Modern & Aman)

class EventController extends Controller
{
    /**
     * 1. Menampilkan daftar event milik HIMA/Tenant yang sedang login saja
     */
    public function index()
    {
        $tenantId = Auth::guard('tenant')->id();
        $events = Event::where('tenant_id', $tenantId)->get();
        
        return view('tenant.events.index', compact('events'));
    }

    /**
     * 2. Menampilkan form tambah event baru
     */
    public function create()
    {
        $categories = Category::all();
        return view('tenant.events.create', compact('categories'));
    }

    /**
     * 3. Menyimpan data event baru ke database & memindahkan berkas ke Storage Public
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'date' => 'required|date',
            'location' => 'required|string|max:255',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
            'category_id' => 'required|exists:categories,id',
            'poster_path' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', 
        ]);

        $data = $request->all();
        $data['tenant_id'] = Auth::guard('tenant')->id();

        // Menyimpan berkas poster ke storage/app/public/posters menggunakan penamaan acak otomatis
        if ($request->hasFile('poster_path')) {
            $path = $request->file('poster_path')->store('posters', 'public');
            $data['poster_path'] = $path; // Hasil akhir di database: "posters/namafile_acak.jpg"
        }

        Event::create($data);

        return redirect()->route('tenant.events.index')->with('success', 'Event berhasil diterbitkan!');
    }

    /**
     * 4. Menampilkan form edit dengan proteksi akses silang antar tenant
     */
    public function edit(Event $event)
    {
        if ($event->tenant_id !== Auth::guard('tenant')->id()) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengelola event ini.');
        }

        $categories = Category::all();
        return view('tenant.events.edit', compact('event', 'categories'));
    }

    /**
     * 5. Memperbarui data event beserta penanganan berkas poster lama dan baru
     */
    public function update(Request $request, Event $event)
    {
        if ($event->tenant_id !== Auth::guard('tenant')->id()) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengubah event ini.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'date' => 'required|date',
            'location' => 'required|string|max:255',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
            'category_id' => 'required|exists:categories,id',
            'poster_path' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->all();

        if ($request->hasFile('poster_path')) {
            // Hapus file poster lama dari storage jika berkas fisiknya terdeteksi ada
            if ($event->poster_path && Storage::disk('public')->exists($event->poster_path)) {
                Storage::disk('public')->delete($event->poster_path);
            }
            
            // Simpan berkas poster baru ke dalam folder 'posters'
            $path = $request->file('poster_path')->store('posters', 'public');
            $data['poster_path'] = $path;
        }

        $event->update($data);

        return redirect()->route('tenant.events.index')->with('success', 'Event berhasil diperbarui!');
    }

    /**
     * 6. Menghapus data event dari sistem serta membersihkan file terkait di media penyimpanan
     */
    public function destroy(Event $event)
    {
        if ($event->tenant_id !== Auth::guard('tenant')->id()) {
            abort(403, 'Anda tidak memiliki hak akses untuk menghapus event ini.');
        }

        // Hapus file gambar dari penyimpanan fisik sebelum record data dihapus permanen
        if ($event->poster_path && Storage::disk('public')->exists($event->poster_path)) {
            Storage::disk('public')->delete($event->poster_path);
        }

        $event->delete();
        return redirect()->route('tenant.events.index')->with('success', 'Event berhasil dihapus.');
    }
}