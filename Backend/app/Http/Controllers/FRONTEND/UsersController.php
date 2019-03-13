<?php

namespace App\Http\Controllers\FRONTEND;

use App\Http\Controllers\Controller;
use App\Interfaces\Frontend\UserInterface;
use App\Models\ActivityLog;
use App\User;
use Config;
use Illuminate\Http\Request;
use Redirect;
use Validator;

class UsersController extends Controller implements UserInterface
{

    public function verifyUser($key)
    {
        $user = User::where('verify_token', $key)->select('id','secondary_email')->first();
        if($user)
        {
            if($user->secondary_email == ''){
                $user->update([
                    'status' => '1',
                    'verify_token' => '',
                    
                ]);
                
                    return view('verify');
            }
            else{
                $user->update([
                    'status' => '1',
                    'verify_token' => '',
                    'email' => $user->secondary_email,
                    'secondary_email' => '',
                    
                ]);
                return view('verify');
                }
        } 
            else 
            {
                return view('error_verify');
            }
    }
    

    /*
     * Main Function to show reset password form
     * @param Request $request (token)
     * @return type (reset page)
     */

    public function showResetForm(Request $request, $token = null)
    {
        $tok = User::where('forgot_password_token', $token)->first();

        if ($tok) {
            return view('reset')->with(
                ['token' => $token, 'email' => $request->email]
            );

        } else {
            return view('token_expire')->with(
                ['token' => $token, 'email' => $request->email]
            );
        }
    }

    public function resetPassword(Request $request)
    {
         $requested_data = $request->all();

        $rules = $this->validate($request, [
            'password' => 'required|min:6|confirmed',
            'forgot_password_token' => 'required|exists:users,forgot_password_token',
            
        ]);
           $check_user_access = User::where(['forgot_password_token' => $requested_data['forgot_password_token']])->first();
            $password = bcrypt($requested_data['password']);
            # update data
            $update_user = User::where('id', $check_user_access->id)
                ->update(['password' => $password, 'auth_token' => '', 'forgot_password_token' => '']);

            return view('reset_success');

       
    }

    

}
