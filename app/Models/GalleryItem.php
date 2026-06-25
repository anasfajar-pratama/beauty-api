<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsActivity;

class GalleryItem extends Model
{
    use LogsActivity;
    protected $table    = 'gallery_items';
    protected $fillable = ['image_url', 'alt_text', 'is_active', 'sort_order'];

    public $timestamps = true;
}
