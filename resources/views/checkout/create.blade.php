@extends('layouts.app')
@section('title', 'Checkout - ' . $event->title)
@section('content')
<main class="max-w-3xl mx-auto px-6 py-20">
    <div class="mb-12">
        <a href="{{ route('events.show', $event->id) }}" class="text-indigo-600 font-bold flex items-center gap-2 mb-6">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Kembali ke Event
        </a>
        <h1 class="text-4xl font-extrabold">Checkout</h1>
        <p class="text-slate-500 mt-2">Lengkapi data Anda untuk mendapatkan tiket.</p>
    </div>

    {{-- Alert Error Berupa Session --}}
    @if(session('error'))
    <div class="mb-6 p-4 bg-red-100 text-red-700 rounded-xl font-bold">
        {{ session('error') }}
    </div>
    @endif

    {{-- Alert Error Validasi Input Form (Nama, Email, HP) --}}
    @if ($errors->any())
    <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-600 rounded-xl text-sm font-semibold">
        <ul class="list-disc pl-5">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="grid grid-cols-1 gap-8">
        {{-- Ringkasan Pesanan & Pembayaran --}}
        <div class="bg-white rounded-3xl border border-slate-200 p-8 shadow-sm">
            <h3 class="text-xl font-bold mb-6 border-b pb-4">Pesanan Anda</h3>
            <div class="flex gap-6 items-start">
                <img src="{{ ($event->poster_path && Storage::disk('public')->exists($event->poster_path))
                 ? asset('storage/' . $event->poster_path)
                 : 'https://placehold.co/200x200' }}"
                    alt="Event" class="w-24 h-24 rounded-2xl object-cover">
                <div>
                    <h4 class="font-extrabold text-lg">{{ $event->title }}</h4>
                    <p class="text-slate-500">
                        {{ \Carbon\Carbon::parse($event->date)->translatedFormat('d M Y') }} • {{ $event->location }}
                    </p>
                    <p class="text-indigo-600 font-bold mt-2">1 x {{ $event->price == 0 ? 'Gratis' : 'Rp ' . number_format($event->price, 0, ',', '.') }}</p>
                </div>
            </div>
            
            {{-- LOGIKA RINCIAN BIAYA DINAMIS --}}
            <div class="mt-8 pt-6 border-t space-y-3">
                <div class="flex justify-between text-slate-500">
                    <span>Harga Tiket</span>
                    <span>{{ $event->price == 0 ? 'Gratis' : 'Rp ' . number_format($event->price, 0, ',', '.') }}</span>
                </div>
                
                <div class="flex justify-between text-slate-500">
                    <span>Biaya Layanan</span>
                    @if($event->price == 0)
                        <span class="text-green-600 font-bold uppercase text-xs">Bebas Biaya</span>
                    @else
                        <span>Rp 5.000</span>
                    @endif
                </div>

                {{-- Potongan Diskon (Sembunyikan bawaan menggunakan kelas Tailwind 'hidden') --}}
                <div class="flex justify-between text-red-600 font-medium hidden" id="row_discount">
                    <span>Potongan Diskon</span>
                    <span id="text_discount">- Rp 0</span>
                </div>
                
                <div class="flex justify-between text-2xl font-black mt-4 pt-4 border-t">
                    <span>Total Bayar</span>
                    <span class="text-indigo-600" id="text_total_price">
                        @if($event->price == 0)
                            Rp 0
                        @else
                            Rp {{ number_format($event->price + 5000, 0, ',', '.') }}
                        @endif
                    </span>
                </div>
            </div>
        </div>

        {{-- Kolom Input Voucher (Hanya tampil jika Event Tidak Gratis) --}}
        @if($event->price > 0)
        <div class="bg-white rounded-3xl border border-slate-200 p-8 shadow-sm">
            <h3 class="text-xl font-bold mb-4 text-slate-800">Punya Kode Voucher?</h3>
            <div class="flex gap-3">
                <input type="text" id="voucher_input" 
                    class="flex-1 px-5 py-4 border-2 border-slate-100 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-600 outline-none transition font-medium" 
                    placeholder="Contoh: MAHASISWA50">
                <button type="button" id="btn_apply_voucher" 
                    class="px-6 py-4 bg-slate-900 text-white font-bold rounded-2xl hover:bg-slate-800 active:scale-95 transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                    Terapkan
                </button>
            </div>
            <!-- Pesan Status Validasi (Sukses/Gagal) menggunakan kelas Tailwind 'hidden' -->
            <div id="voucher_message" class="text-sm font-semibold mt-3 hidden"></div>
        </div>
        @endif

        {{-- Form Data Pemesan --}}
        <div class="bg-white rounded-3xl border border-slate-200 p-8 shadow-sm">
            <h3 class="text-xl font-bold mb-6 italic text-indigo-600 underline underline-offset-8">📦 Data Pemesan</h3>
            <form action="{{ route('checkout.store', $event->id) }}" method="POST" class="space-y-6">
                @csrf
                
                <!-- Input Tersembunyi Sesuai Request Baru Anda -->
                <input type="hidden" name="voucher_code" id="hidden_voucher_code">

                <!-- Input Nama Pelanggan (Otomatis & Readonly) -->
                <div>
                    <label for="customer_name" class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide">Nama Lengkap</label>
                    <input type="text" name="customer_name" id="customer_name" 
                        class="w-full px-5 py-4 bg-slate-50 border-2 border-slate-100 rounded-2xl focus:outline-none transition font-medium text-slate-500 cursor-not-allowed"
                        value="{{ auth()->user()->name }}" readonly required>
                    <p class="text-[10px] text-indigo-500 mt-2 font-bold uppercase tracking-tighter">*Otomatis terisi dari akun Google Anda</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Input Email Pelanggan (Otomatis & Readonly) -->
                    <div>
                        <label for="customer_email" class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide">Email Aktif</label>
                        <input type="email" name="customer_email" id="customer_email" 
                            class="w-full px-5 py-4 bg-slate-50 border-2 border-slate-100 rounded-2xl focus:outline-none transition font-medium text-slate-500 cursor-not-allowed"
                            value="{{ auth()->user()->email }}" readonly required>
                        <p class="text-[10px] text-slate-400 mt-2 font-bold uppercase tracking-tighter">*E-Ticket akan dikirim ke email ini</p>
                    </div>
                    
                    <!-- Input Nomor Telepon (Manual) -->
                    <div>
                        <label for="customer_phone" class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide">No. WhatsApp</label>
                        <input type="tel" name="customer_phone" id="customer_phone" placeholder="Contoh: 08123456789"
                            class="w-full px-5 py-4 bg-white border-2 border-slate-100 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-600 outline-none transition font-medium"
                            required value="{{ old('customer_phone') }}">
                    </div>
                </div>

                {{-- TOMBOL SUBMIT DINAMIS --}}
                <button type="submit" id="btn_submit_checkout"
                    class="w-full py-5 bg-indigo-600 text-white rounded-2xl font-black text-xl shadow-xl shadow-indigo-200 hover:bg-indigo-700 active:scale-95 transition-all">
                    {{ $event->price == 0 ? 'Daftar Event Gratis Sekarang' : 'Lanjut Pembayaran' }}
                </button>
                <p class="text-center text-xs text-slate-400">Dengan menekan tombol di atas, Anda menyetujui Syarat & Ketentuan kami.</p>
            </form>
        </div>
    </div>
