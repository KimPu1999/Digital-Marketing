<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Testimonial extends Model
{
    use SoftDeletes;
    protected $fillable = ['quote', 'name', 'role', 'company', 'is_approved', 'status'];
    protected $casts = ['is_approved' => 'boolean'];
}
