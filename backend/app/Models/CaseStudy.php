<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CaseStudy extends Model
{
    use SoftDeletes;
    protected $fillable = ['portfolio_project_id', 'results', 'gallery', 'body', 'status', 'published_at'];
    protected $casts = ['results' => 'array', 'gallery' => 'array', 'published_at' => 'datetime'];
    public function project(): BelongsTo { return $this->belongsTo(PortfolioProject::class, 'portfolio_project_id'); }
}
