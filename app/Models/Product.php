<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table    = 'products';
    protected $fillable = [
        'name', 'category', 'tagline', 'ingredients',
        'benefits', 'how_to_use', 'image_url', 'sort_order',
    ];

    public $timestamps = true;
}
