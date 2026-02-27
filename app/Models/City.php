<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

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
