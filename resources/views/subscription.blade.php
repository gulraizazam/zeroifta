@extends('layouts.new_main')
@section('content')
      <div class="user-subsPlnas">
        <div class="usp_inn">
          <div class="manage-comp mb-4">
            <div class="Filters-main mb-3 mb-md-4">
              <div class="sec1-style">
          <div class="text-center inHead-span mb-4">
            <h2 class="head-title">{{__('messages.Subscription Plans')}}</h2>
            <p class="head-subtitle">Choose the perfect plan for your business needs</p>
                </div>
          
                <div class="plans-toggel">
                  <div class="sub-toggel text-center">
              <div class="toggle-label">
                <p>{{__('messages.Monthly')}}</p>
                    </div>
                    <div>
                      <label class="switch">
                        <input type="checkbox" id="plansSwitchCheckbox">
                        <span class="slider round"></span>
                      </label>
                    </div>
              <div class="toggle-label">
                <p>{{__('messages.Yearly')}}</p>
                @if($yearlyDiscount > 0)
                    <span class="save-badge">{{__('messages.Save')}} {{ $yearlyDiscount }}%</span>
                @endif
                      </div>
                    </div>
                  </div>

          <div class="subs_plan">
            <div class="row justify-content-center">
              @foreach($plans as $plan)
              @if($plan->is_active)
              <div class="col-lg-4 col-md-6 mb-4 plan-period plan-{{ $plan->billing_period ?: 'one-time' }}" 
                   data-period="{{ $plan->billing_period ?: 'one-time' }}"
                   style="{{ $plan->billing_period == 'year' ? 'display: none;' : '' }}">
                <div class="plan-box h-100 {{ $plan->is_featured ? 'featured-plan' : '' }}">
                  @if($plan->is_featured)
                    <div class="featured-badge">
                      <span>{{__('messages.Featured')}}</span>
                          </div>
                  @endif
                  
                  <div class="pb-head">
                    <h3>{{ $plan->name }}</h3>
                    <div class="price">
                      <h4>${{ number_format($plan->price, 2) }}<span>/{{ $plan->billing_period == 'year' ? __('messages.Yearly') : __('messages.Monthly') }}</span></h4>
                            </div>
                    <button type="button" class="view-details-btn" data-bs-toggle="modal" data-bs-target="#planModal{{ $plan->id }}">
                      <i class="fas fa-info-circle me-1"></i> View Details
                    </button>
                            </div>

                  <div class="pb-body">
                    <h5 class="features-title">{{__('messages.Included Features:')}}</h5>
                    <ul class="features-list">
                      @foreach($availableFeatures as $key => $label)
                        <li class="{{ is_array($plan->features) && in_array($key, $plan->features) ? 'feature-included' : 'feature-excluded' }}">
                          @if(is_array($plan->features) && in_array($key, $plan->features))
                            <span class="feature-check">
                              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="18" height="18">
                                <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z" fill="currentColor"/>
                                      </svg>
                                    </span>
                          @else
                            <span class="feature-cross">
                              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="18" height="18">
                                <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12 19 6.41z" fill="currentColor"/>
                                      </svg>
                                    </span>
                          @endif
                          {{ __('messages.' . $label) }}
                                  </li>
                      @endforeach
                                </ul>
                              </div>

                  <div class="pb-footer mt-auto">
                    @if(auth()->check())
                      @if(auth()->user()->subscription && auth()->user()->subscription->plan_id == $plan->id)
                        <button class="btn btn-secondary w-100" disabled>
                          <i class="fas fa-check-circle me-2"></i>{{__('messages.Current Plan')}}
                        </button>
                      @else
                        <a href="{{ route('purchase', $plan->id) }}" class="mainBtn w-100 {{ $plan->name === 'Premium' ? 'featured-btn' : '' }}">
                          {{__('messages.Purchase')}}
                          <i class="fas fa-arrow-right ms-2"></i>
                        </a>
                      @endif
                    @else
                      <a href="{{ route('login') }}" class="mainBtn w-100">
                        {{__('messages.Login to Purchase')}}
                        <i class="fas fa-sign-in-alt ms-2"></i>
                      </a>
                    @endif
                            </div>
                          </div>
                        </div>
              @endif
              @endforeach
                      </div>
                          </div>
                          </div>
                                    </div>
                                    </div>
                                          </div>
                                          </div>

