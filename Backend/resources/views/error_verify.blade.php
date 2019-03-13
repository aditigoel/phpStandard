@extends('webmaster')

@section('content')
<div class="container">
    <div class="row">


        <div class="col-md-12">


        <div class="errorWrap">
    <div class="header">
      <div class="icon-crossmmark iconsRight">
       <img src="{{ asset('images/cross-circular.png') }}">
      </div>
      <h2>Oops!<small>Verification link expires</small></h2>
    </div>
    <div class="th-body">
      <p> Verification link expired. Please, resend link from the application to get a new verification link.</p>
    </div>
  </div>

         </div>
    </div>
</div>
@endsection
