<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Requests\Jobs\ApplyJobRequest;
use App\Http\Controllers\Controller;
use App\Http\Traits\ProxyService;
use Illuminate\Support\Facades\Auth;
use App\Interfaces\JobInterface;
use Response;
use Config;
use App\User;
use App\proJobApplication;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ClientException;

class jobsController extends Controller implements JobInterface
{
    
   /**
     * @return \Illuminate\Http\JsonResponse
     *
     *
     *  @SWG\Post(
     *   path="/jobs/applyJob",
     *   summary="applyJob",
     *   consumes={"multipart/form-data"},
     *   produces={"application/json"},
     *   tags={"applyJob"},
     *   @SWG\Parameter(
     *     name="user_id",
     *     in="formData",
     *     required=true,
     *     description="Enter user_id",
     *     type="integer",
     *   ),
     *   @SWG\Parameter(
     *     name="job_id",
     *     in="formData",
     *     required=true,
     *     type="integer",
     *     description = "Enter job_id",
     *   ),   
     *   @SWG\Response(response=200, description="Success"),
     *   @SWG\Response(response=400, description="Failed"),
     *   @SWG\Response(response=405, description="Undocumented data"),
     *   @SWG\Response(response=500, description="Internal server error")
     * )
     *
     */
    public function applyJob(ApplyJobRequest $request){

    	if(!empty($request['user_id'])){
    		$applyjob['user_id'] = $request['user_id'];
    	}
    	if(!empty($request['job_id'])){
    		$applyjob['pro_job_id'] = $request['job_id'];
    	}
        $applyjob['created_at'] = time();
    	$job = proJobApplication::create($applyjob);

        if ($job) {
			$data = \Config::get('success.apply_job');	
        } else { 
			$data = \Config::get('error.apply_job');
        }

         return Response::json($data);

    }



    public function acceptInvitation(Request $request){

        if(!empty($request['user_id'])){
            $applyjob['user_id'] = $request['user_id'];
        }
        if(!empty($request['job_id'])){
            $applyjob['pro_job_id'] = $request['job_id'];
        }
        $applyjob['created_at'] = time();
        $job = proJobApplication::create($applyjob);

        if ($job) {
            $data = \Config::get('success.apply_job');  
        } else { 
            $data = \Config::get('error.apply_job');
        }

         return Response::json($data);

    }



    public function requestAction(Request $request){

        
    	
    }



}
