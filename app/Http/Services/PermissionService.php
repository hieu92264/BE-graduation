<?php

namespace App\Http\Services;

use App\Http\_base\BaseService;
use App\Http\Interfaces\PermissionServiceInterface;
use App\Models\Permission;

class PermissionService extends BaseService implements PermissionServiceInterface
{

    protected function getModel(): string
    {
        // TODO: Implement getModel() method.
        return Permission::class;
    }

    public function getPermissionOptions(): array
    {
        try {
            $data = $this->model->all(['id', 'name'])->map(function ($item) {
                return [
                    'label' => $item->name,
                    'value' => $item->id,
                ];
            });

            return $data->toArray();
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
