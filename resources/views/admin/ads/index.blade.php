@extends('layouts.main')
@section('content')
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
              <h6>Ads table</h6>
              <!-- <a href="{{route('users.create')}}" class="btn btn-primary" style="float:right">Add User</a> -->
            </div>
            <div class="card-body px-0 pt-0 pb-2">
              <div class="table-responsive p-0">
                <table class="table align-items-center mb-0">
                  <thead>
                    <tr>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Ad Name</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">User</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Image</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Created Date</th>
                      <th class="text-secondary opacity-7"></th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($ads as $ad)
                    <tr>
                      <td>
                        <div class="d-flex px-2 py-1">
                          <div>
                            <img src="{{asset('assets/img/team-2.jpg')}}" class="avatar avatar-sm me-3" alt="user1">
                          </div>
                          <div class="d-flex flex-column justify-content-center">
                            <h6 class="mb-0 text-sm">{{$ad->name}}</h6>
                            <p class="text-xs text-secondary mb-0"></p>
                          </div>
                        </div>
                      </td>
                      <td>
                        <p class="text-xs font-weight-bold mb-0">{{$ad->user->name ?? 'N/A'}}</p>
                       
                      </td>
                      <td>
                        <img src="{{$ad->images[0]->image}}" style="height: 100px;">
                       
                      </td>
                      <td class="align-middle text-center text-sm">
                        @if($ad->status==1)
                        <span class="badge badge-sm bg-gradient-success">Approved</span>
                        @else
                        <span class="badge badge-sm bg-gradient-danger">Disapproved</span>
                        @endif
                      </td>
                      <td class="align-middle text-center">
                        <span class="text-secondary text-xs font-weight-bold">{{$ad->created_at->format('Y-m-d')}}</span>
                      </td>
                      <td class="align-middle">
                        <a href="{{route('ads.view',$ad->id)}}" class="btn btn-sm btn-primary" data-toggle="tooltip" data-original-title="Edit user">
                          View
                        </a>
                        @if($ad->status==1)
                          <a href="{{route('ads.disapprove',$ad->id)}}" class="btn btn-sm btn-success" data-toggle="tooltip" data-original-title="Edit user">
                          Disapprove
                          </a>
                        @else
                        <a href="{{route('ads.approve',$ad->id)}}" class="btn btn-sm btn-success" data-toggle="tooltip" data-original-title="Edit user">
                            Approve
                          </a>
                        @endif
                        <a href="{{route('ads.delete',$ad->id)}}"  class="btn btn-sm btn-danger" >
                          Delete
                        </a>
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
  @endsection