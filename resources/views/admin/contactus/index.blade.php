@extends('layouts.main')
@section('content')
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.css">
<div class="container-fluid py-4">
      <div class="row">
        <div class="col-12">
            @if(Session::has('success'))
                <div class="alert alert-success" style="color:white">{{Session::get('success')}}</div>
            @endif
            @if(Session::has('error'))
                <div class="alert alert-danger" style="color:white">{{Session::get('error')}}</div>
            @endif
          <div class="card mb-4">
            <div class="card-header pb-0">
              <h6>Contact Forms table</h6>
             
            </div>
            <div class="card-body px-0 pt-0 pb-2">
              <div class="table-responsive p-0">
                <table class="table align-items-center mb-0" id="myTable">
                  <thead>
                    <tr>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Sr #</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Company Name</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Subject</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Sent Date</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Phone</th>
                      
                      <th class="text-secondary opacity-7"></th>
                    </tr>
                  </thead>
                  <tbody>
                    @if(count($forms)>0)
                    @foreach($forms as $form)
                    <tr>
                      <td>{{$loop->iteration}}</td>
                      <td>
                        <div class="d-flex px-2 py-1">
                          
                          <div class="d-flex flex-column justify-content-center">
                            <h6 class="mb-0 text-sm">{{$form->company->name}}</h6>
                            <p class="text-xs text-secondary mb-0"></p>
                          </div>
                        </div>
                      </td>
                      <td class="text-center">
                        <p class="text-xs font-weight-bold mb-0">{{$form->subject}}</p>
                       
                      </td>
                      <td class="text-center">
                        <p class="text-xs font-weight-bold mb-0">{{$form->created_at->format('Y-m-d')}}</p>
                       
                      </td>
                      <td class="text-center">
                        <p class="text-xs font-weight-bold mb-0">{{$form->phone}}</p>
                       
                      </td>
                      
                      <td class="align-middle">
                        <a href="{{route('contactform.detail',$form->id)}}" class="btn btn-sm btn-primary" data-toggle="tooltip" data-original-title="Edit user">
                          Read
                        </a>
                       
                        <a href="{{route('contactform.delete',$form->id)}}"  class="btn btn-sm btn-danger" >
                          Delete
                        </a>
                      </td>
                    </tr>
                    
                   @endforeach
                   @else
                   <tr>
                    <td colspan="5" class="text-center">
                    <p>No records found</p>
                    </td>
                   </tr>
                  
                   @endif
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
      
    
    </div>
  @endsection
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.js"></script>
  <script>
$(document).ready(function() {
    $('#myTable').DataTable();
});
</script>