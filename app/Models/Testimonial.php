<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsActivity;

class Testimonial extends Model
{
    use LogsActivity;
    protected $table    = 'testimonials';
    protected $fillable = ['name', 'content', 'rating', 'avatar_url', 'is_active'];

    public $timestamps = true;
}
