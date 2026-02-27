<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property-read \App\Models\District|null $district
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ward newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ward newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ward query()
 * @mixin \Eloquent
 */
class Ward extends BaseModel
{
    protected $fillable = ['isactive', 'code', 'name', 'district_id', 'sort_order'];

    protected function casts(): array
    {
        return ['sort_order' => 'integer'];
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class, 'district_id');
    }
}
