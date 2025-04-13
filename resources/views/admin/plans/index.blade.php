@extends('layouts.new_main')
@section('content')
<div class="dashbord-inner">
  @if(Session::has('success'))
  <div class="alert alert-success alert-dismissible fade show" role="alert" style="background-color: #13975b;color:white">
    {{Session::get('success')}}
    <!-- <button type="button" class="close" data-bs-dismiss="alert" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button> -->
  </div>
  @endif
  @if(Session::has('error'))
  <div class="alert alert-danger alert-dismissible fade show" role="alert" style="background-color: #dd4957;color:white">
    {{Session::get('error')}}
    <!-- <button type="button" class="close" data-bs-dismiss="alert" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button> -->
  </div>
  @endif

  <div class="manage-comp mb-4">
    <div class="Filters-main mb-3 mb-md-4">
      <div class="sec1-style">
        <div class="row mb-4">
          <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
              <h2 class="head-20Med">{{__('messages.Manage Subscription Plans')}}</h2>
              <a href="{{route('plans.create')}}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>{{__('messages.Add New Plan')}}
              </a>
            </div>
          </div>
        </div>

        <div class="card">
          <div class="card-body">
            <div class="table-responsive">
              <table id="subscriptionPlans" class="table align-middle">
                <thead>
                  <tr>
                    <th>{{__('messages.Plan Name')}}</th>
                    <th>{{__('messages.Price')}}</th>
                    <th>{{__('messages.Billing Period')}}</th>
                    <th>{{__('messages.Status')}}</th>
                    <th>{{__('messages.Featured')}}</th>
                    <th>{{__('messages.Subscribers')}}</th>
                    <th>{{__('messages.Actions')}}</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($plans as $plan)
                  <tr>
                    <td>
                      <div class="d-flex align-items-center">
                        <div class="plan-icon me-3">
                          @if($plan->slug == 'free')
                            <i class="fas fa-gift text-success"></i>
                          @else
                            <i class="fas fa-crown text-warning"></i>
                          @endif
                        </div>
                        <div>
                          <h6 class="mb-0">{{$plan->name}}</h6>
                          <small class="text-muted">{{Str::limit($plan->description, 50)}}</small>
                        </div>
                      </div>
                    </td>
                    <td>
                      <div class="price-badge">
                        <span class="badge bg-light text-dark">
                          ${{number_format($plan->price, 2)}}
                        </span>
                      </div>
                    </td>
                    <td>
                      <span class="badge bg-info">
                        {{ucfirst($plan->billing_period ?? 'One-time')}}
                      </span>
                    </td>
                    <td>
                      <div class="form-check form-switch">
                        <input class="form-check-input status-toggle" type="checkbox" 
                               data-plan-id="{{ $plan->id }}"
                               {{ $plan->is_active ? 'checked' : '' }}>
                      </div>
                    </td>
                    <td>
                      <div class="form-check form-switch">
                        <input class="form-check-input featured-toggle" 
                               type="checkbox" 
                               data-plan-id="{{ $plan->id }}"
                               {{ $plan->is_featured ? 'checked' : '' }}>
                        @if($plan->is_featured)
                          <span class="featured-badge">
                            <i class="fas fa-star"></i>
                          </span>
                        @endif
                      </div>
                    </td>
                    <td>
                      <span class="badge bg-primary">
                        {{\App\Models\Subscription::where('plan_id', $plan->id)->where('status', 'active')->count()}}
                      </span>
                    </td>
                    <td>
                      <div class="btn-group">
                        <a href="{{route('plans.edit', $plan->id)}}" 
                           class="btn btn-sm btn-outline-primary">
                          <i class="fas fa-edit"></i>
                        </a>
                        <button type="button" 
                                class="btn btn-sm btn-outline-danger"
                                onclick="confirmDelete({{$plan->id}})">
                          <i class="fas fa-trash"></i>
                        </button>
                      </div>
                    </td>
                  </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">{{__('messages.Confirm Delete')}}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        {{__('messages.Are you sure you want to delete this plan?')}}
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
          {{__('messages.Cancel')}}
        </button>
        <form id="deletePlanForm" method="POST">
          @csrf
          @method('DELETE')
          <input type="hidden" name="handle_payments" value="keep">
          <button type="submit" class="btn btn-danger">
            {{__('messages.Delete')}}
          </button>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Payment Records Handling Modal -->
<div class="modal fade" id="paymentRecordsModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">{{__('messages.Payment Records Found')}}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p>{{__('messages.This plan has associated payment records. How would you like to proceed?')}}</p>
        <div class="alert alert-warning">
          <i class="fas fa-exclamation-triangle me-2"></i>
          {{__('messages.Deleting payment records cannot be undone.')}}
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
          {{__('messages.Cancel')}}
        </button>
        <button type="button" class="btn btn-warning" onclick="proceedWithDeletion('keep')">
          {{__('messages.Keep Payment Records')}}
        </button>
        <button type="button" class="btn btn-danger" onclick="proceedWithDeletion('delete')">
          {{__('messages.Delete Payment Records')}}
        </button>
      </div>
    </div>
  </div>
