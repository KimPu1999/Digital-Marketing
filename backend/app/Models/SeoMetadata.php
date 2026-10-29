<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class SeoMetadata extends Model
{
    protected $fillable = ['title', 'description', 'canonical_url', 'og_image'];
    public function seoable(): MorphTo { return $this->morphTo(); }
}
