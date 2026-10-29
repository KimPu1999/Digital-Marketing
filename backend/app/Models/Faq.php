<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Faq extends Model
{
    use SoftDeletes;
    protected $fillable = ['question', 'answer', 'category', 'sort_order', 'status'];
    protected $casts = ['sort_order' => 'integer'];
}
