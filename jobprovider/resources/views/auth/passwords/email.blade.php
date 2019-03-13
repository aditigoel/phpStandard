


@extends('retailer.layouts.app_open')

@section('content')
<div class="wp-wrapper innerPage _login">
    <div class="main">
        <div class="banner">
            <div class="row">
                <div class="col-xs-12 col-md-5 col-lg-4">
                    <div class="bannerText loginText">
                    <h3>Forgot Your Password ?</h3>
        <p>In order to receive your access code - please enter the email address you provided during  the registration process.</p>
        @if (session('status'))
          <div class="alert alert-success margin-top-4x">
              {{ session('status') }}
          </div>
      @endif
      <form class="form-horizontal" method="POST" action="{{ route('password.email') }}">
          {{ csrf_field() }}
  
                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                              <input type="email" class="form-control" value= "{{ old('email') }}" name="email" placeholder="Email Address">
                              @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong  class="text-danger">{!! $errors->first('email') !!}</strong>
                                    </span>
                                @endif
                               
                                    
                        </div>
                        <button class="button button-uppercase button-block button-font-medium button-height-large button-font-medium-txt button-rounded margin-bottom-3x signin" type="submit">SUBMIT</button>
                       
                        <div class="form-group">
                            <div class="signinBtm">
                                <p>Don't have an account? <span class="greenTxt"> <a href="signUp.html"> Sign Up </a> </span> </p>
                            </div>
                        </div>
                    </div>
</form>
                </div>
                <div class="col-xs-12 col-md-7 col-lg-8">
                <img src="{{ URL::asset('retailer/src/assets/images/banner.png') }}" class="img-fluid login_img">
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

