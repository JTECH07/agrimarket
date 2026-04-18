<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Delivery extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'order_id',
        'delivery_agent_id',
        'status',
        'tracking_number',
        'delivery_notes',
        'picked_up_at',
        'delivered_at',
        'proof_of_delivery',
    ];

    protected $casts = [
        'picked_up_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function deliveryAgent()
    {
        return $this->belongsTo(DeliveryAgent::class);
    }
}
