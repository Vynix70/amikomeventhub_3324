<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    /**
     * Kolom-kolom yang dapat diisi secara massal (Mass Assignment).
     */
    protected $fillable = [
        'user_id',          // ID Akun pengguna yang login (opsional/nullable)
        'event_id',         // Foreign key ke tabel events
        'ticket_tier_id',   // Foreign key ke tabel ticket_tiers
        'order_id',         // Kode/Nomor Unik Transaksi (contoh: TRX-169...)
        'customer_name',    // Nama lengkap pemesan tiket
        'customer_email',   // Alamat email penerima E-Ticket
        'customer_phone',   // Nomor telepon pemesan tiket
        'quantity',         // Jumlah tiket yang dibeli
        'total_price',      // Total nominal pembayaran (disesuaikan dari total_amount)
        'status',           // Status transaksi (Pending, success, cancel, dsb)
        'snap_token',       // Token pembayaran Midtrans Snap
    ];

    /**
     * Relasi ke Model User (Pemilik Akun)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke Model Event
     */
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Relasi ke Model TicketTier
     * Menggunakan foreign key 'ticket_tier_id'
     */
    public function ticketTier()
    {
        return $this->belongsTo(TicketTier::class, 'ticket_tier_id');
    }
}