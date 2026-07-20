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
     * (Sudah digabung dengan trik manipulasi jam pelaksanaan)
     */
    public function store(Request $request)
    {
        // 1. Validasi input form (tambahkan 'time' ke dalam rule validasi & perketat poster)
        $request->validate([
            'title'       => 'required|string|max:255',
            'date'        => 'required|date',
            'time'        => 'required', // Validasi input jam baru
            'category_id' => 'required|exists:categories,id',
            'location'    => 'required|string|max:255',
            'price'       => 'required|numeric|min:0',
            'stock'       => 'required|integer|min:1',
            'poster_path' => 'required|image|mimes:jpeg,png,jpg|max:2048', // Diwajibkan saat membuat event baru
        ]);

        // 2. TRIK GABUNGKAN TANGGAL & JAM
        // Menggabungkan format "YYYY-MM-DD" dan "HH:MM" menjadi "YYYY-MM-DD HH:MM:00"
        $fullDateTime = $request->date . ' ' . $request->time . ':00';

        // 3. Proses upload file poster ke folder storage/app/public/posters
        $posterPath = null;
        if ($request->hasFile('poster_path')) {
            $posterPath = $request->file('poster_path')->store('posters', 'public');
        }

        // 4. Simpan ke Database dengan memetakan array secara manual demi keamanan data
        Event::create([
            'tenant_id'   => Auth::guard('tenant')->id(),
            'title'       => $request->title,
            'date'        => $fullDateTime, // Sematkan string gabungan tanggal + jam utuh ke kolom date
            'category_id' => $request->category_id,
            'location'    => $request->location,
            'price'       => $request->price,
            'stock'       => $request->stock,
            'poster_path' => $posterPath,
        ]);

        return redirect()->route('tenant.events.index')->with('success', 'Event berhasil diterbitkan beserta jam pelaksanaannya!');
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
     * (Sudah disesuaikan dengan validasi jam dan penggabungan datetime dari data baru)
     */
    public function update(Request $request, Event $event)
    {
        // 1. Proteksi akses silang antar tenant
        if ($event->tenant_id !== Auth::guard('tenant')->id()) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengubah event ini.');
        }

        // 2. Validasi data (menyerap aturan ketat dan validasi 'time' dari kode baru)
        $request->validate([
            'title'       => 'required|string|max:255',
            'date'        => 'required|date',
            'time'        => 'required', // Wajib diisi jamnya saat update
            'category_id' => 'required|exists:categories,id',
            'location'    => 'required|string|max:255',
            'price'       => 'required|numeric|min:0',
            'stock'       => 'required|integer|min:1',
            'poster_path' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // Nullable karena opsional saat edit
        ]);

        // 3. Satukan Tanggal dan Jam baru
        $fullDateTime = $request->date . ' ' . $request->time . ':00';

        // 4. Logika Update Poster (menggunakan Storage::disk untuk menghapus file lama agar lebih clean)
        $posterPath = $event->poster_path; 
        if ($request->hasFile('poster_path')) {
            // Hapus file poster lama dari storage jika berkas fisiknya terdeteksi ada
            if ($posterPath && Storage::disk('public')->exists($posterPath)) {
                Storage::disk('public')->delete($posterPath);
            }
            
            // Simpan file baru ke folder 'posters' dengan disk 'public'
            $posterPath = $request->file('poster_path')->store('posters', 'public');
        }

        // 5. Update data ke database secara manual/explicit demi mass-assignment safety
        $event->update([
            'title'       => $request->title,
            'date'        => $fullDateTime, // Jam masuk ke kolom date tanpa ubah struktur DB
            'category_id' => $request->category_id,
            'location'    => $request->location,
            'price'       => $request->price,
            'stock'       => $request->stock,
            'poster_path' => $posterPath,
        ]);

        return redirect()->route('tenant.events.index')->with('success', 'Detail event berhasil diperbarui beserta waktunya!');
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