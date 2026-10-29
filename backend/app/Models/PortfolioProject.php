<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PortfolioProject extends Model
{
    use SoftDeletes;
    protected $fillable = ['title', 'slug', 'client_label', 'category', 'year', 'summary', 'result_summary', 'challenge', 'strategy', 'accent', 'initials', 'status', 'published_at'];
    protected $casts = ['published_at' => 'datetime'];
    public function caseStudy(): HasOne { return $this->hasOne(CaseStudy::class); }
}
