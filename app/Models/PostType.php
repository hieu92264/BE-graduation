<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PostType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PostType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PostType query()
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Room> $rooms
 * @property-read int|null $rooms_count
 * @mixin \Eloquent
 */
class PostType extends BaseModel
{
    protected $fillable = [
        'isactive',
        'code',
        'name',
        'priority',
        'default_days',
        'price',
        'remark',
        'user_name_created',
        'user_name_updated',
    ];

    protected function casts(): array
    {
        return [
            'priority' => 'integer',
            'default_days' => 'integer',
            'price' => 'integer',
        ];
    }

    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class, 'post_type_id', 'id');
    }
}
