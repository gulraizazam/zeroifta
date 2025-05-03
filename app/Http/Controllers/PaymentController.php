<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Customer;
use Stripe\Subscription;
use Stripe\PaymentMethod;
use App\Models\Payment;
use App\Models\Plan;
use App\Models\Subscription as ModelsSubscription;
use App\Models\User;
use App\Models\PaymentMethod as PaymentMethodModel;
use Illuminate\Support\Facades\Auth;
use Exception;

class PaymentController extends Controller
{
    public function showCheckoutForm()
    {
        return view('checkout');
    }

    public function purchase($id)
    {
        $plan = Plan::findOrFail($id);
        return view('company.subscribe', compact('plan'));
    }

    public function subscribe(Request $request)
    {
        $request->validate([
            'payment_method' => 'required|string',
            'plan_id' => 'required|exists:plans,id',
        ]);

        try {
        Stripe::setApiKey(config('services.stripe.secret'));

        $user = Auth::user();
        $paymentMethod = $request->payment_method;
            $plan = Plan::findOrFail($request->plan_id);

            // Create or retrieve Stripe customer
            if (!$user->stripe_customer_id) {
        $customer = Customer::create([
            'email' => $user->email,
            'name' => $user->name,
            'payment_method' => $paymentMethod,
            'invoice_settings' => [
                'default_payment_method' => $paymentMethod,
            ],
        ]);
                $user->stripe_customer_id = $customer->id;
                $user->save();
            } else {
                $customer = Customer::retrieve($user->stripe_customer_id);
            }

            // Attach the payment method to the customer
            try {
                $stripePaymentMethod = PaymentMethod::retrieve($paymentMethod);
                $stripePaymentMethod->attach(['customer' => $customer->id]);

                // Set as default payment method
                Customer::update($customer->id, [
                    'invoice_settings' => [
                        'default_payment_method' => $paymentMethod
                    ]
                ]);
            } catch (\Exception $e) {
                // Payment method might already be attached
                if (!str_contains($e->getMessage(), 'already been attached')) {
                    throw $e;
                }
            }

            // Check for existing subscription
            $existingSubscription = ModelsSubscription::where('user_id', $user->id)
                ->where('status', 'active')
                ->first();

            if ($existingSubscription) {
                // Update existing subscription
                $stripeSubscription = Subscription::retrieve($existingSubscription->stripe_subscription_id);
                $updatedSubscription = Subscription::update($stripeSubscription->id, [
                    'items' => [
                        [
                            'id' => $stripeSubscription->items->data[0]->id,
                            'price' => $plan->stripe_plan_id,
                        ],
                    ],
                    'payment_behavior' => 'allow_incomplete',
                    'proration_behavior' => 'create_prorations',
                ]);

                $existingSubscription->update([
                'plan_id' => $plan->id,
                    'status' => $updatedSubscription->status,
            ]);

                $subscription = $existingSubscription;
        } else {
                // Create new subscription
                $stripeSubscription = Subscription::create([
                'customer' => $customer->id,
                    'items' => [
                        ['price' => $plan->stripe_plan_id],
                    ],
                    'payment_behavior' => 'allow_incomplete',
                    'payment_settings' => [
                        'payment_method_types' => ['card'],
                        'save_default_payment_method' => 'on_subscription',
                    ],
                    'expand' => ['latest_invoice.payment_intent'],
                ]);

                $subscription = ModelsSubscription::create([
                    'user_id' => $user->id,
                    'stripe_customer_id' => $customer->id,
                    'stripe_subscription_id' => $stripeSubscription->id,
                    'plan_id' => $plan->id,
                    'status' => $stripeSubscription->status,
                ]);
            }

            // Store payment method in our database
            PaymentMethodModel::updateOrCreate(
                ['stripe_payment_method_id' => $paymentMethod],
                [
                    'user_id' => $user->id,
                    'method_name' => 'card',
                    'card_holder_name' => $stripePaymentMethod->billing_details->name,
                    'card_number' => $stripePaymentMethod->card->last4,
                    'expiry_date' => $stripePaymentMethod->card->exp_month . '/' . $stripePaymentMethod->card->exp_year,
                    'card_type' => $stripePaymentMethod->card->brand,
                    'is_default' => true,
                ]
            );

            // Create payment record
            Payment::create([
                'company_id' => $user->id,
                'stripe_payment_id' => $customer->id,
                'stripe_subscription_id' => $stripeSubscription->id,
                'plan' => $plan->billing_period ?? 'one-time',
                'amount' => $plan->price,
                'status' => 'active',
                'plan_id' => $plan->id,
            ]);

            // Update user subscription status
            $user->update(['is_subscribed' => true]);

            // Check if payment needs additional action
            if (
                $stripeSubscription->status === 'incomplete' &&
                $stripeSubscription->latest_invoice->payment_intent->status === 'requires_action'
            ) {
                return response()->json([
                    'requires_action' => true,
                    'payment_intent_client_secret' => $stripeSubscription->latest_invoice->payment_intent->client_secret,
                ]);
            }

            // Calculate next billing date based on the subscription period
            $nextBillingDate = now();
            if ($plan->billing_period === 'month') {
                $nextBillingDate = $nextBillingDate->addMonth();
            } elseif ($plan->billing_period === 'year') {
                $nextBillingDate = $nextBillingDate->addYear();
            }

            // Store subscription details in session for the success page
            session([
                'plan_name' => $plan->name,
                'next_billing_date' => $nextBillingDate->format('F j, Y'),
                'subscription_status' => 'active',
                'subscription_id' => $stripeSubscription->id
            ]);

            return redirect()->route('subscription.success')->with('success', 'Subscription created successfully!');

        } catch (Exception $e) {
            \Log::error('Subscription error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to process subscription: ' . $e->getMessage());
        }
    }