</main>

{{-- SCRIPT JAVASCRIPT DINAMIS --}}
@if($event->price > 0)
<script>
document.getElementById('btn_apply_voucher').addEventListener('click', function() {
    const voucherCode = document.getElementById('voucher_input').value.trim();
    const msgElement = document.getElementById('voucher_message');
    
    if (!voucherCode) {
        msgElement.className = "text-sm font-semibold mt-3 text-red-600";
        msgElement.innerText = "Silakan ketik kode kupon terlebih dahulu!";
        msgElement.classList.remove('hidden');
        return;
    }

    const eventId = "{{ $event->id }}";

    // Request AJAX Fetch ke URL statis
    fetch("/checkout/apply-voucher", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": "{{ csrf_token() }}"
        },
        body: JSON.stringify({
            code: voucherCode,
            event_id: eventId
        })
    })
    .then(response => response.json())
    .then(data => {
        msgElement.classList.remove('hidden');
        
        if (data.success) {
            // Jika Voucher Valid
            msgElement.className = "text-sm font-semibold mt-3 text-green-600";
            msgElement.innerText = data.message;
            
            // ✅ Menggunakan langsung variabel voucherCode yang sudah di-trim di atas
            document.getElementById('hidden_voucher_code').value = voucherCode;
            
            // Tampilkan baris potongan harga & ubah nominal harga total secara dinamis
            document.getElementById('row_discount').classList.remove('hidden');
            document.getElementById('text_discount').innerText = "- Rp " + new Intl.NumberFormat('id-ID').format(data.discount);
            document.getElementById('text_total_price').innerText = "Rp " + new Intl.NumberFormat('id-ID').format(data.final_price);
            
            // Kunci field input & tombol voucher agar tidak dimanipulasi lagi
            document.getElementById('voucher_input').setAttribute('disabled', 'true');
            document.getElementById('voucher_input').classList.add('bg-slate-50', 'text-slate-400', 'cursor-not-allowed');
            document.getElementById('btn_apply_voucher').setAttribute('disabled', 'true');
        } else {
            // Jika Voucher Gagal / Salah / Kedaluwarsa
            msgElement.className = "text-sm font-semibold mt-3 text-red-600";
            msgElement.innerText = data.message;
        }
    })
    .catch(error => {
        console.error("Error:", error);
        msgElement.className = "text-sm font-semibold mt-3 text-red-600";
        msgElement.innerText = "Terjadi kesalahan sistem, silakan coba lagi.";
        msgElement.classList.remove('hidden');
    });
});
</script>
@endif
@endsection