<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category query()
 * @mixin \Eloquent
 */
class Category extends BaseModel
{
    protected $fillable = [
        'isactive',
        'code',
        'name',
        'slug',
        'sort_order',
        'remark',
        'user_name_created',
        'user_name_updated',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer'
        ];
    }

    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class, 'category_id');
    }
}