@foreach($plans as $plan)
<!-- Plan Details Modal -->
<div class="modal fade" id="planModal{{ $plan->id }}" tabindex="-1" aria-labelledby="planModalLabel{{ $plan->id }}" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="planModalLabel{{ $plan->id }}">
          <span class="plan-badge {{ $plan->name === 'Premium' ? 'premium' : 'standard' }}">{{ $plan->name }}</span>
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
      <div class="modal-body">
        <div class="row">
          <!-- Left Column -->
          <div class="col-md-4 plan-info-column">
            <div class="price-section text-center mb-4">
              <div class="price-tag">
                <span class="currency">$</span>
                <span class="amount">{{ number_format($plan->price, 2) }}</span>
                <span class="period">/{{ $plan->billing_period == 'year' ? __('messages.Yearly') : __('messages.Monthly') }}</span>
                                    </div>
              @if($plan->billing_period == 'year')
                <div class="savings-badge">
                  <i class="fas fa-piggy-bank"></i> Save 20% annually
                                    </div>
              @endif
                                    </div>

            <div class="plan-highlights">
              <h6 class="section-title"><i class="fas fa-star"></i> Plan Highlights</h6>
              <div class="highlights-content">
                {{ $plan->description }}
                              </div>
                            </div>

            <div class="plan-action mt-4">
              @if(auth()->check())
                @if(auth()->user()->subscription && auth()->user()->subscription->plan_id == $plan->id)
                  <button class="btn btn-secondary btn-lg w-100" disabled>
                    <i class="fas fa-check-circle me-2"></i>{{__('messages.Current Plan')}}
                  </button>
                @else
                  <a href="{{ route('purchase', $plan->id) }}" 
                     class="btn btn-primary btn-lg w-100 {{ $plan->name === 'Premium' ? 'premium-btn' : '' }}">
                    {{__('messages.Purchase Now')}}
                    <i class="fas fa-arrow-right ms-2"></i>
                  </a>
                @endif
              @else
                <a href="{{ route('login') }}" class="btn btn-primary btn-lg w-100">
                  {{__('messages.Login to Purchase')}}
                  <i class="fas fa-sign-in-alt ms-2"></i>
                </a>
              @endif
                            </div>
                          </div>

          <!-- Right Column -->
          <div class="col-md-8 features-column">
            <div class="features-grid">
              <h6 class="section-title mb-4">
                <i class="fas fa-cubes"></i> {{__('messages.Features & Capabilities')}}
              </h6>
              <div class="row">
                @foreach($availableFeatures as $key => $label)
                  <div class="col-md-6 feature-item {{ is_array($plan->features) && in_array($key, $plan->features) ? 'included' : 'excluded' }}">
                    <div class="feature-card">
                      @if(is_array($plan->features) && in_array($key, $plan->features))
                        <span class="feature-icon included">
                          <i class="fas fa-check-circle"></i>
                                    </span>
                      @else
                        <span class="feature-icon excluded">
                          <i class="fas fa-times-circle"></i>
                                    </span>
                      @endif
                      <div class="feature-text">
                        <h6>{{ __('messages.' . $label) }}</h6>
                        <p class="feature-description">
                          {{ __('messages.' . $label . '_description') }}
                                      </p>
                                    </div>
                                    </div>
                              </div>
                @endforeach
                            </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                    </div>
@endforeach

@push('styles')
<style>
.head-title {
    font-size: 2.5rem;
    font-weight: 700;
    color: var(--h1color);
    margin-bottom: 1rem;
}

.head-subtitle {
    font-size: 1.1rem;
    color: var(--gray1);
}

.plan-box {
    background: var(--card-bg-color);
    border-radius: 15px;
    padding: 2.5rem;
    transition: all 0.3s ease;
    box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    display: flex;
    flex-direction: column;
    position: relative;
    overflow: hidden;
    border: 1px solid rgba(0,0,0,0.05);
}

.featured-plan {
    display: block !important;
    z-index: 1;
    transform: scale(1.05);
    border: 2px solid #0c388b;
    box-shadow: 0 8px 25px rgba(12, 56, 139, 0.15);
}

.featured-badge {
    position: absolute;
    top: 20px;
    right: -35px;
    background: #0c388b;
    color: white;
    padding: 8px 40px;
    transform: rotate(45deg);
    font-size: 0.9rem;
    font-weight: 500;
}

.plan-box:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
}

.featured-plan:hover {
    transform: scale(1.05) translateY(-5px);
}

.pb-head {
    text-align: center;
    padding-bottom: 2rem;
    border-bottom: 1px solid var(--border-1);
}

.pb-head h3 {
    color: var(--h1color);
    margin-bottom: 1rem;
    font-size: 1.8rem;
    font-weight: 700;
}

.plan-description {
    color: var(--gray1);
    font-size: 0.95rem;
    margin-top: 1rem;
}

