@extends('layouts.new_main')

@section('content')
<style>
.success-icon {
    color: #28a745;
    margin-bottom: 2rem;
}
.subscription-details {
    background: #f8f9fa;
    border-radius: 12px;
    padding: 2rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    margin: 2rem auto;
    max-width: 600px;
}
.detail-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem 0;
    border-bottom: 1px solid #e9ecef;
}
.detail-row:last-child {
    border-bottom: none;
}
.status-badge {
    background: #28a745;
    color: white;
    padding: 0.25rem 0.75rem;
    border-radius: 50px;
    font-size: 0.875rem;
}
</style>

<div class="dashbord-inner">
  <div class="manage-comp mb-4">
    <div class="Filters-main mb-3 mb-md-4">
      <div class="sec1-style">
        <div class="subs_plan">
          <div class="text-center">
            <div class="success-icon">
              <i class="fas fa-check-circle" style="font-size: 64px;"></i>
            </div>
            
            <h2 class="head-20Med mb-3">Subscription Successful!</h2>
            <p class="text-muted mb-4">Thank you for subscribing. Your subscription has been activated successfully.</p>
            
            <div class="subscription-details">
              <h5 class="mb-4">Subscription Details</h5>
              <div class="detail-row">
                <span class="label">Plan:</span>
                <strong>{{ session('plan_name') }}</strong>
              </div>
              <div class="detail-row">
                <span class="label">Status:</span>
                <span class="status-badge">
                  <i class="fas fa-check-circle me-1"></i>
                  Active
                </span>
              </div>
              <div class="detail-row">
                <span class="label">Next Billing Date:</span>
                <strong>{{ session('next_billing_date') }}</strong>
              </div>
            </div>
            
            <div class="mt-4">
              <a href="{{ route('dashboard') }}" class="btn btn-primary">
                <i class="fas fa-home me-2"></i>Go to Dashboard
              </a>
              <!-- <a href="{{ route('subscription.manage') }}" class="btn btn-outline-primary ms-2">
                <i class="fas fa-cog me-2"></i>Manage Subscription
              </a> -->
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection 