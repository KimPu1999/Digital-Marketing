<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServiceCategory extends Model
{
    use SoftDeletes;
    protected $fillable = ['name', 'slug', 'description', 'status', 'sort_order'];
    protected $casts = ['sort_order' => 'integer'];
    public function services(): HasMany { return $this->hasMany(Service::class); }
}
