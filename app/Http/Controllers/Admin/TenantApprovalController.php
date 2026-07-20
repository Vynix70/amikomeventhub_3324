<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\Request;

class TenantApprovalController extends Controller
{
    // 1. Menampilkan seluruh penyelenggara/tenant yang butuh pengawasan
    public function index()
    {
        // Mengambil semua tenant, diurutkan dari yang paling baru mendaftar
        $tenants = Tenant::latest()->paginate(10);
        
        return view('admin.tenants.index', compact('tenants'));
    }

    // 2. Mengubah status kelayakan tenant (Verify atau Reject)
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:verified,rejected,pending'
        ]);

        $tenant = Tenant::findOrFail($id);
        $tenant->update([
            'status' => $request->status
        ]);

        // Berikan pesan sukses dinamis berdasarkan status yang dipilih
        $message = "Status penyelenggara {$tenant->name} berhasil diubah menjadi " . ucfirst($request->status) . ".";
        
        return redirect()->back()->with('success', $message);
    }
}