<?php

namespace App\Traits;

use App\Scopes\RestaurantScope;

trait MultitenantRestaurant
{
    protected static function bootMultitenantRestaurant()
    {
        static::addGlobalScope(new RestaurantScope);

        static::creating(function ($model) {
            if (auth()->hasUser() && auth()->user()->user_type === 'restaurant' && auth()->user()->restaurant) {
                if (!$model->restaurant_id) {
                    $model->restaurant_id = auth()->user()->restaurant->id;
                }
            }
        });
    }
}
