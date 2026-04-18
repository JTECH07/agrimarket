<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class ProducerScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        if (auth()->hasUser() && auth()->user()->user_type === 'producer' && auth()->user()->producer) {
            $builder->where('producer_id', auth()->user()->producer->id);
        }
    }
}
