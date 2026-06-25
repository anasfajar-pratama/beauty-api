<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsActivity;

class Product extends Model
{
    use LogsActivity;
    protected $table    = 'products';
    protected $fillable = [
        'name', 'category', 'tagline', 'ingredients',
        'benefits', 'how_to_use', 'image_url', 'sort_order',
        'subcategory_id', 'is_promo', 'is_new', 'description',
        'weight', 'dimensions', 'bpom_number', 'certifications',
        'halal_certified', 'warranty_info', 'price',
    ];

    public $timestamps = true;

    public function subcategory()
    {
        return $this->belongsTo(Subcategory::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order')->orderBy('id');
    }

    public function primaryImage()
    {
        return $this->hasOne(ProductImage::class)->where('is_primary', true);
    }
}