</div>

@push('styles')
<style>
.plan-icon i {
  font-size: 1.5rem;
}

.price-badge .badge {
  font-size: 0.9rem;
  padding: 0.5rem 0.75rem;
}

.table > :not(caption) > * > * {
  padding: 1rem 0.75rem;
}

.btn-group .btn {
  padding: 0.25rem 0.5rem;
}

.form-switch .form-check-input {
  width: 2.5em;
}

.featured-badge {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  margin-left: 0.5rem;
  color: #ffc107;
  animation: pulse 2s infinite;
}

@keyframes pulse {
  0% { transform: scale(1); }
  50% { transform: scale(1.2); }
  100% { transform: scale(1); }
}

.form-switch .featured-toggle:checked {
  background-color: #ffc107;
  border-color: #ffc107;
}

@media (max-width: 768px) {
  .table-responsive {
    border: 0;
  }
  
  .table thead {
    display: none;
  }
  
  .table tbody tr {
    display: block;
    margin-bottom: 1rem;
    border: 1px solid #dee2e6;
    border-radius: 0.25rem;
  }
  
  .table tbody td {
    display: block;
    text-align: right;
    padding: 0.75rem;
    border: none;
  }
  
  .table tbody td::before {
    content: attr(data-label);
    float: left;
    font-weight: bold;
  }
  
  .btn-group {
    justify-content: flex-end;
  }
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize DataTable
    $('#subscriptionPlans').DataTable({
        responsive: true,
        order: [[0, 'asc']],
        language: {
            search: "{{__('messages.Search')}}: ",
            lengthMenu: "{{__('messages.Show')}} _MENU_ {{__('messages.entries')}}",
        }
    });

    // Handle status toggle
    $('.status-toggle').on('change', function() {
        const planId = $(this).attr('data-plan-id');
        const $statusSwitch = $(this);
        
        $.ajax({
            url: "{{ url('plans') }}/" + planId + "/toggle-status",
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    $statusSwitch.prop('checked', response.is_active);
                } else {
                    toastr.error(response.message);
                    $statusSwitch.prop('checked', !$statusSwitch.prop('checked'));
                }
            },
            error: function(xhr) {
                toastr.error("{{__('messages.Failed to update plan status')}}");
                $statusSwitch.prop('checked', !$statusSwitch.prop('checked'));
            }
        });
    });

    // Handle featured toggle separately
    $('.featured-toggle').on('change', function() {
        const planId = $(this).attr('data-plan-id');
        const $featuredSwitch = $(this);
        const $badgeContainer = $featuredSwitch.closest('td');
        
        $.ajax({
            url: `{{ url(app()->getLocale()) }}/plans/${planId}/toggle-featured`,
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    if (response.is_featured) {
                        // Add featured badge if it doesn't exist
                        if (!$badgeContainer.find('.featured-badge').length) {
                            $badgeContainer.append('<span class="featured-badge"><i class="fas fa-star"></i></span>');
                        }
                    } else {
                        // Remove featured badge
                        $badgeContainer.find('.featured-badge').remove();
                    }
                } else {
                    toastr.error(response.message);
                    $featuredSwitch.prop('checked', !$featuredSwitch.prop('checked'));
                }
            },
            error: function(xhr) {
                toastr.error("{{__('messages.Failed to update plan featured status')}}");
                $featuredSwitch.prop('checked', !$featuredSwitch.prop('checked'));
            }
        });
    });
});

function confirmDelete(planId) {
    // First check if plan has payment records
    $.ajax({
        url: "{{ url('plans') }}/" + planId + "/check-payments",
        type: 'GET',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            $('#deletePlanForm').attr('action', "{{ route('plans.delete', '') }}/" + planId);
            
            if (response.has_payments) {
                $('#paymentRecordsModal').modal('show');
            } else {
                $('#deleteModal').modal('show');
            }
        },
        error: function(xhr) {
            toastr.error("{{__('messages.Failed to check plan status')}}");
        }
    });
}

function proceedWithDeletion(handlePayments) {
    $('#paymentRecordsModal').modal('hide');
    $('input[name="handle_payments"]').val(handlePayments);
    $('#deleteModal').modal('show');
}

// Initialize toastr options
toastr.options = {
    "closeButton": true,
    "progressBar": true,
    "positionClass": "toast-top-right",
    "timeOut": "3000"
};
</script>
@endpush

@endsection
