<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\ForgotPasswordRequest;
use App\Http\Requests\User\SocialLoginRequest;
use App\Http\Requests\User\UserChangePasswordRequest;
use App\Http\Requests\User\UserLoginRequest;
use App\Http\Requests\User\UserProfileRequest;
use App\Http\Requests\User\UserResendVerifyRequest;
use App\Http\Requests\User\UserSignupRequest;
use App\Http\Traits\CommonTrait;
use App\Http\Traits\UserTrait;
use App\Interfaces\UserInterface;
use App\Mail\SignupEmail;
use App\Mail\ForgotPasswordEmail;
use App\Models\ActivityLog;
use App\Models\Page;
use App\Role;
use App\User;
use Config;
use DB;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Image;
use Lcobucci\JWT\Parser;
use Mail;
use Response;

class UsersController extends Controller implements UserInterface
{
    use CommonTrait, UserTrait;

    
    /**
     * @return \Illuminate\Http\JsonResponse
     *
     *
     *  @SWG\Post(
     *   path="/user/signUp",
     *   summary="signUp",
     *   consumes={"multipart/form-data"},
     *   produces={"application/json"},
     *   tags={"User"},
     *   @SWG\Parameter(
     *     name="email",
     *     in="formData",
     *     required=true,
     *     type="string",
     *     description = "email",
     *   ),
     *   @SWG\Parameter(
     *     name="password",
     *     in="formData",
     *     required=true,
     *     type="string",
     *     description = "password",
     *   ),
     *  @SWG\Parameter(
     *     name="password_confirmation",
     *     in="formData",
     *     required=true,
     *     type="string",
     *     description = "password_confirmation",
     *   ),
     *  @SWG\Parameter(
     *     name="name",
     *     in="formData",
     *     required=true,
     *     type="string",
     *     description = "name",
     *   ),
     *   
     *   @SWG\Response(response=200, description="Success"),
     *   @SWG\Response(response=400, description="Failed"),
     *   @SWG\Response(response=405, description="Undocumented data"),
     *   @SWG\Response(response=500, description="Internal server error")
     * )
     *
     */

    public function signUp(UserSignupRequest $request)
    {
        $requested_data = $request->all();
       
       
        $array['role_id'] = Role::where('name', 'user')->first()->id;
        $array['password'] = bcrypt($requested_data['password']);
        $array['email'] = $requested_data['email'];
        $array['name'] = $requested_data['name'];
        $array['verify_token'] = $this->getverificationCode();
        $array['created_at'] = time();
        $array['updated_at'] = time();
        $user = User::create($array);
        
        if ($user) {
            Mail::to($user->email)->send(new SignupEmail($user));
            $data = \Config::get('success.user_created');
            $data['data'] = (object) [];
        } else {
            $data = \Config::get('error.user_created');
            $data['data'] = (object) [];
        }
        return Response::json($data);

    }

    /**
     * @return \Illuminate\Http\JsonResponse
     *
     *
     *  @SWG\Post(
     *   path="/user/socialLogin",
     *   summary="socialLogin",
     *   consumes={"multipart/form-data"},
     *   produces={"application/json"},
     *   tags={"User"},
     *  @SWG\Parameter(
     *     name="name",
     *     in="formData",
     *     required=true,
     *     type="string",
     *     description = "name",
     *   ),
     *   @SWG\Parameter(
     *     name="email",
     *     in="formData",
     *     required=true,
     *     type="string",
     *     description = "email",
     *   ),
     *   @SWG\Parameter(
     *     name="device_type",
     *     in="formData",
     *     required=true,
     *     type="string",
     *     description = "Please enter (IOS / ANDROID) ",
     *   ),
     *  @SWG\Parameter(
     *     name="signup_type",
     *     in="formData",
     *     required=true,
     *     type="string",
     *     description = "Please enter (facebook / gmail) ",
     *   ),
     *  @SWG\Parameter(
     *     name="social_id",
     *     in="formData",
     *     required=true,
     *     type="string",
     *     description = "Please enter social id",
     *   ),
     *  @SWG\Parameter(
     *     name="image",
     *     in="formData",
     *     required=false,
     *     type="string",
     *     description = "Please enter image path",
     *   ),
     *   
     *   @SWG\Response(response=200, description="Success"),
     *   @SWG\Response(response=400, description="Failed"),
     *   @SWG\Response(response=405, description="Undocumented data"),
     *   @SWG\Response(response=500, description="Internal server error")
     * )
     *
     */

