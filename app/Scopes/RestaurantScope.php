<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class RestaurantScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        if (auth()->hasUser() && auth()->user()->user_type === 'restaurant' && auth()->user()->restaurant) {
            $builder->where('restaurant_id', auth()->user()->restaurant->id);
        }
    }
}
