<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Service extends Model
{
    use SoftDeletes;
    protected $fillable = ['service_category_id', 'title', 'slug', 'short_title', 'excerpt', 'description', 'benefits', 'deliverables', 'process', 'related_service_slugs', 'icon', 'status', 'published_at'];
    protected $casts = ['benefits' => 'array', 'deliverables' => 'array', 'process' => 'array', 'related_service_slugs' => 'array', 'published_at' => 'datetime'];
    public function category(): BelongsTo { return $this->belongsTo(ServiceCategory::class, 'service_category_id'); }
}
