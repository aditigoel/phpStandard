<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\Profile\UserProfileImageRequest;
use App\Http\Traits\CommonTrait;
use App\Http\Traits\UserTrait;
use App\Http\Traits\ProxyService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\User;
use App\Interfaces\ProfileInterface;
use Intervention\Image\ImageManagerStatic as Image;
use Response;
use Config;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ClientException;




class profileController extends Controller implements ProfileInterface
{

	use CommonTrait, UserTrait, ProxyService; 



   /**
     * @return \Illuminate\Http\JsonResponse
     *
     *
     *  @SWG\Post(
     *   path="/profile/uploadProfileImage",
     *   summary="uploadProfileImage",
     *   consumes={"multipart/form-data"},
     *   produces={"application/json"},
     *   tags={"Profile"},
     *   @SWG\Parameter(
     *     name="user_id",
     *     in="formData",
     *     required=true,
     *     description="Enter user_id",
     *     type="integer",
     *   ),
     *   @SWG\Parameter(
     *     name="image",
     *     in="formData",
     *     required=true,
     *     type="file",
     *     description = "image",
     *   ),   
     *   @SWG\Response(response=200, description="Success"),
     *   @SWG\Response(response=400, description="Failed"),
     *   @SWG\Response(response=405, description="Undocumented data"),
     *   @SWG\Response(response=500, description="Internal server error")
     * )
     *
     */

    public function uploadProfileImage(UserProfileImageRequest $request)
    {   
        $userid = $request['user_id'];
        $user =  User::where('id',$userid)->first(); 
        $allowed = ['jpeg', 'png', 'jpg'];
        $filename = $_FILES['image']['name'];
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        if (!in_array($ext, $allowed)) {
            $data = \Config::get('error.invalid_file_format');
            $data['data'] = (object) [];
            return Response::json($data);
        }
        // check file size
        if ($_FILES['image']['size'] > 2097152) {
            $data = \Config::get('error.file_too_large');
            $data['data'] = (object) [];
            return Response::json($data);
        }



        //upload file
        $dynamic_name = time() . '-' . $this->imageDynamicName() . '.' . $ext;
        $image = $request->file('image')->storeAs('public/user', $dynamic_name);

        if ($image) {


            $image_name = explode('/', $image);
            $saved_Image = $this->userImageVersions($image_name[2]);
            if ($saved_Image) {

                // unlink file from directory
                if ($user->image != '' && $user->image != null) {


                    $previous_image_path = storage_path('app/public/user/') . $user->image;
                    $previous_image_path_thumb = storage_path('app/public/user/thumb/') . $user->image;

                    if (file_exists($previous_image_path)) {
                        unlink($previous_image_path);
                    }
                    if (file_exists($previous_image_path_thumb)) {
                        unlink($previous_image_path_thumb);
                    }
                }



       
                // save file name in user account
                $updateUser = User::where('id', $user->id)->update(['image' => $image_name[2]]);

                if ($updateUser) {
                    // $server_url = Config::get('variable.SERVER_URL');
                    // if (!empty($image_name[2]) && file_exists(storage_path() . '/app/public/user/thumb/' . $image_name[2])) {
                    //     $path = $server_url . '/storage/user/thumb/' . $image_name[2];
                    // } else {
                    //     $path = $server_url . '/images/user-default.png';
                    // }
                    $data = \Config::get('success.uploaded_profile_image');

                    $data['image'] = $image_name[2];
                } else {
                    $data = \Config::get('error.uploaded_profile_image');
                }
            } else {
                $data = \Config::get('error.uploaded_profile_image');
            }
            return Response::json($data);

        }
    }


   /**
     * @return \Illuminate\Http\JsonResponse
     *
     *
     *  @SWG\Get(
     *   path="/profile/getPersonalProfile",
     *   summary="getPersonalProfile",
     *   consumes={"multipart/form-data"},
     *   produces={"application/json"},
     *   tags={"Profile"},
     *   @SWG\Parameter(
     *     name="user_id",
     *     in="query",
     *     required=true,
     *     description="Enter user_id",
     *     type="integer",
     *   ), 
     *   @SWG\Parameter(
     *     name="role",
     *     in="query",
     *     required=false,
     *     description="Enter role_id",
     *     type="integer",
     *   ),  
     *   @SWG\Response(response=200, description="Success"),
     *   @SWG\Response(response=400, description="Failed"),
     *   @SWG\Response(response=405, description="Undocumented data"),
     *   @SWG\Response(response=500, description="Internal server error")
     * )
     *
     */

    public function getPersonalProfile(Request $request)
    {

        $requested_data = User::where('id',$request['user_id'])->first();

        if($requested_data['data']['is_multirole'] == 1){
             $user = User::where(['id' => Auth::user()->id,'role_id', $requested_data['role_id']])
            ->select('id','name','email','role_id','status')
               ->with(['role' => function ($q) {
                    $q->select('id', 'name');
                }
                ])->first();
        }else{
            $user = User::where('id', $request['user_id'])
            ->select('id','name','email','role_id','status')
               ->with(['role' => function ($q) {
                    $q->select('id', 'name');
                }
                ])->first();
        }



                   
        if ($user) {
            $data = \Config::get('success.fetchprofile');
            $data['data'] = $user;
            return Response::json($data);
        } else {
            $data = \Config::get('error.fetchprofile');
            return Response::json($data);
        }
    }

       /**
     * @return \Illuminate\Http\JsonResponse
     *
     *
     *  @SWG\Post(
     *   path="/profile/updatePersonalProfile",
     *   summary="updatePersonalProfile",
     *   consumes={"multipart/form-data"},
     *   produces={"application/json"},
     *   tags={"Profile"},
     *   @SWG\Parameter(
     *     name="user_id",
     *     in="formData",
     *     required=true,
     *     description="Enter user_id",
     *     type="integer",
     *   ),
     *   @SWG\Parameter(
     *     name="name",
     *     in="formData",
     *     required=false,
     *     type="string",
     *     description = "name",
     *   ),
     *   @SWG\Parameter(
     *     name="address",
     *     in="formData",
     *     required=false,
     *     type="string",
     *     description = "address",
     *   ),
     *   @SWG\Parameter(
     *     name="lat",
     *     in="formData",
     *     required=false,
     *     type="string",
     *     description = "lat",
     *   ),
     *   @SWG\Parameter(
     *     name="lng",
     *     in="formData",
     *     required=false,
     *     type="string",
     *     description = "lng",
     *   ),
     *   @SWG\Response(response=200, description="Success"),
     *   @SWG\Response(response=400, description="Failed"),
     *   @SWG\Response(response=405, description="Undocumented data"),
     *   @SWG\Response(response=500, description="Internal server error")
     * )
     *
     */

    public function updatePersonalProfile(Request $request)
    {
       
        $userid = $request['user_id'];
        

        if(!empty($request['name'])){
            $updateuser['name'] = $request['name'];
        }

        if(!empty($request['address'])){
            $updateuser['address'] = $request['address'];   
        }

        if(!empty($request['lat'])){
            $updateuser['lat'] = $request['lat'];   
        }

        if(!empty($request['lng'])){
            $updateuser['lng'] = $request['lng'];   
        }

        $user = User::where('id',$userid)
            ->update($updateuser);
        
        if ($user) {
            $data = \Config::get('success.updateprofile');
        } else {
            $data = \Config::get('error.updateprofile');
        }

        return Response::json($data);
    }


    public function getGuzzleRequest(Request $request)
    {

        $service = $this->getService(); 

        echo "<pre>";
        print_r($service);
        exit;

    }

}
