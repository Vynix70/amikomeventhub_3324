<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Category;
use App\Models\TicketTier;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class EventController extends Controller
{
    public function index() 
    {
        // Memuat relasi 'category', 'user' (tenant/pembuat event), dan 'ticketTiers' secara bersamaan
        $events = Event::with(['category', 'user', 'ticketTiers'])->latest()->get();

        return view('admin.events.index', compact('events'));
    }

    public function create() 
    {
        $categories = Category::all();
        return view('admin.events.create', compact('categories'));
    }

    public function store(Request $request) 
    {
        // 1. Validasi Input Form Utama & Array Tiers
        $request->validate([
            'category_id'        => 'required|exists:categories,id',
            'title'              => 'required|string|max:255',
            'description'        => 'required|string',
            'date'               => 'required|date',
            'location'           => 'required|string',
            'price'              => 'nullable|numeric|min:0',
            'stock'              => 'nullable|numeric|min:0',
            'poster_path'        => 'required|image|mimes:jpg,png,jpeg|max:2048',

            // Validasi Dynamic Tier Tiket
            'tiers'              => 'required|array|min:1',
            'tiers.*.name'       => 'required|string|max:255',
            'tiers.*.price'      => 'required|numeric|min:0',
            'tiers.*.quota'      => 'required|integer|min:1',
            'tiers.*.start_date' => 'required|date',
            'tiers.*.end_date'   => 'required|date|after_or_equal:tiers.*.start_date',
        ]);

        // 2. Eksekusi Simpan dengan Database Transaction
        DB::transaction(function () use ($request) {
            // Upload Poster jika ada
            $posterPath = null;
            if ($request->hasFile('poster_path')) {
                $posterPath = $request->file('poster_path')->store('posters', 'public');
            }

            // Hitung nilai fallback untuk price & stock dari tier jika input manual kosong
            $fallbackPrice = $request->price ?? $request->tiers[0]['price'];
            $fallbackStock = $request->stock ?? array_sum(array_column($request->tiers, 'quota'));

            // A. Simpan Event Utama
            $event = Event::create([
                'user_id'     => auth()->id(), // Menghubungkan event ke user/admin yang membuat jika ada kolom user_id
                'category_id' => $request->category_id,
                'title'       => $request->title,
                'description' => $request->description,
                'date'        => $request->date,
                'location'    => $request->location,
                'price'       => $fallbackPrice,
                'stock'       => $fallbackStock,
                'poster_path' => $posterPath,
            ]);

            // B. Simpan Seluruh Tier Tiket ke Relasi ticketTiers
            foreach ($request->tiers as $tier) {
                $event->ticketTiers()->create([
                    'name'       => $tier['name'],
                    'price'      => $tier['price'],
                    'quota'      => $tier['quota'],
                    'start_date' => $tier['start_date'],
                    'end_date'   => $tier['end_date'],
                ]);
            }
        });

        return redirect()->route('admin.events.index')
            ->with('success', 'Event & Tier Tiket berhasil dibuat!');
    }

    public function edit(Event $event) 
    {
        $categories = Category::all();
        $event->load(['ticketTiers', 'user']); // Meload data tier dan user terkait
        
        return view('admin.events.edit', compact('event', 'categories'));
    }

    public function update(Request $request, Event $event) 
    {
        $data = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'title'       => 'required|string|max:255',
            'description' => 'required',
            'date'        => 'required|date',
            'location'    => 'required',
            'price'       => 'nullable|numeric',
            'stock'       => 'nullable|numeric',
            'poster_path' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
        ]);

        if ($request->hasFile('poster_path')) {
            if ($event->poster_path) {
                Storage::disk('public')->delete($event->poster_path);
            }
            $data['poster_path'] = $request->file('poster_path')->store('posters', 'public');
        }

        $event->update($data);

        return redirect()->route('admin.events.index')
            ->with('success', 'Event berhasil diperbarui.');
    }

    public function destroy(Event $event) 
    {
        if ($event->poster_path) {
            Storage::disk('public')->delete($event->poster_path);
        }
        
        $event->delete();

        return redirect()->route('admin.events.index')
            ->with('success', 'Event berhasil dihapus.');
    }
}