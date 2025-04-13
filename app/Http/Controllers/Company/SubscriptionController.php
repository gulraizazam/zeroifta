<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubscriptionController extends Controller
{
    public function index()
    {
        $plans = Plan::where('is_active', true)
            ->orderBy('price')
            ->get()
            ->map(function($plan) {
                // Normalize billing period values
                if ($plan->billing_period === 'yearly' || $plan->billing_period === 'year') {
                    $plan->billing_period = 'year';
                } elseif ($plan->billing_period === 'monthly' || $plan->billing_period === 'month') {
                    $plan->billing_period = 'month';
                }
                return $plan;
            });
        
        // Debug plan data
        \Log::info('Plans data:', [
            'monthly_plans' => $plans->where('billing_period', 'month')->count(),
            'yearly_plans' => $plans->where('billing_period', 'year')->count(),
            'other_plans' => $plans->whereNotIn('billing_period', ['month', 'year'])->count(),
            'featured_plan' => $plans->where('is_featured', true)->first()
        ]);
        
        $yearlyDiscount = $this->calculateYearlySavings($plans);
        
        $availableFeatures = [
            'vehicles.all' => 'View All Vehicles',
            'vehicles.create' => 'Create Vehicle',
            'vehicles.edit' => 'Edit Vehicle',
            'vehicles.delete' => 'Delete Vehicle',
            'vehicles.import' => 'Import Vehicles',
            'driver_vehicles.index' => 'View Driver Vehicles',
            'driver_vehicles.create' => 'Assign Vehicles to Drivers',
            'driver_vehicles.edit' => 'Edit Driver Vehicle Assignment',
            'driver_vehicles.delete' => 'Remove Driver Vehicle Assignment',
            'drivers.all' => 'View All Drivers',
            'drivers.create' => 'Create Driver',
            'drivers.edit' => 'Edit Driver',
            'drivers.delete' => 'Delete Driver',
            'drivers.import' => 'Import Drivers',
            'drivers.track' => 'Track Drivers',
            'fleet.view' => 'View Fleet Management',
        ];

        return view('subscription', compact('plans', 'availableFeatures', 'yearlyDiscount'));
    }

    private function calculateYearlySavings($plans)
    {
        $maxSavings = 0;
        $monthlyPlans = $plans->where('billing_period', 'month');
        $yearlyPlans = $plans->where('billing_period', 'year');

        foreach ($monthlyPlans as $monthlyPlan) {
            // Find corresponding yearly plan
            $yearlyPlan = $yearlyPlans->firstWhere('name', $monthlyPlan->name);
            
            if ($yearlyPlan) {
                // Calculate yearly cost if paying monthly
                $yearlyPriceIfMonthly = $monthlyPlan->price * 12;
                
                // Calculate actual yearly price
                $yearlyPrice = $yearlyPlan->price;
                
                // Calculate savings percentage
                $savings = (($yearlyPriceIfMonthly - $yearlyPrice) / $yearlyPriceIfMonthly) * 100;
                
                $maxSavings = max($maxSavings, round($savings));
            }
        }

        return $maxSavings;
    }
} 