<?php

namespace App\Interfaces;


use Illuminate\Http\Request;
use App\Http\Requests\UserData\UserSkillRequest;


interface SearchInterface
{
    public function searchProviders(Request $request);
    
 
}
