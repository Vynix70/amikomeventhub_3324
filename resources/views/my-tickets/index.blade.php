@extends('layouts.app')

@section('title', 'Riwayat Tiket Saya')

@section('content')
<main class="max-w-5xl mx-auto px-6 py-12">
    <h1 class="text-3xl font-extrabold text-slate-900 mb-2">Riwayat Tiket Saya</h1>
    <p class="text-slate-500 mb-8">Daftar semua tiket event yang pernah kamu daftar atau beli.</p>

    @if($tickets->isEmpty())
        <div class="bg-white rounded-3xl border border-slate-200 p-12 text-center shadow-sm">
            <div class="w-16 h-16 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center mx-auto mb-4 text-2xl font-bold">
                🎟️
            </div>
            <h3 class="text-lg font-bold text-slate-800">Belum Ada Tiket</h3>
            <p class="text-slate-500 text-sm mt-1 mb-6">Kamu belum pernah mendaftar atau membeli tiket event apa pun.</p>
            <a href="/" class="px-6 py-3 bg-indigo-600 text-white font-bold rounded-xl shadow-md hover:bg-indigo-700 transition text-sm">
                Jelajahi Event
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @foreach($tickets as $ticket)
                <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm flex flex-col justify-between">
                    <div>
                        {{-- Status Pembayaran / Pendaftaran --}}
                        <div class="flex justify-between items-center mb-4">
                            <span class="text-xs font-bold uppercase tracking-wider px-3 py-1 rounded-full 
                                {{ $ticket->status === 'success' || $ticket->status === 'paid' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                                {{ strtoupper($ticket->status ?? 'SUCCESS') }}
                            </span>
                            <span class="text-xs text-slate-400">
                                {{ $ticket->created_at->format('d M Y, H:i') }}
                            </span>
                        </div>

                        {{-- Info Event --}}
                        <div class="flex gap-4 items-center">
                            <img src="{{ $ticket->event->poster_path ? asset('storage/' . $ticket->event->poster_path) : 'https://placehold.co/100x100' }}" 
                                 alt="Poster" class="w-20 h-20 rounded-2xl object-cover border border-slate-100">
                            <div>
                                <h3 class="font-extrabold text-slate-900 line-clamp-1">{{ $ticket->event->title }}</h3>
                                <p class="text-xs text-slate-500 mt-1">
                                    📅 {{ \Carbon\Carbon::parse($ticket->event->date)->translatedFormat('d M Y') }}
                                </p>
                                <p class="text-xs text-slate-500">
                                    📍 {{ $ticket->event->location }}
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Footer Tiket & Harga --}}
                    <div class="mt-6 pt-4 border-t border-slate-100 flex justify-between items-center">
                        <div>
                            <p class="text-[10px] text-slate-400 font-bold uppercase">Total Bayar</p>
                            <p class="text-sm font-extrabold text-indigo-600">
                                @if($ticket->total_price == 0)
                                    <span class="text-emerald-600">Gratis</span>
                                @else
                                    Rp {{ number_format($ticket->total_price, 0, ',', '.') }}
                                @endif
                            </p>
                        </div>
                        
                        {{-- Tombol Detail / E-Ticket --}}
                        <a href="#" class="px-4 py-2 bg-slate-900 text-white font-bold text-xs rounded-xl hover:bg-slate-800 transition">
                            E-Ticket
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</main>
@endsection