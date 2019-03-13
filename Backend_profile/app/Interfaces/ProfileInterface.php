<?php

namespace App\Interfaces;

use App\Http\Requests\Profile\UserProfileImageRequest;
use Illuminate\Http\Request;


interface ProfileInterface
{
    public function uploadProfileImage(UserProfileImageRequest $request);
    public function getPersonalProfile(Request $request);
    public function updatePersonalProfile(Request $request);
    public function getGuzzleRequest(Request $request);

}
