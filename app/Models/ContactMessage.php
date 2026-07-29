<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    protected $table = 'contact_messages';

    protected $fillable = ['name', 'phone', 'email', 'message', 'is_read'];

    protected $casts = [
        'is_read' => 'boolean',
    ];
}
