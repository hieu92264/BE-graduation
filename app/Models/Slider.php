<?php

namespace App\Models;

class Slider extends BaseModel
{
    protected $fillable = [
        'isactive',
        'title',
        'image_url',
        'link_url',
        'sort_order',
        'remark',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
        ];
    }
}
