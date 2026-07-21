@extends('layouts.app')

@section('content')
<main class="max-w-7xl mx-auto px-6 py-12">
    <!-- STRUKTUR UTAMA DETAIL ACARA -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
        
        <!-- ================= SEKTOR KIRI: POSTER ACARA ================= -->
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

        <!-- ================= SEKTOR KANAN: DETAIL INFO ACARA ================= -->
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

            <!-- ================= CARD DINAMIS PENJUALAN TIKET (TIER) ================= -->
            @php
                $isEventFinished = \Carbon\Carbon::now()->startOfDay()->gt(\Carbon\Carbon::parse($event->date)->startOfDay());
                $currentTier = $event->currentTier();
            @endphp

            <div class="p-6 md:p-8 bg-white rounded-3xl border border-slate-100 shadow-xl space-y-6">
                @if($isEventFinished)
                    <div class="p-4 bg-amber-50 border border-amber-200 text-amber-800 rounded-2xl text-center font-bold text-sm">
                        ❌ Acara telah berlangsung pada {{ \Carbon\Carbon::parse($event->date)->translatedFormat('d M Y') }}. Penjualan tiket ditutup.
                    </div>
                @elseif($currentTier)
                    <div>
                        <span class="inline-block px-3 py-1 bg-emerald-100 text-emerald-700 font-bold text-xs rounded-full uppercase">
                            Tier Aktif: {{ $currentTier->name }}
                        </span>
                        <h3 class="text-4xl font-black text-slate-900 mt-2">
                            Rp {{ number_format($currentTier->price, 0, ',', '.') }}
                        </h3>
                        <p class="text-xs text-slate-400 mt-1">
                            Berakhir pada: {{ $currentTier->end_date ? $currentTier->end_date->format('d M Y, H:i') : '-' }} | Sisa Kuota: {{ $currentTier->quota }}
                        </p>
                    </div>

                    @if($currentTier->quota > 0)
                        @auth
                            <!-- Form Pemesanan / Checkout dengan Parameter $event -->
                            <form action="{{ route('checkout.store', $event) }}" method="POST" class="space-y-4">
                                @csrf
                                <input type="hidden" name="event_id" value="{{ $event->id }}">
                                <input type="hidden" name="ticket_tier_id" value="{{ $currentTier->id }}">
                                
                                <div>
                                    <label class="block text-xs font-bold text-slate-600 mb-1">Jumlah Tiket</label>
                                    <input type="number" name="quantity" value="1" min="1" max="{{ min(5, $currentTier->quota) }}" class="w-full px-4 py-2 border border-slate-200 rounded-xl font-semibold text-slate-800 focus:ring-2 focus:ring-indigo-500 outline-none">
                                </div>

                                <!-- FIELD KODE VOUCHER -->
                                <div>
                                    <label class="block text-xs font-bold text-slate-600 mb-1">Kode Voucher (Opsional)</label>
                                    <input type="text" name="voucher_code" placeholder="Masukkan kode diskon" class="w-full px-4 py-2 border border-slate-200 rounded-xl font-semibold text-slate-800 uppercase focus:ring-2 focus:ring-indigo-500 outline-none placeholder:normal-case placeholder:font-normal placeholder:text-slate-400">
                                </div>

                                <button type="submit" class="w-full py-4 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-lg rounded-2xl shadow-lg shadow-indigo-100 hover:shadow-indigo-200 transition">
                                    Beli Tiket Sekarang
                                </button>
                            </form>
                        @else
                            <a href="{{ route('auth.google', ['event_id' => $event->id]) }}"
                               class="flex items-center justify-center gap-3 w-full py-4 bg-slate-900 hover:bg-slate-800 text-white rounded-2xl font-bold text-base transition shadow-md">
                                <img src="https://developers.google.com/static/identity/images/g-logo.png" alt="Google" class="w-5 h-5">
                                Login Google untuk Beli
                            </a>
                        @endauth
                    @else
                        <div class="p-4 bg-rose-50 border border-rose-100 text-rose-600 rounded-2xl text-center font-bold text-sm">
                            ⚠️ Kuota untuk tier {{ $currentTier->name }} telah habis terjual.
                        </div>
                    @endif
                @else
                    <div class="p-4 bg-amber-50 border border-amber-100 text-amber-800 rounded-2xl text-center text-sm font-bold">
                        🚫 Saat ini tidak ada penjualan tiket yang aktif untuk event ini.
                    </div>
                @endif

                <!-- Daftar Seluruh Tahapan Tier (Jadwal & Transparansi Harga) -->
                @if($event->ticketTiers && $event->ticketTiers->count() > 0)
                    <div class="mt-6 border-t border-slate-100 pt-4">
                        <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Jadwal Harga Tiket</h4>
                        <div class="space-y-2">
                            @foreach($event->ticketTiers as $tier)
                                <div class="flex justify-between items-center text-xs p-3 rounded-xl transition {{ $currentTier && $currentTier->id == $tier->id ? 'bg-indigo-50 border border-indigo-200 ring-1 ring-indigo-200' : 'bg-slate-50 border border-slate-100' }}">
                                    <div>
                                        <p class="font-bold text-slate-800">{{ $tier->name }}</p>
                                        <p class="text-[10px] text-slate-400">
                                            {{ $tier->start_date ? $tier->start_date->format('d/m/Y') : 'Segera' }} - {{ $tier->end_date ? $tier->end_date->format('d/m/Y') : 'Selesai' }}
                                        </p>
                                    </div>
                                    <span class="font-black text-slate-900 text-sm">Rp {{ number_format($tier->price, 0, ',', '.') }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- KEBIJAKAN TIKET -->
            <div class="space-y-4">
                <h3 class="text-xl font-bold">Kebijakan Tiket</h3>
                <ul class="space-y-3 text-slate-500">
                    <li class="flex items-start gap-2">
                        <svg class="w-5 h-5 text-green-500 mt-1 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        E-Ticket akan dikirimkan otomatis setelah pembayaran berhasil.
                    </li>
                    <li class="flex items-start gap-2">
                        <svg class="w-5 h-5 text-green-500 mt-1 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Tiket dapat discan di pintu masuk (Check-in).
                    </li>
                    <li class="flex items-start gap-2 text-rose-500">
                        <svg class="w-5 h-5 text-rose-500 mt-1 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
            
            <!-- KIRI: DAFTAR RATINGS & REVIEWS -->
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

            <!-- KANAN: FORM INPUT REVIEW -->
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