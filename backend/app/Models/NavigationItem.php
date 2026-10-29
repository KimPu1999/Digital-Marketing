<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NavigationItem extends Model
{
    use SoftDeletes;
    protected $fillable = ['parent_id', 'label', 'href', 'location', 'sort_order', 'is_visible', 'status'];
    protected $casts = ['is_visible' => 'boolean', 'sort_order' => 'integer'];
    public function parent(): BelongsTo { return $this->belongsTo(self::class, 'parent_id'); }
    public function children(): HasMany { return $this->hasMany(self::class, 'parent_id')->orderBy('sort_order'); }
}