.price h4 {
    font-size: 3rem;
    color: var(--primaryColor);
    margin-bottom: 0;
    font-weight: 700;
}

.price span {
    font-size: 1.1rem;
    color: var(--gray1);
}

.pb-body {
    padding: 2rem 0;
    flex-grow: 1;
}

.features-title {
    color: var(--h1color);
    font-size: 1.1rem;
    margin-bottom: 1.5rem;
    font-weight: 600;
}

.features-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.features-list li {
    margin-bottom: 1rem;
    color: var(--grayWhite);
    display: flex;
    align-items: center;
    gap: 0.8rem;
    font-size: 1rem;
    padding: 0.5rem 0;
}

.feature-included {
    opacity: 1;
}

.feature-excluded {
    opacity: 0.6;
}

.feature-check {
    color: #0c388b;
    display: flex;
    align-items: center;
}

.feature-cross {
    color: #dc3545;
    display: flex;
    align-items: center;
}

.dark-mode .feature-check {
    color: #fff;
}

.dark-mode .feature-cross {
    color: #ff4d4d;
}

.pb-footer {
    padding-top: 2rem;
    border-top: 1px solid var(--border-1);
}

.mainBtn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 1rem;
    font-weight: 600;
    transition: all 0.3s ease;
}

.featured-btn {
    background: #0c388b;
    color: white;
}

.featured-btn:hover {
    background: #092e75;
    transform: translateY(-2px);
}

/* Toggle Switch Styles */
.plans-toggel {
    margin: 2rem 0 4rem 0;
}

.sub-toggel {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 1.5rem;
}

.toggle-label {
    position: relative;
}

.toggle-label p {
    font-size: 1.1rem;
    color: var(--grayWhite);
    margin: 0;
    font-weight: 500;
}

