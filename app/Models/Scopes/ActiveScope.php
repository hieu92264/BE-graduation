<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class ActiveScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @template TModel of \Illuminate\Database\Eloquent\Model
     *
     * @param \Illuminate\Database\Eloquent\Builder<TModel> $builder
     * @param TModel $model
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
        $builder->where($model->qualifyColumn('isactive'), 'Y');
    }
}
