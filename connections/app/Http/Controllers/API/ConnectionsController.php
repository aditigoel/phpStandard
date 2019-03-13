<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Connections\AcceptFriendRequest;
use App\Http\Requests\Connections\SendFriendRequest;
use App\Http\Traits\CommonTrait;
use App\Interfaces\ConnectionsInterface;
use App\Models\BlockedUser;
use App\Models\Friend;
use App\User;
use App\Role;
use Config;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Response;
use Input;


class FriendsController extends Controller implements ConnectionsInterface
{
    use CommonTrait;
    /**
     *  Function : send Friend Request
     * @bodyParam friend_id integer required user id of user .
     *  Output : Sent,
     */

    public function sendFriendRequest(SendFriendRequest $request)
    {   

        $already_friend = $this->alreadyFriend($request); # check if already friend

        if ($already_friend == 1) {
            $data = \Config::get('error.already_friend');
            $data['data'] = (object) [];
            return Response::json($data);
        }
        $already_friend_request_sent = $this->alreadyFriendRequestSent($request); # check if already friend request sent

        if ($already_friend_request_sent == 1) {
            $data = \Config::get('error.already_friend_request_sent');
            $data['data'] = (object) [];
            return Response::json($data);
        }
        $already_friend_request_receive = $this->alreadyFriendRequestReceive($request); # check if already friend request receive

        if ($already_friend_request_receive == 1) {
            $data = \Config::get('error.already_friend_request_receive');
            $data['data'] = (object) [];
            return Response::json($data);
        }
        $requested_data = $request->all();
        $challenge_id = 0;
        $user_id = $requested_data['data']['id'];
        $type = 13; # Any user send friend request
        $recv_id = $request->friend_id;
        $sent_friend_request = Friend::Create(['owner_user_id' => $request['data']['id'],
            'friend_id' => $request->friend_id,
            'created_at' => time(),
            'updated_at' => time()]);

        if ($sent_friend_request) {

          //  $notification = $this->notifications($requested_data, $user_id, $challenge_id, $type, $recv_id);

         //   $push_notification = $this->pushNotifications($recv_id,$notification->id,$type,$challenge_id);

         //   $email_notification = $this->emailNotifications($recv_id,$notification->id,$type,$challenge_id);

           
                         
            $data = \Config::get('success.sent_friend_request');
            $data['data'] = (object) [];
            return Response::json($data);
        } else {
            $data = \Config::get('error.sent_friend_request');
            $data['data'] = (object) [];
            return Response::json($data);
        }
    }
    # check if already friend request sent .
    private function alreadyFriendRequestSent($request)
    {
        $already_friend_request_sent = Friend::where('friend_id', $request->friend_id)
            ->where('owner_user_id', Auth::user()->id)
            ->where('status', 0)
            ->count();

        return $already_friend_request_sent;
    }
    # check if already friend request recieve  .
    private function alreadyFriendRequestReceive($request)
    {
        $already_friend_request_receive = Friend::where('friend_id', Auth::user()->id)
            ->where('owner_user_id', $request->friend_id)
            ->where('status', 0)
            ->count();

        return $already_friend_request_receive;

    }
    # check if already friend  .
    private function alreadyFriend($request)
    {
        $already_friend = Friend::where(function ($query) use ($request) {
            $query->where('friend_id', $request->friend_id)
                ->where('owner_user_id', Auth::user()->id)
                ->where('status', 1);
        })
            ->count();

        return $already_friend;

    }

    /**
     *  Function : accept Friend Request
     * @bodyParam friend_id integer required user id of user.
     *  Output :Accept,
     */

    public function acceptFriendRequest(AcceptFriendRequest $request)
    {
        $requested_data = $request->all();
        $challenge_id = 0;
        $user_id = Auth::user()->id;
        $type = 14; # any user accept friend request

        $recv_id = $request->friend_id;

        $data = ['owner_user_id' => $request->friend_id,
            'friend_id' => $request['data']['id']];
        $accept = Friend::where($data)->update(['status' => 1]);
        if ($accept) {
            $insert_new_row = Friend::Create(
                ['owner_user_id' => $request['data']['id'],
                    'friend_id' => $request->friend_id,
                    'status' => 1,
                    'created_at' => time(),
                    'updated_at' => time()]);
        }

        if ($accept && $insert_new_row) {
            //$notification = $this->notifications($requested_data, $user_id, $challenge_id, $type, $recv_id);

           //  $push_notification = $this->pushNotifications($recv_id,$notification->id,$type,$challenge_id);

            //  $email_notification = $this->emailNotifications($recv_id,$notification->id,$type,$challenge_id);

            $data = \Config::get('success.accept_friend_request');
            $data['data'] = (object) [];
            return Response::json($data);
        } else {
            $data = \Config::get('error.accept_friend_request');
            $data['data'] = (object) [];
            return Response::json($data);
        }

    }

    /**
     *  Function : reject Friend Request
     * @bodyParam friend_id integer required user id of user .
     *  Output : Sent,
     */

