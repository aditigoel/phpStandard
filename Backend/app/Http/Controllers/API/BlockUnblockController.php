<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Traits\CommonTrait;
use App\Http\Traits\UserTrait;
use App\Http\Requests\BlockUnblock\BlockedUserRequest;
use App\Http\Requests\BlockUnblock\UnblockedUserRequest;
use App\Interfaces\BlockUnblockInterface;
use App\User;
use App\Models\BlockedUser;
use Auth;
use Illuminate\Http\Request;
use Mail;
use Response;
use Config;


class BlockUnblockController extends Controller implements BlockUnblockInterface
{
    use CommonTrait, UserTrait;
    /**
 * @return \Illuminate\Http\JsonResponse
 *
 *
 *  @SWG\Post(
 *   path="/blockunblock/blockedUser",
 *   summary="block User",
 *   consumes={"multipart/form-data"},
 *   produces={"application/json"},
 *   tags={"BlockUnblock"},
 * @SWG\Parameter(
 *     name="Authorization",
 *     in="header",
 *     required=true,
 *     description = "Enter Token",
 *     type="string",
 *   ),
 * @SWG\Parameter(
 *     name="blocked_to",
 *     in="formData",
 *     type="integer",
 *     required=true,
 *     description= "enter user id",
 *   ),
 *  
 *   @SWG\Response(response=200, description="Success"),
 *   @SWG\Response(response=400, description="Failed"),
 *   @SWG\Response(response=405, description="Undocumented data"),
 *   @SWG\Response(response=500, description="Internal server error")
 * )
 *
 */

    public function blockedUser(BlockedUserRequest $request)
    {
        $requested_data = $request->all();
        $array['blocked_by'] = $request['data']['id'];
        $array['blocked_to'] = $requested_data['blocked_to'];
        $array['created_at'] = time();
        if($requested_data['data']['id'] != $requested_data['blocked_to'])
        {
            $blockedUser = BlockedUser::create($array);
        }
        
        if ($blockedUser) {
            $data = \Config::get('success.blocked_user');
            $data['data'] = (object) [];
            return Response::json($data);
        } else {
            $data = \Config::get('error.blocked_user');
            $data['data'] = (object) [];
            return Response::json($data);
        }
    }
    /**
     * @return \Illuminate\Http\JsonResponse
     *
     *
     *  @SWG\Get(
     *   path="/blockunblock/getBlockedUser",
     *   summary="Get all block users with search functionality with q parameter",
     *   consumes={"multipart/form-data"},
     *   produces={"application/json"},
     *   tags={"BlockUnblock"},
     * @SWG\Parameter(
     *     name="Authorization",
     *     in="header",
     *     required=true,
     *     description = "Enter Token",
     *     type="string",
     *   ),
     * @SWG\Parameter(
     *     name="q",
     *     in="query",
     *     required=false,
     *     type="string",
     *     description = "enter any user name in q parameter for search specific blocked user"
     *   ),
     *   @SWG\Response(response=200, description="Success"),
     *   @SWG\Response(response=400, description="Failed"),
     *   @SWG\Response(response=405, description="Undocumented data"),
     *   @SWG\Response(response=500, description="Internal server error")
     * )
     *
     */
    
    public function getBlockedUser(Request $request)
    {
        $blockedUser = BlockedUser::where('blocked_by', $request['data']['id'])
            ->with(['blockedTo' => function ($q) {
                $q->select('id', 'name', 'image');
            }]);

        if (isset($request->q) && !empty($request->q)) {
            $blockedUser = $blockedUser->whereHas('blockedTo', function ($q) use ($request) {
                $q->where('name', 'Like', '%' . $request->q . '%');
            });
        }

        $blockedUser = $blockedUser->paginate(\Config::get('variable.page_per_record'));
        if ($blockedUser) {
            $data = \Config::get('success.list_blocked_user');
            $data['data'] = $blockedUser;
            return Response::json($data);
        } else {
            $data = \Config::get('error.list_blocked_user');
            $data['data'] = (object) [];
            return Response::json($data);
        }
    }
/**
 * @return \Illuminate\Http\JsonResponse
 *
 *
 *  @SWG\Post(
 *   path="/blockunblock/unblockedUser",
 *   summary="unblock  User",
 *   consumes={"multipart/form-data"},
 *   produces={"application/json"},
 *   tags={"BlockUnblock"},
 * @SWG\Parameter(
 *     name="Authorization",
 *     in="header",
 *     required=true,
 *     description = "Enter Token",
 *     type="string",
 *   ),
 * @SWG\Parameter(
 *     name="blocked_to",
 *     in="formData",
 *     type="integer",
 *     description= "enter user id",
 *   ),
 *  
 *   @SWG\Response(response=200, description="Success"),
 *   @SWG\Response(response=400, description="Failed"),
 *   @SWG\Response(response=405, description="Undocumented data"),
 *   @SWG\Response(response=500, description="Internal server error")
 * )
 *
 */
    
    public function unblockedUser(UnblockedUserRequest $request)
    {
        $requested_data = $request->all();
        $unblockedUser = BlockedUser::where('blocked_to', $requested_data['blocked_to'])->where('blocked_by', $request['data']['id'])->delete();
        if ($unblockedUser) {
            $data = \Config::get('success.unblocked_user');
            $data['data'] = (object) [];
            return Response::json($data);
        } else {
            $data = \Config::get('error.unblocked_user');
            $data['data'] = (object) [];
            return Response::json($data);
        }
    }
    
   

    

}
