<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Plan;
use Exception;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Product;
use Stripe\Price;

class PlansController extends Controller
{
    public function index()
    {
        $plans = Plan::orderBy('id','desc')->get();
        return view('admin.plans.index',get_defined_vars());
    }
    public function create()
    {
        return view('admin.plans.add');
    }
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required',
            'price' => 'required',
            'features' => 'nullable|array',
            'features.*' => 'string'
        ]);

        try {
            Stripe::setApiKey(config('services.stripe.secret'));
            $product = Product::create([
                'name' => $request->name,
            ]);

            $stripePriceData = [
                'product' => $product->id,
                'unit_amount' => $request->price * 100,
                'currency' => 'usd',
            ];

            if ($request->recurring) {
                $stripePriceData['recurring'] = ['interval' => $request->billing_period];
            }

            $price = Price::create($stripePriceData);
            
            $plan = new Plan();
            $plan->name = $request->name;
            $plan->price = $request->price;
            $plan->billing_period = $request->recurring ? $request->billing_period : null;
            $plan->slug = str_replace('-', '_', \Str::slug($request->name));
            $plan->recurring = $request->recurring ?? null;
            $plan->stripe_plan_id = $price->id;
            $plan->description = $request->description;
            $plan->features = $request->features ?? [];
            $plan->save();
            
            return redirect('plans')->withSuccess('Plan Added Successfully');
        } catch(Exception $e) {
            \Log::error('Failed to create plan: ' . $e->getMessage());
            return redirect()->back()->withError('Failed to create plan: ' . $e->getMessage());
        }
    }
    public function edit($id)
    {
        $plan = Plan::find($id);
        return view('admin.plans.edit',get_defined_vars());
    }
    public function update(Request $request, $id)
    {
        $plan = Plan::find($id);

        // Validate features
        $features = $request->input('features', []);
        
        // Update price on Stripe
        Stripe::setApiKey(env('STRIPE_SECRET'));

        if ($request->price != $plan->price) {
            // Create a new price on Stripe
            $stripePrice = Price::create([
                'unit_amount' => $request->price * 100,
                'currency' => 'usd',
                'recurring' => $request->recurring ? ['interval' => $request->billing_period] : null,
                'product' => $plan->stripe_plan_id,
            ]);

            $plan->stripe_price_id = $stripePrice->id;
        }

        // Update the plan in the database
        $plan->name = $request->name;
        $plan->price = $request->price;
        $plan->billing_period = $request->recurring ? $request->billing_period : null;
        $plan->recurring = $request->recurring ? $request->billing_period : 1;
        $plan->description = $request->description;
        $plan->features = $features;
        $plan->update();

        return redirect('plans')->withSuccess('Plan Updated Successfully');
    }
    public function delete($id)
    {
        $plan = Plan::find($id);
        $payments = Payment::where('plan_id',$id)->first();
        if($payments){
            return redirect()->back()->withError('Subscription exist against this plan. you can not delete this.');
        }
        $plan->delete();
        return redirect('plans')->withError('Plan Deleted Successfully');
    }

    public function toggleStatus($id)
    {
        try {
            $plan = Plan::findOrFail($id);
            $plan->is_active = !$plan->is_active;
            $plan->save();

            return response()->json([
                'success' => true,
                'message' => __('messages.Plan status updated successfully'),
                'is_active' => $plan->is_active
            ]);
        } catch (\Exception $e) {
            \Log::error('Plan status toggle failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => __('messages.Failed to update plan status')
            ], 500);
        }
    }

    public function updateOrder(Request $request)
    {
        foreach ($request->order as $index => $planId) {
            Plan::where('id', $planId)->update(['sort_order' => $index]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Plan order updated successfully'
        ]);
    }

    public function toggleFeatured($id)
    {
        try {
            $plan = Plan::findOrFail($id);
            // Simply toggle the featured status
            $plan->is_featured = !$plan->is_featured;
            $plan->save();

            return response()->json([
                'success' => true,
                'message' => $plan->is_featured 
                    ? __('messages.Plan marked as featured')
                    : __('messages.Plan removed from featured'),
                'is_featured' => $plan->is_featured
            ]);
        } catch (\Exception $e) {
            \Log::error('Plan featured toggle failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => __('messages.Failed to update plan featured status')
            ], 500);
        }
    }
}
