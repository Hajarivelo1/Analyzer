<?php

// app/Models/SeoGeneration.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SeoGeneration extends Model
{
    protected $fillable = ['user_id', 'prompt', 'lang', 'title', 'meta'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }


    public function variants()
    {
        return $this->hasMany(SeoVariant::class, 'generation_id');
    }

}

