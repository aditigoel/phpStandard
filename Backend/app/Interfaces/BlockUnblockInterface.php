<?php

namespace App\Interfaces;


use Illuminate\Http\Request;
use App\Http\Requests\BlockUnblock\BlockedUserRequest;
use App\Http\Requests\BlockUnblock\UnblockedUserRequest;


interface BlockUnblockInterface
{
   public function blockedUser(BlockedUserRequest $request);

    public function getBlockedUser(Request $request);

    public function unblockedUser(UnblockedUserRequest $request);
}
