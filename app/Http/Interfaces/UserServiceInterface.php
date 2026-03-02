<?php

namespace App\Http\Interfaces;

use App\Http\_base\BaseServiceInterface;

interface UserServiceInterface extends BaseServiceInterface
{
    public function getPermissions(string $id): array;

    public function syncPermissions(string $id, array $permission_ids): array;
}
