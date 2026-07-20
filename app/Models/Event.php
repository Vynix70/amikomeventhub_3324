<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id', // Pastikan kolom foreign key ini ada di tabel events Anda
        'title',
        'description',
        'date',
        'location',
        'price',
        'stock',
        'category_id',
        'partner_id', // Pastikan kolom foreign key ini ada di tabel events Anda
        'poster_path',
    ];

    /**
     * Casting tipe data kolom (DIADOPSI DARI KODE BARU)
     * Otomatis mengubah string tanggal menjadi objek Carbon/Datetime
     */
    protected $casts = [
        'date' => 'datetime',
    ];

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

    /**
     * Relasi ke model Tenant
     */
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}