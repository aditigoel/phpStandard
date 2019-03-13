@extends('webmaster')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading denni">Reset Password</div>

                <div class="panel-body">
                  <form action="{{ url('resetPassword/') }}" method="post" class="form-horizontal">
                  {{ csrf_field() }}

                        <input type="hidden" name="forgot_password_token" value="{{ $token }}">


                        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                            <label for="password" class="control-label">Password</label>


                                <input id="password" type="password" class="form-control" name="password" >

                                @if ($errors->has('password'))
                                    <span class="help-block" style="color:red;">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif

                        </div>

                        <div class="form-group{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                            <label for="password-confirm" class=" control-label">Confirm Password</label>

                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" >

                                @if ($errors->has('password_confirmation'))
                                    <span class="help-block" style="color:red;">
                                        <strong>{{ $errors->first('password_confirmation') }}

                                        </strong>
                                    </span>
                                @endif

                        </div>


                        <div class="form-group form_tongy">
                            <div class="text-center">
                                <button type="submit" class="btn btn-primary">
                                    Reset Password
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
