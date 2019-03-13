<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\UserSkill;
use App\Http\Requests\UserData\UserSkillRequest;
use App\Http\Traits\ProxyService;
use Illuminate\Support\Facades\Auth;
use App\Interfaces\UserDataInterface;
use Response;
use Config;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ClientException;

class userDataController extends Controller implements UserDataInterface
{
    //
    use ProxyService; 

/**
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Post(
     *   path="/skills/addUserSkills",
     *   summary="create and edit skill",
     *   produces={"application/json"},
     *   tags={"skills"},
     *   @SWG\Parameter(
     *     name="Body",
     *     in="body",
     *     description = "save_type =save or publish,type =create or edit ",
     *     @SWG\Schema(ref="#/definitions/addUserSkills"),
     *   ),
     *   @SWG\Response(response=200, description="Success"),
     *   @SWG\Response(response=400, description="Failed"),
     *   @SWG\Response(response=405, description="Undocumented data"),
     *   @SWG\Response(response=500, description="Internal server error")
     * )
     * @SWG\Definition(
     *     definition="addUserSkills",
     *     allOf={
     *         @SWG\Schema(
     *            @SWG\Property(
     *              property="skills",
     *              type="array",
     *              @SWG\Items(type="integer")
     *       
     *              ),
     				@SWG\Property(
     *              property="user_id",
     *              type="integer",
     *              ),
     *         )
     *     }
     * )
     *
     */

    public function addUserSkills(UserSkillRequest $request)
    {   
        $requestdata = array();
       	
        foreach ($request['skills'] as $value) {
        $requestdata[] = [
                    'user_id'=>  $request['user_id'],
                    'skill_id'=> $value,
                    'created_at'=>time()

            ];
        }

        $deleteskills = UserSkill::where('user_id', $request['user_id'])->delete();
        $userskills = UserSkill::insert($requestdata);
        if ($userskills) {
			$data = \Config::get('success.add_skills');
        } else { 
			$data = \Config::get('error.add_skills');
        }

        return Response::json($data);
    }

    public function addPortfolio(Request $request)
    {   

    }



}
