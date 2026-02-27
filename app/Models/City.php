<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\District> $district
 * @property-read int|null $district_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|City newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|City newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|City query()
 * @mixin \Eloquent
 */
class City extends BaseModel
{
    protected $fillable = ['isactive', 'code', 'name', 'sort_order'];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
        ];
    }

    public function district(): HasMany
    {
        return $this->hasMany(District::class, 'city_id');
    }
}
