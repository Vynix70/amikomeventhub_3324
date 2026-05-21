<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Partner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage; // Untuk menghapus file lama

class PartnersController extends Controller
{
    // READ: Menampilkan halaman utama partner
    public function index(Request $request)
    {
        $search = $request->get('search');
        $partners = Partner::when($search, function($query, $search) {
            return $query->where('name', 'like', "%{$search}%");
        })->latest()->get();

        return view('admin.partners.index', compact('partners'));
    }

    // CREATE: Menyimpan data partner baru beserta logonya
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:partners,name',
            'logo' => 'required|image|mimes:jpeg,png,jpg,svg|max:2048', // Maksimal 2MB
        ]);

        // Proses upload file gambar
        $logoPath = null;
        if ($request->hasFile('logo')) {
            // Menyimpan ke dalam folder 'public/partners'
            $logoPath = $request->file('logo')->store('partners', 'public');
        }

        Partner::create([
            'name' => $request->name,
            'logo_url' => $logoPath
        ]);

        return redirect()->route('admin.partners.index')->with('success', 'Partner berhasil ditambahkan!');
    }

    // UPDATE: Memperbarui data partner dan mengganti logo jika ada logo baru
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:partners,name,' . $id,
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
        ]);

        $partner = Partner::findOrFail($id);
        $logoPath = $partner->logo_url;

        // Jika user mengunggah logo baru
        if ($request->hasFile('logo')) {
            // Hapus logo lama dari storage jika ada
            if ($partner->logo_url && Storage::disk('public')->exists($partner->logo_url)) {
                Storage::disk('public')->delete($partner->logo_url);
            }
            // Simpan logo baru
            $logoPath = $request->file('logo')->store('partners', 'public');
        }

        $partner->update([
            'name' => $request->name,
            'logo_url' => $logoPath
        ]);

        return redirect()->route('admin.partners.index')->with('success', 'Partner berhasil diperbarui!');
    }

    // DELETE: Menghapus data partner sekaligus file logonya dari storage
    public function destroy($id)
    {
        $partner = Partner::findOrFail($id);

        // Hapus file gambar dari storage sebelum menghapus record di database
        if ($partner->logo_url && Storage::disk('public')->exists($partner->logo_url)) {
            Storage::disk('public')->delete($partner->logo_url);
        }

        $partner->delete();

        return redirect()->route('admin.partners.index')->with('success', 'Partner berhasil dihapus!');
    }
}