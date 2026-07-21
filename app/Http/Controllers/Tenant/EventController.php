<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Category;
use App\Models\TicketTier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class EventController extends Controller
{
    /**
     * 1. Menampilkan daftar event milik HIMA/Tenant yang sedang login
     */
    public function index()
    {
        $tenantId = Auth::guard('tenant')->id();
        $events = Event::where('tenant_id', $tenantId)->with('ticketTiers')->get();
        
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
     * 3. Menyimpan data event baru + Dynamic Pricing Tier
     */
    public function store(Request $request)
    {
        // 1. Validasi Input Event & Array Tiers
        $request->validate([
            'title'       => 'required|string|max:255',
            'date'        => 'required|date',
            'time'        => 'required',
            'category_id' => 'required|exists:categories,id',
            'location'    => 'required|string|max:255',
            'description' => 'nullable|string',
            'poster_path' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',

            // Validasi Array Dynamic Pricing Tier
            'tiers'              => 'required|array|min:1',
            'tiers.*.name'       => 'required|string|max:255',
            'tiers.*.price'      => 'required|numeric|min:0',
            'tiers.*.quota'      => 'required|integer|min:1',
            'tiers.*.start_date' => 'required|date',
            'tiers.*.end_date'   => 'required|date|after_or_equal:tiers.*.start_date',
        ]);

        $fullDateTime = $request->date . ' ' . $request->time . ':00';

        DB::beginTransaction();
        try {
            // 2. Upload Poster
            $posterPath = null;
            if ($request->hasFile('poster_path')) {
                $posterPath = $request->file('poster_path')->store('posters', 'public');
            }

            // Hitung total stok dari gabungan seluruh kuota tier
            $totalStock = array_sum(array_column($request->tiers, 'quota'));
            $firstTierPrice = $request->tiers[0]['price'];

            // 3. Simpan Event Utama
            $event = Event::create([
                'tenant_id'   => Auth::guard('tenant')->id(),
                'title'       => $request->title,
                'description' => $request->description,
                'date'        => $fullDateTime,
                'category_id' => $request->category_id,
                'location'    => $request->location,
                'price'       => $firstTierPrice, // Harga fallback/summary tier pertama
                'stock'       => $totalStock,      // Total stok gabungan
                'poster_path' => $posterPath,
            ]);

            // 4. Simpan Setiap Tier Tiket ke Tabel ticket_tiers
            foreach ($request->tiers as $tier) {
                $event->ticketTiers()->create([
                    'name'       => $tier['name'],
                    'price'      => $tier['price'],
                    'quota'      => $tier['quota'],
                    'start_date' => $tier['start_date'],
                    'end_date'   => $tier['end_date'],
                ]);
            }

            DB::commit();
            return redirect()->route('tenant.events.index')->with('success', 'Event beserta skema Dynamic Pricing berhasil diterbitkan!');

        } catch (\Exception $e) {
            DB::rollBack();
            if (isset($posterPath) && Storage::disk('public')->exists($posterPath)) {
                Storage::disk('public')->delete($posterPath);
            }
            return back()->withInput()->with('error', 'Gagal menyimpan event: ' . $e->getMessage());
        }
    }

    /**
     * 4. Menampilkan form edit dengan proteksi akses
     */
    public function edit(Event $event)
    {
        if ($event->tenant_id !== Auth::guard('tenant')->id()) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengelola event ini.');
        }

        $event->load('ticketTiers');
        $categories = Category::all();
        
        return view('tenant.events.edit', compact('event', 'categories'));
    }

    /**
     * 5. Memperbarui data event & Dynamic Pricing Tier
     */
    public function update(Request $request, Event $event)
    {
        if ($event->tenant_id !== Auth::guard('tenant')->id()) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengubah event ini.');
        }

        $request->validate([
            'title'       => 'required|string|max:255',
            'date'        => 'required|date',
            'time'        => 'required',
            'category_id' => 'required|exists:categories,id',
            'location'    => 'required|string|max:255',
            'description' => 'nullable|string',
            'poster_path' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',

            // Validasi Array Dynamic Pricing Tier
            'tiers'              => 'required|array|min:1',
            'tiers.*.name'       => 'required|string|max:255',
            'tiers.*.price'      => 'required|numeric|min:0',
            'tiers.*.quota'      => 'required|integer|min:1',
            'tiers.*.start_date' => 'required|date',
            'tiers.*.end_date'   => 'required|date|after_or_equal:tiers.*.start_date',
        ]);

        $fullDateTime = $request->date . ' ' . $request->time . ':00';

        DB::beginTransaction();
        try {
            // Logika Update Poster
            $posterPath = $event->poster_path; 
            if ($request->hasFile('poster_path')) {
                if ($posterPath && Storage::disk('public')->exists($posterPath)) {
                    Storage::disk('public')->delete($posterPath);
                }
                $posterPath = $request->file('poster_path')->store('posters', 'public');
            }

            $totalStock = array_sum(array_column($request->tiers, 'quota'));
            $firstTierPrice = $request->tiers[0]['price'];

            // Update Event Utama
            $event->update([
                'title'       => $request->title,
                'description' => $request->description,
                'date'        => $fullDateTime,
                'category_id' => $request->category_id,
                'location'    => $request->location,
                'price'       => $firstTierPrice,
                'stock'       => $totalStock,
                'poster_path' => $posterPath,
            ]);

            // Replace Seluruh Tier Tiket (Hapus lama, simpan set baru)
            $event->ticketTiers()->delete();

            foreach ($request->tiers as $tier) {
                $event->ticketTiers()->create([
                    'name'       => $tier['name'],
                    'price'      => $tier['price'],
                    'quota'      => $tier['quota'],
                    'start_date' => $tier['start_date'],
                    'end_date'   => $tier['end_date'],
                ]);
            }

            DB::commit();
            return redirect()->route('tenant.events.index')->with('success', 'Detail event & tier harga berhasil diperbarui!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal memperbarui event: ' . $e->getMessage());
        }
    }

    /**
     * 6. Menghapus data event beserta berkas dan tier tiketnya
     */
    public function destroy(Event $event)
    {
        if ($event->tenant_id !== Auth::guard('tenant')->id()) {
            abort(403, 'Anda tidak memiliki hak akses untuk menghapus event ini.');
        }

        DB::beginTransaction();
        try {
            if ($event->poster_path && Storage::disk('public')->exists($event->poster_path)) {
                Storage::disk('public')->delete($event->poster_path);
            }

            $event->ticketTiers()->delete();
            $event->delete();

            DB::commit();
            return redirect()->route('tenant.events.index')->with('success', 'Event berhasil dihapus.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus event: ' . $e->getMessage());
        }
    }
}