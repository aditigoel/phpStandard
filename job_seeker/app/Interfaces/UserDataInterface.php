<?php

namespace App\Interfaces;


use Illuminate\Http\Request;
use App\Http\Requests\UserData\UserSkillRequest;


interface UserDataInterface
{
    public function addUserSkills(UserSkillRequest $request);
    public function addPortfolio(Request $request);
   


}
