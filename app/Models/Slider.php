<?php

namespace App\Models;

/**
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Slider newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Slider newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Slider query()
 * @mixin \Eloquent
 */
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
