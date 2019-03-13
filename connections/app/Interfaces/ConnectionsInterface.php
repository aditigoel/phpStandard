<?php

namespace App\Interfaces;


use Illuminate\Http\Request;
use App\Http\Requests\Connections\AddProJobRequest;

interface ConnectionsInterface
{
    public function sendFriendRequest(SendFriendRequest $request);

    public function acceptFriendRequest(AcceptFriendRequest $request);

    public function rejectFriendRequest(AcceptFriendRequest $request);

    public function getFriends(Request $request);

    public function getRecomendedFriends(Request $request);

    public function getFriendRequestList(Request $request);
    
    public function unfriend(SendFriendRequest $request);

    public function mutualFriends(Request $request);


  
}