.save-badge {
    display: none;
    position: absolute;
    top: -20px;
    right: -20px;
    background: #28a745;
    color: white;
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 0.8rem;
    font-weight: 500;
    animation: fadeIn 0.3s ease-in;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

/* Responsive Styles */
@media (max-width: 768px) {
    .head-title {
        font-size: 2rem;
    }

    .plan-box {
        padding: 1.5rem;
    }

    .featured-plan {
        transform: scale(1);
    }

    .featured-plan:hover {
        transform: translateY(-5px);
    }

    .pb-head h3 {
        font-size: 1.5rem;
    }

    .price h4 {
        font-size: 2.5rem;
    }
}

.view-details-btn {
    background: none;
    border: none;
    color: var(--primaryColor);
    font-size: 0.9rem;
    padding: 0.5rem;
    margin-top: 1rem;
    cursor: pointer;
    transition: all 0.3s ease;
}

.view-details-btn:hover {
    color: var(--blueColor);
    text-decoration: underline;
}

.modal-content {
    background: var(--card-bg-color);
    border: none;
    border-radius: 15px;
}

.modal-header {
    border-bottom: 1px solid var(--border-1);
    padding: 1.5rem;
}

.modal-header .modal-title {
    color: var(--h1color);
    font-size: 1.5rem;
    font-weight: 600;
}

.modal-body {
    padding: 1.5rem;
}

.modal-footer {
    border-top: 1px solid var(--border-1);
    padding: 1.5rem;
}

.price-section {
    text-align: center;
}

.modal-price {
    font-size: 2.5rem;
    color: var(--primaryColor);
    font-weight: 700;
}

.modal-price span {
    font-size: 1rem;
    color: var(--gray1);
}

.section-title {
    color: var(--h1color);
    font-size: 1.1rem;
    font-weight: 600;
    margin-bottom: 1rem;
}

.plan-full-description {
    color: var(--grayWhite);
    font-size: 1rem;
    line-height: 1.6;
}

.modal-features-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.modal-features-list li {
    margin-bottom: 0.75rem;
    color: var(--grayWhite);
    display: flex;
    align-items: center;
    gap: 0.8rem;
    font-size: 0.95rem;
}

.dark-mode .modal-content {
    background: var(--HeadBg);
}

.dark-mode .modal-header,
.dark-mode .modal-footer {
    border-color: rgba(255, 255, 255, 0.1);
}

@media (max-width: 768px) {
    .modal-dialog {
        margin: 1rem;
    }
    
    .modal-price {
        font-size: 2rem;
    }
}

/* Modal Styles */
.modal-xl {
    max-width: 80vw;
}

.modal-content {
    background: var(--card-bg-color);
    border: none;
    border-radius: 20px;
    overflow: hidden;
}

.modal-header {
    background: linear-gradient(45deg, var(--primaryColor), #1a4ca3);
    padding: 1.5rem 2rem;
    border: none;
}

.modal-title {
    color: white;
    font-size: 1.8rem;
    font-weight: 600;
}

.plan-badge {
    padding: 0.5rem 1.5rem;
    border-radius: 50px;
    font-size: 1.2rem;
}

.plan-badge.premium {
    background: rgba(255, 255, 255, 0.2);
    border: 1px solid rgba(255, 255, 255, 0.4);
}

.modal-body {
    padding: 2rem;
}

/* Price Section */
.plan-info-column {
    border-right: 1px solid var(--border-1);
    padding: 2rem;
}

.price-tag {
    margin-bottom: 1rem;
}

.price-tag .currency {
    font-size: 2rem;
    vertical-align: top;
    color: var(--primaryColor);
}

.price-tag .amount {
    font-size: 4rem;
    font-weight: 700;
    color: var(--primaryColor);
    line-height: 1;
}

.price-tag .period {
    font-size: 1.2rem;
    color: var(--gray1);
}

.savings-badge {
    display: inline-block;
    background: #28a745;
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 50px;
    font-size: 0.9rem;
    margin-top: 1rem;
}

/* Features Grid */
.features-column {
    padding: 2rem;
}

.feature-card {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    padding: 1.5rem;
    border-radius: 10px;
    background: var(--card-bg-color);
    margin-bottom: 1rem;
    border: 1px solid var(--border-1);
    transition: all 0.3s ease;
}

.feature-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.feature-icon {
    font-size: 1.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    border-radius: 50%;
}

.feature-icon.included {
    color: #28a745;
    background: rgba(40, 167, 69, 0.1);
}

.feature-icon.excluded {
    color: #dc3545;
    background: rgba(220, 53, 69, 0.1);
}

.feature-text h6 {
    color: var(--h1color);
    margin-bottom: 0.5rem;
    font-weight: 600;
}

.feature-description {
    color: var(--gray1);
    font-size: 0.9rem;
    margin: 0;
}

.section-title {
    color: var(--h1color);
    font-size: 1.3rem;
    font-weight: 600;
    margin-bottom: 2rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.section-title i {
    color: var(--primaryColor);
}

.plan-highlights {
    background: var(--card-bg-color);
    padding: 1.5rem;
    border-radius: 10px;
    border: 1px solid var(--border-1);
}

.highlights-content {
    color: var(--grayWhite);
    font-size: 1rem;
    line-height: 1.6;
}

.premium-btn {
    background: linear-gradient(45deg, var(--primaryColor), #1a4ca3);
    border: none;
}

.premium-btn:hover {
    background: linear-gradient(45deg, #1a4ca3, var(--primaryColor));
    transform: translateY(-2px);
}

/* Dark Mode Adjustments */
.dark-mode .modal-content {
    background: var(--HeadBg);
}

.dark-mode .feature-card {
    background: rgba(255, 255, 255, 0.05);
    border-color: rgba(255, 255, 255, 0.1);
}

/* Responsive Adjustments */
@media (max-width: 992px) {
    .modal-xl {
        max-width: 95vw;
    }

    .plan-info-column {
        border-right: none;
        border-bottom: 1px solid var(--border-1);
        margin-bottom: 2rem;
    }
}

@media (max-width: 768px) {
    .feature-item {
        width: 100%;
    }
}

/* Update plan transition */
.plan-period {
    transition: all 0.3s ease-in-out;
}

.save-badge {
    display: none;
    transition: all 0.3s ease-in-out;
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    function togglePlans(showYearly) {
        if (showYearly) {
            // Hide monthly plans but keep featured ones visible
            $('.plan-month').not('.featured-plan').hide();
            $('.plan-year').fadeIn();
            $('.save-badge').fadeIn();
    } else {
            // Hide yearly plans but keep featured ones visible
            $('.plan-year').not('.featured-plan').hide();
            $('.plan-month').fadeIn();
            $('.save-badge').fadeOut();
        }
    }

    // Handle plan period toggle
    $('#plansSwitchCheckbox').on('change', function() {
        const isYearly = $(this).prop('checked');
        togglePlans(isYearly);
        localStorage.setItem('preferred_billing_period', isYearly ? 'year' : 'month');
    });

    // Set initial state based on stored preference or default to monthly
    const preferredPeriod = localStorage.getItem('preferred_billing_period') || 'month';
    $('#plansSwitchCheckbox').prop('checked', preferredPeriod === 'year');
    togglePlans(preferredPeriod === 'year');
  });
</script>
@endpush

@endsection
