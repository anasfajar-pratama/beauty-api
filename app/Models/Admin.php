<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Admin extends Authenticatable
{
    use HasApiTokens;

    protected $table    = 'admins';
    protected $fillable = ['username', 'password'];
    protected $hidden   = ['password', 'remember_token'];

    public $timestamps = true;
}
