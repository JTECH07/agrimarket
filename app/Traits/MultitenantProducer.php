<?php

namespace App\Traits;

use App\Scopes\ProducerScope;

trait MultitenantProducer
{
    protected static function bootMultitenantProducer()
    {
        static::addGlobalScope(new ProducerScope);

        static::creating(function ($model) {
            if (auth()->hasUser() && auth()->user()->user_type === 'producer' && auth()->user()->producer) {
                if (!$model->producer_id) {
                    $model->producer_id = auth()->user()->producer->id;
                }
            }
        });
    }
}