    public function rejectFriendRequest(AcceptFriendRequest $request)
    {
        $data = ['owner_user_id' => $request->friend_id,
            'friend_id' => $request['data']['id']];
        $reject = Friend::where($data)->delete();

        if ($reject) {
            $data = \Config::get('success.reject_friend_request');
            $data['data'] = (object) [];
            return Response::json($data);
        } else {
            $data = \Config::get('error.reject_friend_request');
            $data['data'] = (object) [];
            return Response::json($data);
        }
    }

    /**
     *  Function :  Get friends list.
     * @queryParam q string optional name of the user for search .
     * @return response (status, message, success/failure)
     */

    public function getFriends(Request $request)
    {
        $myfriends = $this->userFriends($request['data']['id']);


        $getFriends = User::where('id', '!=', $request['data']['id'])
            ->whereIn('id', $myfriends)
            ->where('role_id', Role::where('slug','user')->first()->id)
            ->where('status', 1)
            ->select('id', 'name', 'image')
            ->doesnthave('blockMyusers')
            ->doesnthave('blockMeusers')
            ->withCount(['friends' => function ($q2) use ($myfriends) {
                $q2->whereIn('friend_id', $myfriends)->where('status', 1);
            }]);

        if (isset($request->q) && !empty($request->q)):

            $getFriends = $getFriends->where('name', 'Like', '%' . $request->q . '%');

        endif;

        $getFriends = $getFriends->orderBy('name', 'asc')
            ->paginate(\Config::get('variable.page_per_record'))
            ->toArray();

 
        $data = \Config::get('success.get_friends_list'); #For users fetched successfully
        $data['friends_list'] = $getFriends;
     
        return Response::json($data);

    }

    


    /**
     *  Function :  Get recommended friends
     * @return response (status, message, success/failure)
     */
    public function getRecomendedFriends(Request $request)
    {
        $myfriends = $this->userFriends($request['data']['id']);

        $recomended = User::where('id', '!=', $request['data']['id'])
            ->whereNotIn('id', $myfriends)
            ->where('role_id', 2)
            ->where('status', 1)
            ->select('id', 'name', 'image')
            ->doesnthave('blockMyusers')
            ->doesnthave('blockMeusers')
              ->doesnthave('sendRequestUsers')
            ->doesnthave('receiveRequestUsers')
        
            ->withCount(['friends' => function ($q2) use ($myfriends) {
                $q2->whereIn('friend_id', $myfriends)->where('status', 1);
            }])->inRandomOrder();

        if (isset($request->q) && !empty($request->q)):

            $recomended = $recomended->where('name', 'Like', '%' . $request->q . '%');

        endif;

        $recomended = $recomended->orderBy('friends_count', 'desc')
            ->take(3)
            ->get()
            ->toArray();

        
        $data = \Config::get('success.list_recomended_user'); #For users fetched successfully
        $data['recomend_list'] = $recomended;
      

        return Response::json($data);

    }

  
    /**
     *  Function: Ger Friend requests.
     * @return response (status, message, success/failure)
     */
    public function getFriendRequestList(Request $request)
    {   

        $get_freind_list = Friend::where('friend_id', $request['data']['id'])->where('status', 0)->with(['owner' => function ($q) {
            $q->select('id', 'name', 'image');
        }])
        ->doesnthave('blockMyusers')
            ->doesnthave('blockMeusers')
            ->orderBy('created_at', 'desc')->paginate(\Config::get('variable.page_per_record'));
        if ($get_freind_list) {
            $data = \Config::get('success.get_friendrequest_list');
            $data['data'] = $get_freind_list;
            return Response::json($data);
        } else {
            $data = \Config::get('error.get_friendrequest_list');
            $data['data'] = (object) [];
            return Response::json($data);
        }
    }
    /**
     *  Function: Unfriend Brandy
     * @bodyParam friend_id integer required The id of the friend.
     * @return response (status, message, success/failure)
     */
    public function unfriend(SendFriendRequest $request)
    {
        $unfriend = friend::where(['friend_id' => $request->friend_id, 'status' => 1])->delete();
        $unfriend_del = friend::where(['owner_user_id' => $request->friend_id, 'status' => 1])->delete();
        if ($unfriend && $unfriend_del) {
            $data = \Config::get('success.unfriend');
            $data['data'] = (object) [];
            return Response::json($data);
        } else {
            $data = \Config::get('error.unfriend');
            $data['data'] = (object) [];
            return Response::json($data);
        }
    }

   

    /*
     *  Function:Get Mutual friends
     * queryParam friend_id required The friend id of the user.
     * @return response (status, message, success/failure)
     */
    public function mutualFriends(Request $request)
    {
        $myfriends = $this->userFriends($request['data']['id']);
        $requested_data = $request->all();
        $get_mutual_friends = Friend::where('friend_id', $requested_data['friend_id'])
            ->doesnthave('blockMyusers')
            ->doesnthave('blockMeusers')
            ->where('status', 1)
            ->with('owner')
            ->whereIn('owner_user_id', $myfriends)
            ->orderBy('created_at', 'desc')
            ->paginate(\Config::get('variable.page_per_record'));
        if ($get_mutual_friends) {
            $data = \Config::get('success.mutual_friend');
            $data['data'] = $get_mutual_friends;
            return Response::json($data);
        } else {
            $data = \Config::get('error.mutual_friend');
            $data['data'] = (object) [];
            return Response::json($data);
        }
    }
}
