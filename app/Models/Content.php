<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Content newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Content newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Content query()
 * @mixin \Eloquent
 */
class Content extends BaseModel
{
    use SoftDeletes;

    protected $table = 'news';

    protected $fillable = [
        'isactive',
        'author_user_id',
        'title',
        'slug',
        'thumbnail_url',
        'content',
        'status',
        'published_at',
        'remark',
        'user_name_created',
        'user_name_updated',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
        ];
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_user_id');
    }
}
