<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BlogPost extends Model
{
    use SoftDeletes;
    protected $fillable = ['blog_category_id', 'author_id', 'title', 'slug', 'excerpt', 'body', 'reading_time', 'status', 'published_at'];
    protected $casts = ['reading_time' => 'integer', 'published_at' => 'datetime'];
    public function category(): BelongsTo { return $this->belongsTo(BlogCategory::class, 'blog_category_id'); }
    public function author(): BelongsTo { return $this->belongsTo(User::class, 'author_id'); }
}
