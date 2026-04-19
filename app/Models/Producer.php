<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Producer extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'farm_name',
        'location',
        'description',
        'logo',
        'registration_number',
        'is_verified',
        'social_links',
    ];

    protected $casts = [
        'social_links' => 'array',
        'is_verified' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
