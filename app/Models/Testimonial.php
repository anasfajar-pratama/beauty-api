<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    protected $table    = 'testimonials';
    protected $fillable = ['name', 'content', 'rating', 'avatar_url', 'is_active'];

    public $timestamps = true;
}
