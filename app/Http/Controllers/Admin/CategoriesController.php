<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoriesController extends Controller
{
    // READ: Menampilkan semua data kategori
    public function index(Request $request)
    {
        $search = $request->get('search');
        
        // Fitur Tambahan: Pencarian jika user mengetik sesuatu
        $categories = Category::when($search, function($query, $search) {
            return $query->where('name', 'like', "%{$search}%");
        })->latest()->get();

        return view('admin.categories.index', compact('categories'));
    }

    // CREATE: Menyimpan kategori baru ke database
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
        ]);

        Category::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
        ]);

        return redirect()->route('admin.categories.index')->with('success', 'Kategori berhasil ditambahkan!');
    }

    // UPDATE: Memperbarui nama kategori
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $id,
        ]);

        $category = Category::findOrFail($id);
        $category->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
        ]);

        return redirect()->route('admin.categories.index')->with('success', 'Kategori berhasil diperbarui!');
    }

    // DELETE: Menghapus kategori
    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        
        // Cek jika kategori memiliki relasi event (opsional/keamanan data)
        if ($category->events()->count() > 0) {
            return redirect()->route('admin.categories.index')->with('error', 'Kategori tidak bisa dihapus karena terikat dengan event.');
        }

        $category->delete();

        return redirect()->route('admin.categories.index')->with('success', 'Kategori berhasil dihapus!');
    }
}