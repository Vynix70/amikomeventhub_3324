<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
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
}