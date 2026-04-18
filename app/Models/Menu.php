<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\MultitenantRestaurant;

class Menu extends Model
{
    use SoftDeletes, MultitenantRestaurant;

    protected $fillable = [
        'restaurant_id',
        'name',
        'description',
        'is_active',
    ];

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function menuItems()
    {
        return $this->hasMany(MenuItem::class);
    }
}
