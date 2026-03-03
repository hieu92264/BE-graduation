<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class RolePermissions extends Model
{
    protected $table = 'role_permissions';

    protected $fillable = [
        'user_type',
        'permission_id',
    ];

    public function permissions(): BelongsToMany {
        return $this->belongsToMany(Permission::class, 'permission_user');
    }
}
