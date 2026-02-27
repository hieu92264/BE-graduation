<?php

namespace App\Models;

use App\Common\Enums\UserType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProfiles newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProfiles newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProfiles query()
 * @mixin \Eloquent
 */
class UserProfiles extends BaseModel
{
    protected $table = 'user_profiles';
    protected $fillable = [
        'isactive',
        'user_id',
        'full_name',
        'phone_number',
        'avatar_url',
        'address',
        'zalo',
        'facebook',
        'user_type',
        'remark',
        'user_name_created',
        'user_name_updated',
    ];

    protected function casts(): array
    {
        return [
            'user_type' => UserType::class,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
