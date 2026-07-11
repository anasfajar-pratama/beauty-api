<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hero extends Model
{
    protected $table = 'heroes';

    protected $fillable = [
        'type',
        'brand_key',
        'product_type',
        'theme',
        'title',
        'subtitle',
        'description',
        'button_text',
        'button_link',
        'hero_image',
        'logo',
        'logo_style',
        'label',
        'label_color',
        'event_date',
        'event_end_date',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'event_date' => 'date',
            'event_end_date' => 'date',
            'sort_order' => 'integer',
        ];
    }
}
