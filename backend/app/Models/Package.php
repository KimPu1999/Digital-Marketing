<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Package extends Model
{
    use SoftDeletes;
    protected $fillable = ['name', 'slug', 'eyebrow', 'description', 'price', 'price_label', 'price_note', 'is_featured', 'status', 'published_at'];
    protected $casts = ['price' => 'decimal:2', 'is_featured' => 'boolean', 'published_at' => 'datetime'];
    public function features(): HasMany { return $this->hasMany(PackageFeature::class)->orderBy('sort_order'); }
}
