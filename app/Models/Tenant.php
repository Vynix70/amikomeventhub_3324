<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable; // Supaya tenant bisa login sendiri

class Tenant extends Authenticatable
{
    use HasFactory;

    protected $fillable = ['name', 'status','slug', 'email', 'password', 'description', 'logo'];
    protected $hidden = ['password'];

    // Relasi: Satu Organisasi bisa membuat banyak Event
    public function events()
    {
        return $this->hasMany(Event::class);
    }
}