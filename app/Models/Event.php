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
     * Relasi ke model Category
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * TAMBAHKAN INI: Relasi ke model Partner
     */
    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }

    public function reviews()
{
    return $this->hasMany(Review::class);
}

// Fitur tambahan: Menghitung rata-rata rating bintang untuk event ini
public function averageRating()
{
    return $this->reviews()->avg('rating') ?? 0;
}
    

public function tenant()
{
    return $this->belongsTo(Tenant::class);
}

}