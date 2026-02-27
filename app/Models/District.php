<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property-read \App\Models\City|null $city
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Ward> $ward
 * @property-read int|null $ward_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|District newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|District newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|District query()
 * @mixin \Eloquent
 */
class District extends BaseModel
{
    protected $fillable = ['isactive', 'code', 'name', 'city_id', 'sort_order'];

    protected function casts(): array
    {
        return ['sort_order' => 'integer'];
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    public function ward(): HasMany
    {
        return $this->hasMany(Ward::class, 'district_id');
    }
}
