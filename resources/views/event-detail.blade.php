@extends('layouts.app')

@section('content')
<main class="max-w-7xl mx-auto px-6 py-12">
    <!-- STRUKTUR UTAMA DETAIL ACARA -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
        
        <!-- ================= SEKTOR KIRI: POSTER ACARA (TERUPDATE) ================= -->
        <div class="lg:col-span-1">
            <div class="sticky top-32">
                <img src="{{ 
                        $event->poster_path 
                        ? (file_exists(public_path('assets/images/' . $event->poster_path)) 
                            ? asset('assets/images/' . $event->poster_path) 
                            : (Storage::disk('public')->exists($event->poster_path) 
                                ? asset('storage/' . $event->poster_path) 
                                : 'https://placehold.co/600x800?text=No+Poster'))
                        : 'https://placehold.co/600x800?text=No+Poster' 
                     }}" 
                     alt="{{ $event->title }}"
                     class="w-full rounded-[2.5rem] shadow-2xl border-8 border-white object-cover aspect-[3/4]">
                
                <div class="mt-8 p-6 bg-white rounded-3xl border border-slate-100 shadow-sm">
                    <h4 class="font-bold mb-4">Penyelenggara</h4>
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-indigo-100 rounded-full flex items-center justify-center text-indigo-600 font-black">
                            {{ substr($event->partner->name ?? 'AH', 0, 2) }}
                        </div>
                        <div>
                            <p class="font-bold text-slate-800">{{ $event->partner->name ?? 'AmikomEventHub' }}</p>
                            <p class="text-xs text-slate-500">Verified Organizer</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- SEKTOR KANAN: DETAIL INFO ACARA -->
        <div class="lg:col-span-2 space-y-12">
            <div class="space-y-4">
                <span class="px-4 py-1.5 bg-indigo-100 text-indigo-700 rounded-full text-sm font-bold uppercase tracking-wider">
                    {{ $event->category->name ?? 'Event' }}
                </span>
                
                <h1 class="text-4xl md:text-5xl font-black leading-tight text-slate-900">
                    {{ $event->title }}
                </h1>
                
                <div class="flex flex-wrap gap-6 text-slate-500 font-medium">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                            </path>
                        </svg>
                        <span>{{ \Carbon\Carbon::parse($event->date)->translatedFormat('l, d M Y') }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                            </path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <span>{{ $event->location }}</span>
                    </div>
                </div>
            </div>

            <div class="prose prose-slate max-w-none">
                <h3 class="text-2xl font-bold mb-4">Deskripsi Event</h3>
                <div class="text-lg text-slate-600 leading-relaxed space-y-4">
                    {!! nl2br(e($event->description)) !!}
                </div>
            </div>

            <!-- CARD STRUKTUR HARGA / PEMBELIAN TIKET -->
            @php
                $isEventFinished = \Carbon\Carbon::now()->startOfDay()->gt(\Carbon\Carbon::parse($event->date)->startOfDay());
            @endphp
            
            <div class="bg-indigo-600 rounded-[2.5rem] p-8 md:p-12 text-white shadow-2xl shadow-indigo-200 relative overflow-hidden">
                <div class="relative z-10 flex flex-col md:flex-row justify-between items-center gap-8">
                    <div>
                        <p class="text-indigo-200 font-bold uppercase tracking-widest text-sm mb-2">Harga Tiket</p>
                        <h2 class="text-5xl font-black">
                            Rp {{ number_format($event->price, 0, ',', '.') }} 
                            <span class="text-lg font-medium text-indigo-200">/ orang</span>
                        </h2>
                        
                        <p class="mt-4 text-indigo-100 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            @if($isEventFinished)
                                <span class="font-bold uppercase text-amber-300">Event Telah Selesai</span>
                            @elseif($event->stock > 0)
                                Sisa stok: <span class="font-bold underline">{{ $event->stock }} Tiket lagi!</span>
                            @else
                                <span class="font-bold uppercase text-rose-300">Tiket Habis Terjual!</span>
                            @endif
                        </p>
                    </div>
                    
                    <div class="w-full md:w-auto text-center md:text-right">
                        @if($isEventFinished)
                            <!-- KONDISI BARU: EVENT SUDAH SELESAI -->
                            <button disabled class="w-full md:w-auto inline-block px-8 py-5 bg-slate-500 text-slate-300 rounded-2xl font-bold text-lg cursor-not-allowed shadow-inner opacity-75">
                                ❌ Penjualan Tiket Ditutup
                            </button>
                        @elseif($event->stock > 0)
                            <!-- KONDISI: BELUM SELESAI & STOK ADA -->
                            @auth
                                <a href="{{ route('checkout.create', $event->id) }}"
                                    class="inline-block px-10 py-5 bg-white text-indigo-600 rounded-2xl font-black text-xl hover:scale-105 transition-transform shadow-xl">
                                    Pesan Sekarang
                                </a>
                            @else
                                <a href="{{ route('auth.google', ['event_id' => $event->id]) }}"
                                    class="flex items-center justify-center gap-3 px-8 py-5 bg-white text-slate-800 rounded-2xl font-black text-lg hover:scale-105 transition-transform shadow-xl">
                                    <img src="https://developers.google.com/static/identity/images/g-logo.png" alt="Google" class="w-6 h-6">
                                    Login Google untuk Beli
                                </a>
                            @endauth
                        @else
                            <!-- KONDISI: TIKET HABIS -->
                            <button disabled class="inline-block px-10 py-5 bg-slate-400 text-white rounded-2xl font-black text-xl cursor-not-allowed shadow-inner">
                                Sold Out
                            </button>
                        @endif
                    </div>
                </div>
                
                @if($isEventFinished)
                    <div class="relative z-10 mt-6 pt-4 border-t border-indigo-500/50 text-indigo-100 text-xs text-center md:text-left">
                        *Acara ini telah berlangsung pada {{ \Carbon\Carbon::parse($event->date)->translatedFormat('d M Y') }}. Anda hanya dapat memberikan atau melihat ulasan di bawah ini.
                    </div>
                @endif
                
                <div class="absolute -right-20 -bottom-20 w-64 h-64 bg-white opacity-10 rounded-full"></div>
                <div class="absolute -left-10 -top-10 w-32 h-32 bg-indigo-400 opacity-20 rounded-full"></div>
            </div>

            <div class="space-y-4">
                <h3 class="text-xl font-bold">Kebijakan Tiket</h3>
                <ul class="space-y-3 text-slate-500">
                    <li class="flex items-start gap-2">
                        <svg class="w-5 h-5 text-green-500 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        E-Ticket akan dikirimkan otomatis setelah pembayaran berhasil.
                    </li>
                    <li class="flex items-start gap-2">
                        <svg class="w-5 h-5 text-green-500 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Tiket dapat discan di pintu masuk (Check-in).
                    </li>
                    <li class="flex items-start gap-2 text-rose-500">
                        <svg class="w-5 h-5 text-rose-500 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Tiket yang sudah dibeli tidak dapat direfund.
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- ==================== BAGIAN ULASAN DAN RATING ==================== -->
    <div class="mt-16 pt-12 border-t border-slate-200">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
            
            <!-- KIRI (lg:col-span-2): DAFTAR RATINGS & REVIEWS YANG SUDAH MASUK -->
            <div class="lg:col-span-2 space-y-6">
                <div>
                    <h3 class="text-2xl font-bold text-slate-900 mb-2">💬 Ulasan Peserta ({{ $event->reviews->count() }})</h3>
                    <div class="flex items-center gap-2 font-semibold text-slate-700">
                        <span>Rata-rata Rating:</span> 
                        <span class="text-amber-500 flex items-center gap-1">
                            ★ {{ number_format($event->averageRating(), 1) }} <span class="text-slate-400 font-normal text-sm">/ 5.0</span>
                        </span>
                    </div>
                </div>

                <div class="space-y-4">
                    @forelse($event->reviews as $review)
                        <div class="bg-white border border-slate-100 p-6 rounded-3xl shadow-sm space-y-3">
                            <div class="flex items-center gap-3">
                                <img src="{{ $review->user->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($review->user->name) }}" 
                                     class="rounded-full w-10 h-10 object-cover border border-slate-100" alt="User avatar">
                                <div>
                                    <h6 class="font-bold text-slate-800 leading-none mb-1">{{ $review->user->name }}</h6>
                                    <span class="text-xs text-slate-400">{{ $review->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                            
                            <!-- Bintang Penilaian -->
                            <div class="text-amber-500 text-sm tracking-wide">
                                {{ str_repeat('★', $review->rating) }}{{ str_repeat('☆', 5 - $review->rating) }}
                            </div>
                            
                            <p class="text-slate-600 leading-relaxed">{{ $review->comment }}</p>
                        </div>
                    @empty
                        <div class="text-center py-12 bg-slate-100/50 rounded-3xl border border-dashed border-slate-200">
                            <p class="text-slate-400 font-medium">Belum ada ulasan untuk acara ini.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- KANAN (lg:col-span-1): FORM INPUT REVIEW -->
            <div class="lg:col-span-1">
                <div class="bg-white border border-indigo-100 rounded-[2rem] p-6 shadow-xl shadow-indigo-50/50 sticky top-32">
                    <h5 class="text-xl font-bold text-indigo-900 mb-4">Berikan Penilaian Anda</h5>
                    
                    @auth
                        @php
                            $hasTicket = \App\Models\Transaction::where('event_id', $event->id)
                                            ->where('customer_email', auth()->user()->email)
                                            ->whereIn('status', ['success', 'settlement'])->exists();
                        @endphp

                        @if($isEventFinished && $hasTicket)
                            <form action="{{ route('review.store', $event->id) }}" method="POST" class="space-y-4">
                                @csrf
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-2">Rating Bintang</label>
                                    <select name="rating" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-slate-700 font-medium focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition" required>
                                        <option value="">-- Pilih Bintang --</option>
                                        <option value="5">⭐⭐⭐⭐⭐ (5 - Sangat Puas)</option>
                                        <option value="4">⭐⭐⭐⭐ (4 - Puas)</option>
                                        <option value="3">⭐⭐⭐ (3 - Cukup)</option>
                                        <option value="2">⭐⭐ (2 - Kurang Puas)</option>
                                        <option value="1">⭐ (1 - Buruk)</option>
                                    </select>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-2">Testimoni / Ulasan</label>
                                    <textarea name="comment" rows="4" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-4 text-slate-700 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition placeholder:text-slate-400" placeholder="Ceritakan pengalaman seru kamu di acara ini..." required></textarea>
                                </div>
                                
                                <button type="submit" class="w-full py-3.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-lg shadow-indigo-100 hover:shadow-indigo-200 transition">
                                    Kirim Ulasan
                                </button>
                            </form>
                        @elseif(!$hasTicket)
                            <div class="p-4 bg-amber-50 rounded-2xl border border-amber-100 text-amber-800 text-sm font-medium text-center">
                                Tombol ulasan terkunci. Hanya pembeli tiket resmi yang dapat memberikan review.
                            </div>
                        @else
                            <div class="p-4 bg-blue-50 rounded-2xl border border-blue-100 text-blue-800 text-sm font-medium text-center">
                                Acara belum selesai dilaksanakan. Ulasan dapat diisi mulai tanggal: <br>
                                <strong class="text-base text-blue-900 block mt-1">{{ \Carbon\Carbon::parse($event->date)->addDay()->translatedFormat('d M Y') }}</strong>
                            </div>
                        @endif
                    @else
                        <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200 text-slate-600 text-sm font-medium text-center">
                            Silakan <a href="{{ route('auth.google', ['event_id' => $event->id]) }}" class="text-indigo-600 font-bold underline hover:text-indigo-700">Login via Google</a> terlebih dahulu untuk memberikan penilaian.
                        </div>
                    @endauth
                </div>
            </div>

        </div>
    </div>
</main>
@endsection