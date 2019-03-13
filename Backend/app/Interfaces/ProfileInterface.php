<?php

namespace App\Interfaces;

use App\Http\Requests\Profile\UserProfileImageRequest;

use Illuminate\Http\Request;

interface ProfileInterface
{
    public function uploadProfileImage(UserProfileImageRequest $request);

}
