<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MediaAsset extends Model
{
    use SoftDeletes;
    protected $fillable = ['uploaded_by', 'name', 'disk', 'path', 'mime_type', 'size', 'alt_text'];
    protected $casts = ['size' => 'integer'];
    public function uploader(): BelongsTo { return $this->belongsTo(User::class, 'uploaded_by'); }
}
