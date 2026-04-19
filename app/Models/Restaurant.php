<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Restaurant extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'name',
        'location',
        'description',
        'banner',
        'logo',
        'is_open',
        'opening_hours',
    ];

    protected $casts = [
        'opening_hours' => 'array',
        'is_open' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function menus()
    {
        return $this->hasMany(Menu::class);
    }

    public function reviews()
    {
        return $this->morphMany(Review::class, 'reviewable');
    }
}
