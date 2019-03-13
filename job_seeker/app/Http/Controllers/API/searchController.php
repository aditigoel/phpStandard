<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Traits\ProxyService;
use Illuminate\Support\Facades\Auth;
use App\Interfaces\SearchInterface;
use Response;
use Config;
use App\User;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ClientException;

class searchController extends Controller implements SearchInterface
{
    //


	   /**
     * @return \Illuminate\Http\JsonResponse
     *
     *
     *  @SWG\Get(
     *   path="/search/searchProviders",
     *   summary="searchProviders",
     *   consumes={"query"},
     *   tags={"search"},
  
     *   @SWG\Parameter(
     *     name="name",
     *     in="query",
     *     required=false,
     *     type="string",
     *     description = "name of the provider",
     *   ),   
     *   @SWG\Response(response=200, description="Success"),
     *   @SWG\Response(response=400, description="Failed"),
     *   @SWG\Response(response=405, description="Undocumented data"),
     *   @SWG\Response(response=500, description="Internal server error")
     * )
     *
     */

    public function searchProviders(Request $request)
    {

    	$query = User::select('id','name','image')->orderBy('id', 'DESC');
   		$query = $query->where('status',1);
   		$query = $query->where('role_id',2);

        if(!empty($request['name'])){
            $query = $query->where('name', 'like', '%' . $request['name'] . '%');
        }

		$providers = $query->paginate(10);

        if ($providers) {
			$data = \Config::get('success.search_providers');
			$data['data'] = $providers;
        } else { 
			$data = \Config::get('error.search_providers');
        }
         return Response::json($data);

    }
}
