<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CheckPlanFeature
{
    public function handle(Request $request, Closure $next, $feature)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();
        $subscription = $user->subscription;

        // Log the current state for debugging
        Log::info('Feature check', [
            'user_id' => $user->id,
            'feature' => $feature,
            'has_subscription' => (bool)$subscription,
            'subscription_status' => $subscription ? $subscription->status : null,
            'plan_id' => $subscription && $subscription->plan ? $subscription->plan->id : null,
            'plan_features' => $subscription && $subscription->plan ? $subscription->plan->features : [],
        ]);

        // Check if user has an active subscription
        if (!$subscription) {
            return redirect()->route('subscription')
                ->with('error', __('messages.Please subscribe to access this feature'));
        }

        // Load the plan relationship if not already loaded
        if (!$subscription->relationLoaded('plan')) {
            $subscription->load('plan');
        }

        $plan = $subscription->plan;

        // Check if plan exists and is active
        if (!$plan || !$plan->is_active) {
            return redirect()->route('subscription')
                ->with('error', __('messages.Your subscription plan is not active'));
        }

        // Ensure features is an array and contains the required feature
        $features = is_array($plan->features) ? $plan->features : [];
        if (!in_array($feature, $features)) {
            return redirect()->route('subscription')
                ->with('error', __('messages.Your current plan does not include this feature'));
        }

        return $next($request);
    }
} 