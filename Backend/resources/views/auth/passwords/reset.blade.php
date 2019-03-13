

@extends('retailer.layouts.app_open')

@section('content')
<div class="wp-wrapper innerPage _login">
    <div class="main">
        <div class="banner">
            <div class="row">
                <div class="col-xs-12 col-md-5 col-lg-4">
                    <div class="bannerText loginText">
                    <h2>Reset Password</h2>
                        <div class="form-group emailText">
                            <p>Enter your details below</p>
                        </div>
                        @if (session('status'))
          <div class="alert alert-success margin-top-4x">
              {{ session('status') }}
          </div>
      @endif
      <form class="form-horizontal" method="POST" action="{{ route('password.request') }}">
                 {{ csrf_field() }}
                 <input type="hidden" name="token" value="{{ $token }}">
                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                              <input type="email" class="form-control" value= "{{ old('email') }}" name="email" placeholder="Email Address">
                              @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong class="text-danger">{!! $errors->first('email') !!}</strong>
                                    </span>
                                @endif
                               
                                    
                        </div>

                        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                        <input class="form-control{{ $errors->has('password') ? ' has-error' : '' }}" type="password" placeholder="New Password"  name="password">
                               @if ($errors->has('password'))
                                   <span class="help-block">
                                       <strong class="text-danger">{{ $errors->first('password') }}</strong>
                                   </span>
                               @endif
                               
                                    
                        </div>


                        <div class="form-group{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                        <input class="form-control{{ $errors->has('password_confirmation') ? ' has-error' : '' }}" type="password" placeholder="Confirm Password"  name="password_confirmation">
                   @if ($errors->has('password_confirmation'))
                                   <span class="help-block">
                                       <strong class="text-danger">{{ $errors->first('password_confirmation') }}</strong>
                                   </span>
                               @endif
                               
                                    
                        </div>

                        <button class="button button-uppercase button-block button-font-medium button-height-large button-font-medium-txt button-rounded margin-bottom-3x signin" type="submit">Reset Password</button>
                       
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


