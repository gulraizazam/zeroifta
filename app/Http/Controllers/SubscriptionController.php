<?php

namespace App\Http\Controllers;

use App\Models\PaymentMethod;
use App\Models\Plan;
use App\Models\SelectedPlan;
use App\Models\Subscription;
use App\Models\User;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Token;
use Illuminate\Support\Facades\Auth;

class SubscriptionController extends Controller
{
    protected $subscriptionService;

    public function __construct(SubscriptionService $subscriptionService)
    {
        $this->subscriptionService = $subscriptionService;
    }

    /**
     * Subscribe to a plan.
     */
    public function subscribe(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'plan_id' => 'required|string',
            'payment_method_id' => 'nullable|string',
        ]);

        try {
            \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

            $user = User::find($request->user_id);

            if (!$user->stripe_customer_id) {
                $stripeCustomer = \Stripe\Customer::create([
                    'email' => $user->email,
                    'name' => $user->name,
                ]);
                $user->stripe_customer_id = $stripeCustomer->id;
                $user->save();
            }

            $priceId = Plan::find($request->plan_id)->stripe_plan_id;

            $currentSubscription = \Stripe\Subscription::all([
                'customer' => $user->stripe_customer_id,
                'status' => 'active',
            ]);

            if ($currentSubscription->data) {
                $subscriptionId = $currentSubscription->data[0]->id;

                if ($request->has('payment_method_id')) {
                    // Attach the new payment method to the customer
                    $stripePaymentMethod = \Stripe\PaymentMethod::retrieve($request->payment_method_id);
                    $stripePaymentMethod->attach(['customer' => $user->stripe_customer_id]);

                    // Set the new payment method as default
                    \Stripe\Customer::update(
                        $user->stripe_customer_id,
                        ['invoice_settings' => ['default_payment_method' => $request->payment_method_id]]
                    );
                }

                $updatedSubscription = \Stripe\Subscription::update($subscriptionId, [
                    'items' => [
                        ['id' => $currentSubscription->data[0]->items->data[0]->id, 'price' => $priceId],
                    ],
                ]);
                Subscription::where('stripe_customer_id', $user->stripe_customer_id)->delete();
                $request->user()->subscriptions()->create([
                    'stripe_customer_id' => $user->stripe_customer_id,
                    'stripe_subscription_id' => $updatedSubscription->id,
                    'plan_id' => $request->plan_id,
                    'status' => 'active',
                ]);
                $features =[];
                $checkSubscription = Subscription::where('user_id',$user->id)->where('status','active')->first();
                if($checkSubscription){
                    $planName = Plan::where('id',$checkSubscription->plan_id)->first();
                    if($planName->slug == "basic_monthly" || $planName->slug == "basic_yearly"){
                        $features = [
                            'minimum_gallons'=>false,
                            'add_stop'=>false,
                            'change_reserve_fuel'=>false,
                            'customize_fuel_tank_capacity' =>false,
                        ];
                    }else{
                        $features = [
                            'minimum_gallons'=>true,
                            'add_stop'=>true,
                            'change_reserve_fuel'=>true,
                            'customize_fuel_tank_capacity' =>true,
                        ];
                    }

                }else{
                    $features = [
                        'minimum_gallons'=>false,
                        'add_stop'=>false,
                        'change_reserve_fuel'=>false,
                        'customize_fuel_tank_capacity' =>false,
                    ];
                }
                return response()->json([
                    'status' => 200,
                    'message' => 'Subscription updated successfully',
                    'data' => [
                        'subscription_id' => $updatedSubscription->id,
                        'plan_name' => $planName->name ?? null,
                        'price' => $priceId, // Assuming $priceId holds the correct price
                        'status' => $updatedSubscription->status,
                        'features' => $features,
                    ],
                ]);
            } else {
                $subscriptionData = [
                    'customer' => $user->stripe_customer_id,
                    'items' => [
                        ['price' => $priceId],
                    ],
                    'expand' => ['latest_invoice.payment_intent'],
                ];

                if ($request->has('payment_method_id')) {
                    $stripePaymentMethod = \Stripe\PaymentMethod::retrieve($request->payment_method_id);
                    $stripePaymentMethod->attach(['customer' => $user->stripe_customer_id]);

                    \Stripe\Customer::update(
                        $user->stripe_customer_id,
                        ['invoice_settings' => ['default_payment_method' => $request->payment_method_id]]
                    );
                }

                $newSubscription = \Stripe\Subscription::create($subscriptionData);
                $features =[];
                $subscriptionModel = new Subscription();
                $subscriptionModel->user_id = $user->id;
                $subscriptionModel->stripe_customer_id = $user->stripe_customer_id;
                $subscriptionModel->stripe_subscription_id = $newSubscription->id;
                $subscriptionModel->plan_id = $request->plan_id;
                $subscriptionModel->status = 'active';
                $subscriptionModel->save();

                $user->is_subscribed = true;
                $user->is_confirmation_available = 0;
                $user->save();
                $checkSubscription = Subscription::where('user_id',$user->id)->where('status','active')->first();
                if($checkSubscription){
                    $planName = Plan::where('id',$checkSubscription->plan_id)->first();
                    if($planName->slug == "basic_monthly" || $planName->slug == "basic_yearly"){
                        $features = [
                            'minimum_gallons'=>false,
                            'add_stop'=>false,
                            'change_reserve_fuel'=>false,
                            'customize_fuel_tank_capacity' =>false,
                        ];
                    }else{
                        $features = [
                            'minimum_gallons'=>true,
                            'add_stop'=>true,
                            'change_reserve_fuel'=>true,
                            'customize_fuel_tank_capacity' =>true,
                        ];
                    }
                }else{
                    $features = [
                        'minimum_gallons'=>false,
                        'add_stop'=>false,
                        'change_reserve_fuel'=>false,
                        'customize_fuel_tank_capacity' =>false,
                    ];
                }
                //$newSubscription->subscription = $checkSubscription;
                $newSubscription->features = $features;
                SelectedPlan::where('user_id',$user->id)->delete();
                return response()->json([
                    'status' => 200,
                    'message' => 'Subscription created successfully',
                  'data' => [
                        'subscription_id' => $newSubscription->id,
                        'plan_name' => $planName->name ?? null,
                        'price' => $priceId, // Assuming $priceId holds the correct price
                        'status' => $newSubscription->status,
                        'features' => $features,
                    ],
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => $e->getMessage(),
                'data' => (object)[],
            ], 500);
        }
    }

    /**
     * Get available subscription plans.
     */
    public function getPlans(Request $request)
    {
        $plans = $this->subscriptionService->getPlans($request->all());

        return response()->json([
            'status' => 200,
            'message' => 'Subscription plans retrieved successfully',
            'data' => $plans,
        ]);
    }

    /**
     * Cancel the user's subscription.
     */
    public function cancelSubscription(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        try {
            // Set Stripe secret key
            \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

            // Get the user from the database
            $user = User::find($request->user_id);

            // Check if the user has a Stripe customer ID
            if (!$user->stripe_customer_id) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Stripe customer not found for this user.',
                    'data' => (object)[],
                ], 404);
            }

            // Find the active subscription for the user
            $subscription = Subscription::where('user_id', $request->user_id)
                ->where('status', 'active')  // Only consider active subscriptions
                ->first();

            if (!$subscription) {
                return response()->json([
                    'status' => 404,
                    'message' => 'No active subscription found for this user.',
                    'data' => (object)[],
                ], 404);
            }

            // Retrieve the subscription from Stripe
            $stripeSubscription = \Stripe\Subscription::retrieve($subscription->stripe_subscription_id);

            // Cancel the subscription on Stripe
            $cancelledSubscription = $stripeSubscription->cancel();

            // Update the subscription status in the database
            $subscription->status = 'cancelled';
            $subscription->save();

            // Update the user's subscription status
            $user->is_subscribed = false;
            $user->save();

            return response()->json([
                'status' => 200,
                'message' => 'Subscription cancelled successfully',
                'data' => $cancelledSubscription,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Failed to cancel subscription',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get the authenticated user's subscription details.
     */
    public function getSubscriptionDetails()
    {
        $subscription = Subscription::where('user_id', auth()->id())->first();

        if (!$subscription) {
            return response()->json([
                'status' => 404,
                'message' => 'No active subscription found',
                'data' => (object) [],
            ], 404);
        }

        return response()->json([
            'status' => 200,
            'message' => 'Subscription details retrieved successfully',
            'data' => $subscription,
        ]);
    }

    public function generateToken(Request $request)
    {
        // Validate the card details
        $request->validate([
            'number'    => 'required|digits_between:13,19',
            'exp_month' => 'required|integer|between:1,12',
            'exp_year'  => 'required|integer|min:' . date('Y'),
            'cvc'       => 'required|digits_between:3,4',
        ]);

        try {
            // Set Stripe API key
            Stripe::setApiKey(env('STRIPE_SECRET'));

            // Create a token
            $token = Token::create([
                'card' => [
                    'number'    => $request->number,
                    'exp_month' => $request->exp_month,
                    'exp_year'  => $request->exp_year,
                    'cvc'       => $request->cvc,
                ],
            ]);

            return response()->json([
                'status' => true,
                'token'   => $token->id,
            ], 200);
        } catch (\Stripe\Exception\CardException $e) {
            // Handle specific card errors
            return response()->json([
                'success' => false,
                'message' => $e->getError()->message,
            ], 400);
        } catch (\Exception $e) {
            // Handle other exceptions
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function storeSelectedPlan(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'plan_id' => 'required|exists:plans,id',
            'payment_method_id' => 'nullable|exists:payment_methods,stripe_payment_method_id',
        ]);

        // Find the default payment method or the one provided in the request
        $checkCard = PaymentMethod::where('user_id', $request->user_id)
                                ->where('is_default', 1)
                                ->first();

        $findCard = PaymentMethod::where('stripe_payment_method_id', $request->payment_method_id)->first();
        if($findCard){
            $findCard->is_default = true;
        }
        $paymentMethodId = $findCard ? $findCard->id : ($checkCard ? $checkCard->id : null);

        if (!$paymentMethodId) {
            return response()->json([
                'status' => 400,
                'message' => 'No valid payment method found for the user.',
                'data' => (object) [],
            ], 400);
        }

       // Ensure only one SelectedPlan exists for the user
        $existingPlan = SelectedPlan::where('user_id', $request->user_id)->first();

        if ($existingPlan) {
            // Update the existing plan
            $existingPlan->update([
                'plan_id' => $request->plan_id,
                'payment_method_id' => $paymentMethodId,
            ]);

        } else {
            // Create a new plan
            $existingPlan = SelectedPlan::create([
                'user_id' => $request->user_id,
                'plan_id' => $request->plan_id,
                'payment_method_id' => $paymentMethodId,
            ]);

        }

        // Update user confirmation availability
        $user = User::find($request->user_id);
        $user->update(['is_confirmation_available' => 1]);

        return response()->json([
            'status' => 200,
            'message' => 'Selected plan stored successfully',
            'data' => $existingPlan,
        ]);
    }

    public function getSelectedPlan(Request $request)
    {
        $selectedPlan = SelectedPlan::with(['user','plan','paymentMethod'])->where('user_id',$request->user_id)->first();
        if(isset($selectedPlan)){
            $selectedPlan->plan->price = '$'.$selectedPlan->plan->price;
            if($selectedPlan->plan->billing_period=="month"){
                $selectedPlan->plan->billing_period="monthly";
            }
            if($selectedPlan->plan->billing_period=="year"){
                $selectedPlan->plan->billing_period="yearly";
            }
            return response()->json(['status'=>200,'message'=>'selected plan fetched','data'=>$selectedPlan]);
        }

        return response()->json(['status'=>404,'message'=>'no selected plan available','data'=>(object)[]]);
    }

    public function index()
    {
        $plans = Plan::where('is_active', true)->orderBy('price')->get();
        
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

        return view('subscription', compact('plans', 'availableFeatures'));
    }
}
