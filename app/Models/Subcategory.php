<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsActivity;

class Subcategory extends Model
{
    use LogsActivity;
    protected $fillable = ['category', 'name', 'slug'];

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
