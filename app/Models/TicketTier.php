<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketTier extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'name',
        'price',
        'quota',
        'start_date',
        'end_date',
    ];

    /**
     * Cast kolom agar otomatis dikonversi ke tipe data yang sesuai.
     */
    protected $casts = [
        'start_date' => 'datetime',
        'end_date'   => 'datetime',
        'price'      => 'integer',
        'quota'      => 'integer',
    ];

    /**
     * Relasi ke model Event.
     */
    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}