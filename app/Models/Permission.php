<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Permission extends BaseModel
{
    use SoftDeletes;
    protected $fillable = [
        'isactive',
        'code',
        'name',
        'parent_id',
        'url',
        'user_name_created',
        'user_name_updated'
    ];

    protected function casts(): array
    {
        return [];
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'permission_user')->withTimestamps();
    }
}
