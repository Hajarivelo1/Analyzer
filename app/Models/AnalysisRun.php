<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnalysisRun extends Model
{
    protected $fillable = [
        'project_id',
        'seo_metrics',
        'pagespeed_opportunities',
        'ai_summary',
    ];

    public function project()
{
    return $this->belongsTo(\App\Models\Project::class);
}

}
