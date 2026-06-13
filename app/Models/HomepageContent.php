<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomepageContent extends Model
{
    protected $table    = 'homepage_contents';
    protected $fillable = ['key', 'value'];

    public $timestamps = true;
}
