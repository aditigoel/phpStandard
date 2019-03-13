<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Traits\CommonTrait;
use App\Http\Traits\UserTrait;
use App\Http\Requests\ReportUser\ReportUserRequest;
use App\Http\Requests\ReportUser\UnReportUserRequest;
use App\Interfaces\ReportUserInterface;
use App\User;
use App\Models\UserReport;
use Auth;
use Illuminate\Http\Request;
use Mail;
use Response;
use Config;


class ReportUserController extends Controller implements ReportUserInterface
{
    use CommonTrait, UserTrait;
    /**
 * @return \Illuminate\Http\JsonResponse
 *
 *
 *  @SWG\Post(
 *   path="/reportUser/reportUser",
 *   summary="report User",
 *   consumes={"multipart/form-data"},
 *   produces={"application/json"},
 *   tags={"Report User"},
 * @SWG\Parameter(
 *     name="Authorization",
 *     in="header",
 *     required=true,
 *     description = "Enter Token",
 *     type="string",
 *   ),
 * @SWG\Parameter(
 *     name="reported_to",
 *     in="formData",
 *     type="integer",
 *     required=true,
 *     description= "enter user id",
 *   ),
 * @SWG\Parameter(
 *     name="reason",
 *     in="formData",
 *     type="string",
 *     description= "enter reason",
 *   ),
 * @SWG\Parameter(
 *     name="comment",
 *     in="formData",
 *     type="string",
 *     description= "enter comment",
 *   ),
 *  
 *   @SWG\Response(response=200, description="Success"),
 *   @SWG\Response(response=400, description="Failed"),
 *   @SWG\Response(response=405, description="Undocumented data"),
 *   @SWG\Response(response=500, description="Internal server error")
 * )
 *
 */

    public function reportUser(ReportUserRequest $request)
    {
        $requested_data = $request->all();
        $array['reported_by'] = $request['data']['id'];
        $array['reported_to'] = $requested_data['reported_to'];
        $array['reason'] = isset($requested_data['reason']) ? $requested_data['reason'] : '';
        $array['comment'] = isset($requested_data['comment']) ? $requested_data['comment'] : '';
        $array['created_at'] = time();
        if($requested_data['data']['id'] != $requested_data['reported_to'])
        {
             $reportUser = UserReport::create($array);
        }
       
        if ($reportUser) {
            $data = \Config::get('success.reported_user');
            $data['data'] = (object) [];
            return Response::json($data);
        } else {
            $data = \Config::get('error.reported_user');
            $data['data'] = (object) [];
            return Response::json($data);
        }
    }
    /**
     * @return \Illuminate\Http\JsonResponse
     *
     *
     *  @SWG\Get(
     *   path="/reportUser/getReportedUser",
     *   summary="Get all reported users with search functionality with q parameter",
     *   consumes={"multipart/form-data"},
     *   produces={"application/json"},
     *   tags={"Report User"},
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
    
    public function getReportedUser(Request $request)
    {
        $reportedUser = UserReport::where('reported_by', $request['data']['id'])
            ->with(['reportedToUser' => function ($q) {
                $q->select('id', 'name', 'image');
            }]);

        if (isset($request->q) && !empty($request->q)) {
            $reportedUser = $reportedUser->whereHas('reportedToUser', function ($q) use ($request) {
                $q->where('name', 'Like', '%' . $request->q . '%');
            });
        }

        $reportedUser = $reportedUser->paginate(\Config::get('variable.page_per_record'));


        $profile = $this->userProfile($request['data']['id']);


        if ($reportedUser) {
            $data = \Config::get('success.list_reported_user');
            $data['data'] = $reportedUser;
            $data['profile'] = $profile;
            return Response::json($data);
        } else {
            $data = \Config::get('error.list_reported_user');
            $data['data'] = (object) [];
            return Response::json($data);
        }
    }
/**
 * @return \Illuminate\Http\JsonResponse
 *
 *
 *  @SWG\Post(
 *   path="/reportUser/unReportUser",
 *   summary="un Repor User",
 *   consumes={"multipart/form-data"},
 *   produces={"application/json"},
 *   tags={"Report User"},
 * @SWG\Parameter(
 *     name="Authorization",
 *     in="header",
 *     required=true,
 *     description = "Enter Token",
 *     type="string",
 *   ),
 * @SWG\Parameter(
 *     name="reported_to",
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
    
    public function unReportUser(UnReportUserRequest $request)
    {
        $requested_data = $request->all();
        $unreportUser = UserReport::where('reported_to', $requested_data['reported_to'])->where('reported_by', $request['data']['id'])->delete();
        if ($unreportUser) {
            $data = \Config::get('success.unreport_user');
            $data['data'] = (object) [];
            return Response::json($data);
        } else {
            $data = \Config::get('error.unreport_user');
            $data['data'] = (object) [];
            return Response::json($data);
        }
    }
    
   

    

}
