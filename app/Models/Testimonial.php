<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsActivity;

class Testimonial extends Model
{
    use LogsActivity;
    protected $table    = 'testimonials';
    protected $fillable = ['name', 'content', 'phone', 'email', 'rating', 'avatar_url', 'is_active', 'is_admin_created'];

    public $timestamps = true;
}
