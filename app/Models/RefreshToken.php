<?php

namespace App\Models;

use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property string $isactive
 * @property int $user_id
 * @property string $token
 * @property \Illuminate\Support\Carbon $expires_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RefreshToken newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RefreshToken newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RefreshToken query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RefreshToken whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RefreshToken whereExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RefreshToken whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RefreshToken whereIsactive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RefreshToken whereToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RefreshToken whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RefreshToken whereUserId($value)
 * @property-read \App\Models\User $user
 * @mixin \Eloquent
 */
class RefreshToken extends BaseModel
{
    use MassPrunable;

    protected $fillable = [
        'isactive',
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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
