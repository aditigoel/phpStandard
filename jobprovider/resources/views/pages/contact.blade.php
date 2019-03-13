@extends('index')
@section('content')
<div class="pageWrapper">
    <div class="container">
        @if (Session::has('flash_message'))
        <div class="alert alert-{!! Session::get('flash_level') !!}">
            {!! Session::get('flash_message') !!}
        </div>
        @endif
        @if(session()->has('message.level'))
        <div class="alert alert-{{ session('message.level') }}"> 
            {!! session('message.content') !!}
        </div>
        @endif 
        <div class="pageWrapper-content">
            <div class="page-title">
                <h2>
                    <span>Contact Us</span>
                </h2>
            </div>

            <div class="contact-section">
                <form class="form-horizontal" method="POST" name="contactForm" onsubmit="return contactUsForm()" action="{{ route('page.send-contact') }}">
                    <script src='https://www.google.com/recaptcha/api.js'></script>
                    {{ csrf_field() }}
                    <div class="form-block">
                        <div class="field-group">
                            <div class="from-box">
                                <div class="_fieldGroup">
                                <div class="row">
                                <div class="col-md-6">
                               
                                    <div class="form-group m-md">
                                        <label>Name</label>
                                        <input type="text" class="form-control" id="name" placeholder="Enter Name" name="name" autofocus=""/>
                                        <span id="name_error" style="color: red;"></span>
                                        @if ($errors->has('name'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('name') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                    <div class="form-group m-md">
                                        <label>Email</label>
                                        <input type="text" id="email" name="email" class="form-control" placeholder="Enter Email" autofocus="" />
                                        <span id="email_error" style="color: red;"></span>
                                        @if ($errors->has('email'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('email') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                    <div class="form-group m-md">
                                        <label>Subject</label>
                                        <select id="subject" name="subject" class="form-control">
                                            <option value="">Select your subject</option>
                                           
                                            <option value="Advertising">Advertising</option>
                                            <option value="Our courses">Our courses</option>
                                            <option value="Access Request">Access Request</option>
                                            <option value="Terms & Policy">Terms & Policy</option>
                                            <option value="General Queries">General Queries</option>
                                            
                                        </select>
                                        <!--<input type="text" id="subject" name="subject" class="form-control" placeholder="Enter Subject" autofocus="" />-->
                                        <span id="subject_error" style="color: red;"></span>
                                        @if ($errors->has('subject'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('subject') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                    <div class="form-group m-md">
                                        <label>Message</label>
                                        <textarea name="message" id="message" class="form-control" minlength="100" maxlength="500" autofocus="" placeholder="Enter Your Message"></textarea>
                                        <span id="message_error" style="color: red;"></span>
                                        @if ($errors->has('message'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('message') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                    <div class="form-group m-md">
                                    <div class="g-recaptcha" data-sitekey="6LefJ0UUAAAAAPi9BsUZmYF-QAfablZiXAb7s6i6"></div>
                                    <span id="captcha_error" style="color: red;"></span>
                                    </div>
                                    <div class="btn-field">
                                        <button class="button button-border button-theme button-min-250" type="submit">Send Message</button>
                                    </div>
                                 
                                    </div>
                                </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>

@endsection