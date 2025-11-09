<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;



class Plan extends Model
{
    protected $guarded = [];
    // Scope pour récupérer les plans actifs
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
