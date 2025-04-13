<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckPlanFeature
{
    public function handle(Request $request, Closure $next, $feature)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();
        $subscription = $user->subscription;

        if (!$subscription || !$subscription->plan) {
            return redirect()->route('subscription')
                ->with('error', __('messages.Please subscribe to access this feature'));
        }

        $plan = $subscription->plan;
        if (!$plan->is_active || !in_array($feature, $plan->features ?? [])) {
            return redirect()->route('subscription')
                ->with('error', __('messages.Your current plan does not include this feature'));
        }

        return $next($request);
    }
} 