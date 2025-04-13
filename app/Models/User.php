<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }
    public function vehicles()
    {
        return $this->hasMany(Vehicle::class, 'owner_id');
    }

    public function subscription()
    {
        return $this->hasOne(Subscription::class)->where('status', 'active');
    }

    public function hasFeature($feature)
    {
        $subscription = $this->subscription;
        if (!$subscription || !$subscription->plan || !$subscription->plan->is_active) {
            return false;
        }
        
        return is_array($subscription->plan->features) && in_array($feature, $subscription->plan->features);
    }
}
