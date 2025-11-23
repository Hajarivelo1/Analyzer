<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SeoVariant extends Model
{
    protected $fillable = ['generation_id', 'title', 'meta'];

    public function generation()
    {
        return $this->belongsTo(SeoGeneration::class, 'generation_id');
    }
}

