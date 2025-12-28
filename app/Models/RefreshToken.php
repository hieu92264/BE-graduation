<?php

namespace App\Models;

use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\SoftDeletes;

class RefreshToken extends BaseModel
{
    use MassPrunable;

    protected $fillable = [
        'íactive',
        'user_id',
        'expires_at',
        'token'
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
        ];
    }

    public function prunable()
    {
        return static::where('expires_at', '<', now());
    }
}
