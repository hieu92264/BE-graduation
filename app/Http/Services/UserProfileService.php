<?php

namespace App\Http\Services;

use App\Http\_base\BaseService;
use App\Http\Interfaces\UserProfileServiceInterface;
use App\Models\UserProfiles;

class UserProfileService extends BaseService implements UserProfileServiceInterface
{

    protected function getModel(): string
    {
        // TODO: Implement getModel() method.
        return UserProfiles::class;
    }
}
