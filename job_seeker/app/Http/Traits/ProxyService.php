<?php
namespace App\Http\Traits;

use Image;
use App\User;
use App\Models\AppJob;
use App\Models\Notification;
use Auth;
use DB;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ClientException;
trait ProxyService
{
    public function getService()
    {
        $client = new \GuzzleHttp\Client();
        $request = $client->get('http://holisserver.ignivastaging.com/api/services');
        $response = $request->getBody()->getContents();
      	return $response;
    }
               
}
