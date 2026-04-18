<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DeliveryAgent extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'vehicle_type',
        'vehicle_registration',
        'is_available',
        'current_latitude',
        'current_longitude',
    ];

    protected $casts = [
        'is_available' => 'boolean',
        'current_latitude' => 'decimal:8',
        'current_longitude' => 'decimal:8',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function deliveries()
    {
        return $this->hasMany(Delivery::class);
    }
}