    public function cancel($id)
    {
        try {
            Stripe::setApiKey(config('services.stripe.secret'));
            
            $subscription = ModelsSubscription::where('stripe_subscription_id', $id)
                ->where('user_id', Auth::id())
                ->firstOrFail();

            $stripeSubscription = Subscription::retrieve($id);
            $stripeSubscription->cancel();

            $subscription->update(['status' => 'cancelled']);
            Auth::user()->update(['is_subscribed' => false]);

            return redirect()->route('subscription.index')->with('success', 'Subscription cancelled successfully.');
        } catch (Exception $e) {
            \Log::error('Subscription cancellation error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to cancel subscription: ' . $e->getMessage());
        }
    }

    public function handleWebhook(Request $request)
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        $payload = $request->getContent();
        $sig_header = $request->header('Stripe-Signature');
        $endpoint_secret = config('services.stripe.webhook_secret');

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload, $sig_header, $endpoint_secret
            );
        } catch(\UnexpectedValueException $e) {
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch(\Stripe\Exception\SignatureVerificationException $e) {
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        switch ($event->type) {
            case 'customer.subscription.updated':
            case 'customer.subscription.deleted':
                $subscription = $event->data->object;
                $this->handleSubscriptionUpdate($subscription);
                break;
            case 'invoice.payment_succeeded':
                $invoice = $event->data->object;
                $this->handleSuccessfulPayment($invoice);
                break;
            case 'invoice.payment_failed':
                $invoice = $event->data->object;
                $this->handleFailedPayment($invoice);
                break;
        }

        return response()->json(['status' => 'success']);
    }

    protected function handleSubscriptionUpdate($stripeSubscription)
    {
        $subscription = ModelsSubscription::where('stripe_subscription_id', $stripeSubscription->id)->first();
        if ($subscription) {
            $subscription->update(['status' => $stripeSubscription->status]);
            
            if ($stripeSubscription->status === 'canceled') {
                $subscription->user->update(['is_subscribed' => false]);
            }
        }
    }

    protected function handleSuccessfulPayment($invoice)
    {
        if ($invoice->subscription) {
            $subscription = ModelsSubscription::where('stripe_subscription_id', $invoice->subscription)->first();
            if ($subscription) {
                $subscription->update(['status' => 'active']);
                $subscription->user->update(['is_subscribed' => true]);
            }
        }
    }

    protected function handleFailedPayment($invoice)
    {
        if ($invoice->subscription) {
            $subscription = ModelsSubscription::where('stripe_subscription_id', $invoice->subscription)->first();
            if ($subscription) {
                $subscription->update(['status' => 'past_due']);
            }
        }
    }
}
