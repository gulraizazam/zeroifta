@extends('layouts.new_main')
@section('content')
<div class="dashbord-inner">
    <!-- Section 1 -->
    <div class="profileForm-area mb-4">
        <div class="sec1-style">

        <form method="post" action="{{route('vehicle.import')}}" enctype="multipart/form-data">
            @csrf
            <div class="row pt-3">
                <div class="col-lg-12 col-md-12 col-sm-12 col-12 mb-2">
                    <div class="dash-input mb-3">
                        <label class="input-lables pb-2" for="exampleFormControlInput1" class="pb-1">{{__('messages.File (Excel)')}}</label>
                        <input type="file" class="form-control login-input choose-file-input" id="exampleFormControlInput1"  name="file" />
                    </div>
                    @error('file')
                            <span class="invalid-feedback" role="alert" style="display: block;">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                </div>


            </div>
            <div class="buttons mt-5">
            <a href="{{ asset('vehicle.xlsx') }}" download class="btn btn-warning" style="float: right;">{{__('messages.Download Sample File')}}</a>
                <button type="submit"  class="mainBtn">{{__('messages.Submit')}}</a>
            </div>
        </div>
    </div>
</div>

@endsection
