<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GalleryItem extends Model
{
    protected $table    = 'gallery_items';
    protected $fillable = ['image_url', 'alt_text', 'is_active', 'sort_order'];

    public $timestamps = true;
}
