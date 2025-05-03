@extends('layouts.new_main')

@section('content')
<style>
  .card-element {
    border: 1px solid #e2e8f0;
    padding: 15px;
    border-radius: 8px;
    background-color: white;
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
    transition: all 0.2s ease;
  }
  .card-element:focus {
    box-shadow: 0 0 0 2px rgba(66, 153, 225, 0.5);
    outline: none;
  }
  .StripeElement {
    width: 100%;
    padding: 10px;
  }
  .subscription-summary {
    background-color: #f7fafc;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 20px;
  }
  .error-message {
    color: #e53e3e;
    margin-top: 8px;
    font-size: 14px;
  }
  .payment-status {
    display: none;
    padding: 15px;
    border-radius: 8px;
    margin-top: 15px;
  }
  .payment-status.success {
    background-color: #c6f6d5;
    color: #2f855a;
  }
  .payment-status.error {
    background-color: #fed7d7;
    color: #c53030;
  }
</style>

<div class="dashbord-inner">
  <div class="manage-comp mb-4">
    <div class="Filters-main mb-3 mb-md-4">
      <div class="sec1-style">
        <div class="subs_plan">
          <div class="inHead-span">
            <h2 class="head-20Med">Complete Your Subscription</h2>
          </div>
          
          <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
              <div class="card shadow-lg">
                <div class="card-body p-4">
                  
                  <div class="subscription-summary mb-4">
                    <h5 class="mb-3">Subscription Summary</h5>
                    <div class="d-flex justify-content-between mb-2">
                      <span>Plan:</span>
                      <strong>{{ $plan->name }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                      <span>Price:</span>
                      <strong>${{ number_format($plan->price, 2) }}/{{ $plan->billing_period }}</strong>
                    </div>
                  </div>

                  <form id="subscribe-form" action="{{ route('pay') }}" method="POST">
                    @csrf
                    <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                    
                    <div class="form-group mb-4">
                      <label class="form-label mb-2">Card Information</label>
                      <div id="card-element" class="card-element">
                        <!-- Stripe Element will be inserted here -->
                      </div>
                      <div id="card-errors" class="error-message" role="alert"></div>
                    </div>

                    <div class="payment-status" id="payment-status"></div>

                    <button type="submit" class="btn btn-primary w-100" id="submit-button">
                      <span id="button-text">Complete Subscription</span>
                      <span id="spinner" class="spinner-border spinner-border-sm ms-2 d-none" role="status"></span>
                    </button>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="https://js.stripe.com/v3/"></script>
<script>
  // Initialize Stripe
  const stripe = Stripe('{{ config('services.stripe.key') }}');
  const elements = stripe.elements();

  // Create card Element
  const cardElement = elements.create('card', {
    style: {
      base: {
        fontSize: '16px',
        color: '#32325d',
        fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
        fontSmoothing: 'antialiased',
        '::placeholder': {
          color: '#aab7c4'
        }
      },
      invalid: {
        color: '#fa755a',
        iconColor: '#fa755a'
      }
    }
  });

  // Mount the card Element
  cardElement.mount('#card-element');

  // Handle form submission
  const form = document.getElementById('subscribe-form');
  const submitButton = document.getElementById('submit-button');
  const spinner = document.getElementById('spinner');
  const buttonText = document.getElementById('button-text');
  const paymentStatus = document.getElementById('payment-status');

  form.addEventListener('submit', async (event) => {
    event.preventDefault();

    // Disable the submit button to prevent multiple submissions
    submitButton.disabled = true;
    spinner.classList.remove('d-none');
    buttonText.textContent = 'Processing...';

    try {
      const { paymentMethod, error } = await stripe.createPaymentMethod({
        type: 'card',
        card: cardElement,
        billing_details: {
          name: '{{ Auth::user()->name }}',
          email: '{{ Auth::user()->email }}'
        }
      });

      if (error) {
        // Handle error
        const errorElement = document.getElementById('card-errors');
        errorElement.textContent = error.message;
        submitButton.disabled = false;
        spinner.classList.add('d-none');
        buttonText.textContent = 'Complete Subscription';
        
        paymentStatus.textContent = 'Payment failed: ' + error.message;
        paymentStatus.className = 'payment-status error';
        paymentStatus.style.display = 'block';
      } else {
        // Create hidden input with payment method ID
        const hiddenInput = document.createElement('input');
        hiddenInput.setAttribute('type', 'hidden');
        hiddenInput.setAttribute('name', 'payment_method');
        hiddenInput.setAttribute('value', paymentMethod.id);
        form.appendChild(hiddenInput);

        // Submit the form
        form.submit();
      }
    } catch (err) {
      console.error('Error:', err);
      submitButton.disabled = false;
      spinner.classList.add('d-none');
      buttonText.textContent = 'Complete Subscription';
      
      paymentStatus.textContent = 'An unexpected error occurred. Please try again.';
      paymentStatus.className = 'payment-status error';
      paymentStatus.style.display = 'block';
    }
  });

  // Handle real-time validation errors
  cardElement.addEventListener('change', ({error}) => {
    const displayError = document.getElementById('card-errors');
    if (error) {
      displayError.textContent = error.message;
    } else {
      displayError.textContent = '';
    }
  });
</script>
@endsection
