<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\GetPagesRequest;
use App\Http\Requests\Settings\ContactUsRequest;
use App\Http\Requests\Settings\UserChangeEmailRequest;
use App\Http\Traits\CommonTrait;
use App\Http\Traits\UserTrait;
use App\Interfaces\SettingInterface;
use App\User;
use App\Models\Page;
use App\Models\Contact;
use App\Models\JobReport;
use App\Models\AppJob;
use App\Models\Subject;
use Auth;
use Illuminate\Http\Request;
use App\Mail\ChangeEmail;
use Mail;
use Response;
use Config;


class SettingsController extends Controller implements SettingInterface
{
    use CommonTrait, UserTrait;
    /**
 * @return \Illuminate\Http\JsonResponse
 *
 *
 *  @SWG\Post(
 *   path="/settings/settingsOnOff",
 *   summary="push notification on off",
 *   consumes={"multipart/form-data"},
 *   produces={"application/json"},
 *   tags={"Settings"},
 * @SWG\Parameter(
 *     name="Authorization",
 *     in="header",
 *     required=true,
 *     description = "Enter Token",
 *     type="string",
 *   ),
 * @SWG\Parameter(
 *     name="push_notification",
 *     in="formData",
 *     type="string",
 *     description= "0 for OFF and 1 for ON",
 *   ),
 *  
 *   @SWG\Response(response=200, description="Success"),
 *   @SWG\Response(response=400, description="Failed"),
 *   @SWG\Response(response=405, description="Undocumented data"),
 *   @SWG\Response(response=500, description="Internal server error")
 * )
 *
 */

    public function settingsOnOff(Request $request)
    {
        $user = Auth::user();
        $user = $user->update($request->all());
        if ($user) {
            $data = \Config::get('success.action_on_off');
        } else {
            $data = \Config::get('error.action_on_off');
            $data['data'] = (object) [];
        }
        return Response::json($data);

    }
    /**
     * @return \Illuminate\Http\JsonResponse
     *
     *
     *  @SWG\Get(
     *   path="/settings/getPages",
     *   summary="Get page information",
     *   consumes={"multipart/form-data"},
     *   produces={"application/json"},
     *   tags={"Settings"},
     * @SWG\Parameter(
 *     name="Authorization",
 *     in="header",
 *     required=true,
 *     description = "Enter Token",
 *     type="string",
 *   ),
     * @SWG\Parameter(
     *     name="meta_key",
     *     in="query",
     *     required=true,
     *     type="string",
     *     description = "for about us page meta_key = about_us, for terms and conditions meta_key = term, for privacy policy meta_key=privacy_policy"
     *   ),
     *   @SWG\Response(response=200, description="Success"),
     *   @SWG\Response(response=400, description="Failed"),
     *   @SWG\Response(response=405, description="Undocumented data"),
     *   @SWG\Response(response=500, description="Internal server error")
     * )
     *
     */
    
    public function getPages(GetPagesRequest $request)
    {
        $requested_data = $request->all();
        $getPages = Page::where('meta_key', $requested_data['meta_key'])->latest()->first();
        if ($getPages) {
            $data = \Config::get('success.get_pages');
            $data['data'] = $getPages->meta_value;
            $data['user_id'] = Auth::user()->id;
        } else {
            $data = \Config::get('error.get_pages');
            $data['data'] = (object) [];
        }
        return Response::json($data);

    }
/**
 * @return \Illuminate\Http\JsonResponse
 *
 *
 *  @SWG\Post(
 *   path="/settings/contactUs",
 *   summary="contact us page",
 *   consumes={"multipart/form-data"},
 *   produces={"application/json"},
 *   tags={"Settings"},
 * @SWG\Parameter(
 *     name="Authorization",
 *     in="header",
 *     required=true,
 *     description = "Enter Token",
 *     type="string",
 *   ),
 * @SWG\Parameter(
 *     name="message",
 *     in="formData",
 *     required=true,
 *     type="string",
 *   ),
 *  @SWG\Parameter(
 *     name="subject",
 *     in="formData",
 *     required=true,
 *     type="string",
 *   ),
 *   @SWG\Response(response=200, description="Success"),
 *   @SWG\Response(response=400, description="Failed"),
 *   @SWG\Response(response=405, description="Undocumented data"),
 *   @SWG\Response(response=500, description="Internal server error")
 * )
 *
 */
    
