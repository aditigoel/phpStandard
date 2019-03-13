<?php

namespace App\Interfaces;

use App\Http\Requests\User\ForgotPasswordRequest;
use App\Http\Requests\User\UserChangePasswordRequest;
use App\Http\Requests\User\UserLoginRequest;
use App\Http\Requests\User\UserProfileImageRequest;
use App\Http\Requests\User\UserResendVerifyRequest;
use App\Http\Requests\User\UserSignupRequest;
use Illuminate\Http\Request;

interface UserInterface
{
    public function signUp(UserSignupRequest $request);

    public function login(UserLoginRequest $request);

    public function logout(Request $request);

    public function resendVerification(UserResendVerifyRequest $request);

    public function forgotPassword(ForgotPasswordRequest $request);
    public function sendOtp(Request $request);
    public function resendOtp(Request $request);
    public function varify_otp(Request $request);

  
}
