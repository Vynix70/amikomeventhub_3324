<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'title',
        'description',
        'date',
        'location',
        'price',
        'stock',
        'category_id',
        'partner_id',
        'poster_path',
    ];

    /**
     * Casting tipe data kolom
     * Otomatis mengubah string tanggal menjadi objek Carbon/Datetime
     */
    protected $casts = [
        'date' => 'datetime',
    ];

    /* ==========================================
     | RELASI TIKET & TIER (DARI KODE BARU)
     | ========================================== */

    /**
     * Relasi ke model TicketTier (Dynamic Pricing)
     */
    public function ticketTiers()
    {
        return $this->hasMany(TicketTier::class);
    }

    /**
     * Helper untuk mengambil tier yang AKTIF saat ini secara otomatis
     */
    public function currentTier()
    {
        $now = now();
        return $this->ticketTiers()
            ->where('start_date', '<=', $now)
            ->where('end_date', '>=', $now)
            ->where('quota', '>', 0)
            ->first();
    }

    /* ==========================================
     | RELASI & HELPER (DARI KODE LAMA)
     | ========================================== */

    /**
     * Relasi ke model Category
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Relasi ke model Partner
     */
    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }

    /**
     * Relasi ke model Tenant
     */
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Relasi ke model Review
     */
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Fitur tambahan: Menghitung rata-rata rating bintang untuk event ini
     */
    public function averageRating()
    {
        return $this->reviews()->avg('rating') ?? 0;
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id'); // ganti 'user_id' jika nama kolom foreign key kamu berbeda
    }
}