    public function contactUs(ContactUsRequest $request)
    {
        $email = Auth::user()->email;
        $name = Auth::user()->fullname;
        $message = $request->message;
        $subject = $request->subject;
        $admin_email = Config::get('variable.ADMIN_EMAIL');
        $frontend_url = Config::get('variable.FRONTEND_URL');
        Mail::send('emails.users.contact_us', [
            "data" => array(
                "name"=> $name,
                "email" => $email,
                "message" => $message ,
                "subject" => $subject,
            )], function ($message) use ($email, $admin_email,$subject) {
            $message->from($email, config('variable.SITE_NAME'));
            $message->to(trim($admin_email), config('variable.SITE_NAME'))->subject(config('variable.SITE_NAME') . ' : Contact Us');
        });
        if (count(Mail::failures()) > 0) {
            $data = \Config::get('error.error_sending_email');
            $data['data'] = (object) [];
            return Response::json($data);
        } else {

            $data = \Config::get('success.contact_us');
            $data['data'] = (object) [];
            return Response::json($data);
        }
    }
    
    /**
     * @return \Illuminate\Http\JsonResponse
     *
     *
     *  @SWG\Post(
     *   path="/settings/changeEmail",
     *   summary="change email",
     *   consumes={"multipart/form-data"},
     *   produces={"application/json"},
     *   tags={"Settings"},
     * @SWG\Parameter(
     *     name="Authorization",
     *     in="header",
     *     required=true,
     *     description="Enter Token",
     *     type="string",
     *   ),
     *   @SWG\Parameter(
     *     name="old_email",
     *     in="formData",
     *     required=true,
     *     type="string",
     *     description = "old email",
     *   ),   
     * @SWG\Parameter(
     *     name="new_email",
     *     in="formData",
     *     required=true,
     *     type="string",
     *     description = "new email",
     *   ),   
     * @SWG\Parameter(
     *     name="new_email_confirmation",
     *     in="formData",
     *     required=true,
     *     type="string",
     *     description = "new email confirmation",
     *   ),   
     *   @SWG\Response(response=200, description="Success"),
     *   @SWG\Response(response=400, description="Failed"),
     *   @SWG\Response(response=405, description="Undocumented data"),
     *   @SWG\Response(response=500, description="Internal server error")
     * )
     *
     */

    public function changeEmail(UserChangeEmailRequest $request)
    {
        $oldEmail = $request->old_email;
        $newEmail = $request->new_email;
        $email = Auth::user()->email;
        $user = Auth::user();

        if ($oldEmail = Auth::user()->email) {

                
                $user = User::find(Auth::user()->id);
                        $user->update(
                        ['secondary_email' => $newEmail,
                          'verify_token'=>  $this->getverificationCode(),
                          ]
                        );
                        if($user){

                            Mail::to($newEmail)->send(new ChangeEmail($user));

                $data = \Config::get('success.update_email');
                $data['data'] = (object) [];
            } else {
                $data = \Config::get('error.update_email');
                $data['data'] = (object) [];
            } 
        } else {
            $data = \Config::get('error.wrong_old_email');
            $data['data'] = (object) [];
        }   
        return Response::json($data);
    }

     /**
     * @return \Illuminate\Http\JsonResponse
     *
     *
     *  @SWG\Post(
     *   path="/settings/changePassword",
     *   summary="Change Password",
     *   consumes={"multipart/form-data"},
     *   produces={"application/json"},
     *   tags={"Settings"},
     *   @SWG\Parameter(
     *     name="Authorization",
     *     in="header",
     *     required=true,
     *     description="Enter Token",
     *     type="string",
     *   ),
     *   @SWG\Parameter(
     *     name="old_password",
     *     in="formData",
     *     required=true,
     *     type="string",
     *     description = "old password",
     *   ),  
     *   @SWG\Parameter(
     *     name="new_password",
     *     in="formData",
     *     required=true,
     *     type="string",
     *     description = "new password",
     *   ),  
     * @SWG\Parameter(
     *     name="new_password_confirmation",
     *     in="formData",
     *     required=true,
     *     type="string",
     *     description = "new password confirmation",
     *   ),   
     *   @SWG\Response(response=200, description="Success"),
     *   @SWG\Response(response=400, description="Failed"),
     *   @SWG\Response(response=405, description="Undocumented data"),
     *   @SWG\Response(response=500, description="Internal server error")
     * )
     *
     */

    public function changePassword(UserChangePasswordRequest $request)
    {
        $oldPassword = $request->old_password;
        $newPassword = $request->new_password;
        $hashedPassword = Auth::user()->password;
        if (Hash::check($oldPassword, $hashedPassword)) {
            $user = User::find(Auth::user()->id)
                ->update(
                    ['password' => Hash::make($newPassword)]
                );
            if ($user) {
                $data = \Config::get('success.update_password');
                $data['data'] = (object) [];
            } else {
                $data = \Config::get('error.update_password');
                $data['data'] = (object) [];
            }
        } else {
            $data = \Config::get('error.wrong_old_password');
            $data['data'] = (object) [];
        }
        return Response::json($data);

    }
    

    

}
