<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'stripe_subscription_id',
        'stripe_plan_id',
        'status',
        'trial_ends_at',
        'ends_at',
    ];
}
