<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'billing_period',
        'recurring',
        'stripe_plan_id',
        'stripe_price_id',
        'description',
        'is_active',
        'sort_order',
        'features',
        'badge_text',
        'badge_color',
        'slug'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'features' => 'array'
    ];
}
