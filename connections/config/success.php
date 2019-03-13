<?php

return [
     'sent_friend_request' => [
        'status' => 200, 'message' => 'Friend request sent'],
        
   'accept_friend_request' => [
        'status' => 200, 'message' => 'You have accepted the friend request',
    ],
    'reject_friend_request' => [
        'status' => 200, 'message' => 'Request rejected successfully',
    ],
    'get_friends_list' => [
        'status' => 200, 'message' => 'Friends fetch successfully ',
    ],
	'get_friendrequest_list' => [
        'status' => 200, 'message' => 'Friend requests list fetch successfully',
    ],
    'unfriend' => [
        'status' => 200, 'message' => 'Unfriend successfully',
    ],
'mutual_friend' => [
        'status' => 200, 'message' => 'Mutual friends fetch successfully',
    ],
];