    public function socialLogin(SocialLoginRequest $request)
    {
        $requested_data = $request->all();

        $user = User::where('email', '=', request('email'))->first();
        //Now log in the user if exists
        if ($user != null) {

            switch ($user->status) { # if blocked by admin

                case 2:
                    Auth::logout();
                    $data = \Config::get('error.account_blocked_admin');
                    $data['data'] = (object) [];
                    return Response::json($data);
                    break;
            }

            DB::table('oauth_access_tokens')->where('user_id', $user->id)->update(['revoked' => true]); # logout

            Auth::loginUsingId($user->id); # login

            $user->device_type = isset($requested_data['device_type']) ? $requested_data['device_type'] : '';
            $user->signup_type = isset($requested_data['signup_type']) ? $requested_data['signup_type'] : '';
             $user->social_id = isset($requested_data['social_id']) ? $requested_data['social_id'] : '';
            $user->image = isset($requested_data['image']) ? $requested_data['image'] : '';
            $user->save();
            $remember_me = isset($requested_data['remember_me']) ? $requested_data['remember_me'] : false;
            $user_last_login = User::where('id', $user->id)
                ->update(['current_login' => time(), 'last_login' => $user->current_login]);
            return Response::json([
                'status' => 200,
                'role_id' => $user->role_id,
                'profile_status' => $user->status
                
            ])->header('access_token', $user->createToken(env("APP_NAME"))->accessToken);
        } else {
            $array = [];
            $array['role_id'] = Role::where('name', 'user')->first()->id;
            $array['name'] = $requested_data['name'];
            $array['email'] = $requested_data['email'];
            $array['device_type'] = isset($requested_data['device_type']) ? $requested_data['device_type'] : '';
            $array['signup_type'] = isset($requested_data['signup_type']) ? $requested_data['signup_type'] : '';
            $array['social_id'] = isset($requested_data['social_id']) ? $requested_data['social_id'] : '';
            $array['image'] = isset($requested_data['image']) ? $requested_data['image'] : '';
            $array['status'] = 1;
            $array['created_at'] = time();
            $array['updated_at'] = time();

            $user = User::create($array);
            Auth::loginUsingId($user->id);
            $remember_me = isset($requested_data['remember_me']) ? $requested_data['remember_me'] : false;
             $user_last_login = User::where('id', $user->id)
                ->update(['current_login' => time(), 'last_login' => $user->current_login]);
            return Response::json([
                'status' => 200,
                'role_id' => $user->role_id,
                'profile_status' => $user->status,
                
            ])->header('access_token', $user->createToken(env("APP_NAME"))->accessToken);

        }
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     *
     *
     *  @SWG\Post(
     *   path="/user/login",
     *   summary="login",
     *   consumes={"multipart/form-data"},
     *   produces={"application/json"},
     *   tags={"User"},
     *   @SWG\Parameter(
     *     name="email",
     *     in="formData",
     *     required=true,
     *     type="string",
     *     description = "email",
     *   ),
     *   @SWG\Parameter(
     *     name="password",
     *     in="formData",
     *     required=true,
     *     type="string",
     *     description = "password",
     *   ),
     * @SWG\Parameter(
     *     name="device_type",
     *     in="formData",
     *     required=false,
     *     type="string",
     *     description = "IOS/ANDROID",
     *   ),
     * @SWG\Parameter(
     *     name="device_id",
     *     in="formData",
     *     required=false,
     *     type="string",
     *     description = "device_id",
     *   ),
     *   
     *   @SWG\Response(response=200, description="Success"),
     *   @SWG\Response(response=400, description="Failed"),
     *   @SWG\Response(response=405, description="Undocumented data"),
     *   @SWG\Response(response=500, description="Internal server error")
     * )
     *
     */
    public function login(UserLoginRequest $request)
    {
        $requested_data = $request->all();
        if (Auth::attempt(['email' => request('email'), 'password' => request('password')], request('remember_me'))) {
            $user = Auth::user();
            $token= $user->createToken(env("APP_NAME"))->accessToken;

            $user = $user->update([
                'auth_token' => $token ,
                'device_type' => isset($requested_data['device_type']) ? $requested_data['device_type'] : '',
                'device_id' => isset($requested_data['device_id']) ? $requested_data['device_id'] : '',
                'updated_at' => time(),
            ]);
            $user = Auth::user();
            switch ($user->status) {
                case 0:
                    Auth::logout();
                    $data = \Config::get('error.account_not_verified');
                    $data['user_unverified'] = true;
                    $data['email'] = request('email');
                    $data['data'] = (object) [];
                    break;
                case 2:
                    Auth::logout();
                    $data = \Config::get('error.account_blocked_admin');
                    $data['data'] = (object) [];
                    break;
            }

            $remember_me = isset($requested_data['remember_me']) ? $requested_data['remember_me'] : false;
            $user_last_login = User::where('id', $user->id)
                ->update(['current_login' => time(), 'last_login' => $user->current_login]);

            $user_activity_login = ActivityLog::updateOrCreate(
                ['user_id' => $user->id, 'meta_key' => 'last_login'],
                ['user_id' => $user->id, 'meta_key' => 'last_login',
                    'meta_value' => $user->current_login, 'status' => 1,
                    'created_at' => time(), 'updated_at' => time()]
            );
            return Response::json([
                'status' => 200,
                'role_id' => $user->role_id,
                'profile_status' => $user->status,
                ])->header('access_token', $user->createToken(env("APP_NAME"))->accessToken);
        } else {
            $data = \Config::get('error.invalid_email_password');
            $data['data'] = (object) [];
        }
        return Response::json($data);

    }
    /*
     * Main Function for logout
     * @param Request $request (data_type)
     * @return type (status, success/error)
     */
    public function logout(Request $request)
    {
        $value = $request->bearerToken();
        $id = (new Parser())->parse($value)->getHeader('jti');
        $token = $request->user()->tokens->find($id);

        if ($token->revoke()) {
            $data = \Config::get('success.logout');
            $data['data'] = (object) [];
        } else {
            $data['status'] = 400;
            $data['message'] = 'Error';
        }
        return Response::json($data);

    }

    /**
     * @return \Illuminate\Http\JsonResponse
     *
     *
     *  @SWG\Post(
     *   path="/user/resendVerification",
     *   summary="resendVerification",
     *   consumes={"multipart/form-data"},
     *   produces={"application/json"},
     *   tags={"User"},
     *   @SWG\Parameter(
     *     name="email",
     *     in="formData",
     *     required=true,
     *     type="string",
     *     description = "email",
     *   ),   
     *   @SWG\Response(response=200, description="Success"),
     *   @SWG\Response(response=400, description="Failed"),
     *   @SWG\Response(response=405, description="Undocumented data"),
     *   @SWG\Response(response=500, description="Internal server error")
     * )
     *
     */
    public function resendVerification(UserResendVerifyRequest $request)
    {
        $requested_data = $request->all();
        $array['verify_token'] = $this->getverificationCode();
        $array['created_at'] = time();
        $user = User::where('email', $requested_data['email'])->update($array);
        if ($user) {
            $user = User::where('email', $requested_data['email'])->first();
             Mail::to($user->email)->send(new SignupEmail($user));
            $data = \Config::get('success.resend_verification');
            $data['data'] = (object) [];

        } else {
            $data = \Config::get('error.resend_verification');
            $data['data'] = (object) [];
        }
        return Response::json($data);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     *
     *
     *  @SWG\Post(
     *   path="/user/forgotPassword",
     *   summary="forgotPassword",
     *   consumes={"multipart/form-data"},
     *   produces={"application/json"},
     *   tags={"User"},
     *   @SWG\Parameter(
     *     name="email",
     *     in="formData",
     *     required=true,
     *     type="string",
     *     description = "email",
     *   ),   
     *   @SWG\Response(response=200, description="Success"),
     *   @SWG\Response(response=400, description="Failed"),
     *   @SWG\Response(response=405, description="Undocumented data"),
     *   @SWG\Response(response=500, description="Internal server error")
     * )
     *
     */
    public function forgotPassword(ForgotPasswordRequest $request)
    {
        $requested_data = $request->all();
        $forgot_password_code = $this->getverificationCode();
        $check_user = User::where(['email' => $requested_data['email']])->first();

        if (!empty($check_user)) {
            User::where('email', $requested_data['email'])->update(array('forgot_password_token' => $forgot_password_code)); // update the record in the DB.

            $email = $requested_data['email'];
            $admin_email = Config::get('variable.ADMIN_EMAIL');
            $frontend_url = Config::get('variable.FRONTEND_URL');
            $name = $check_user->name;
            
             Mail::to($check_user->email)->send(new ForgotPasswordEmail($check_user));
            if (count(Mail::failures()) > 0) {
                $data = \Config::get('error.error_sending_email');
                $data['data'] = (object) [];
            } else {
                $data = \Config::get('success.send_forgot_password_link');
                $data['data'] = (object) [];
            }
        } else {
            $data = \Config::get('error.send_forgot_password_link');
            $data['data'] = (object) [];
        }
        return Response::json($data);

    }


    public function varify_otp(Request $request)
    {
        $phone = $request['mobile_no'];       
        $user=User::where('otp',$request['otp'])->update(['status'=>1,'mobile_no'=>$phone]);
        if($user)
        {    
            $data=array(
            'message'=>'Varify OTP successful',
            'status'=>200,
            ); 
        }else {
            $data['status'] = 400;
            $data['message'] = 'Your otp is not valid';
            $data['data'] = [];
        } 
        return Response::json($data);
    }


    public function resendOtp(Request $request)
    {
            $phone = $request['mobile_no'];
            $user=User::where('mobile_no',$phone)->first();
            if ($user) {
                $array['otp']=mt_rand(1000, 9999);
                $otp=$array['otp'];
                $basic = new \Nexmo\Client\Credentials\Basic('ccfc455d', 'q70RjCeYeF6auOsW');
                $client = new \Nexmo\Client($basic);
                
                
                $message = $client->message()->send([
                'to' => '+91 '.$phone,
                'from' => 'Nexmo',
                'text' => 'Your OTP code is '.$otp
                ]); 
                User::where('id',$user['id'])->update($array);
                $data['message'] = 'A verification OTP has been sent to your registered number, please verify';
                $data['status'] = 200;
                return Response::json($data);
            } else {
                $data['message'] = 'Error';
                $data['status'] = 400;
                return Response::json($data);
            }
    }


 
    public function sendOtp(Request $request)
    {
       
        if(!empty($request['mobile_no'])){
            $array['mobile_no'] = $request['mobile_no'];
        }

        $array['created_at'] = time();

            //$use=Auth::user(); 
       $random=mt_rand(1000, 9999);
       $array['otp'] = $random;
       $basic = new \Nexmo\Client\Credentials\Basic('ccfc455d', 'q70RjCeYeF6auOsW');
       $client = new \Nexmo\Client($basic);
        
        $message = $client->message()->send([
        'to' => '+91 '.$array['mobile_no'],
        'from' => 'Nexmo',
        'text' => 'Your OTP code is '.$random
        ]); 
        $user = User::create($array);

        if ($user) {
            $data['message'] = 'Otp has been sent to your number';
            $data['otp'] = $random;  
            $data['status'] = 200;
            return Response::json($data);
        } else {  
            $data['message'] = 'Error';
            $data['status'] = 400;
            $data['otp'] = '0';
            return Response::json($data);
        }
    }

   





}
