<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property-read \App\Models\Comment|null $comment
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommentReply newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommentReply newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommentReply onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommentReply query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommentReply withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommentReply withoutTrashed()
 * @mixin \Eloquent
 */
class CommentReply extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'comment_id',
        'user_id',
        'content',
        'status',
    ];

    public function comment(): BelongsTo
    {
        return $this->belongsTo(Comment::class, 'comment_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
