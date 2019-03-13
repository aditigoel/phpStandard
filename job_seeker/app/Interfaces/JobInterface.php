<?php

namespace App\Interfaces;


use Illuminate\Http\Request;
use App\Http\Requests\jobs\ApplyJobRequest;



interface JobInterface
{
    public function applyJob(ApplyJobRequest $request);
    public function acceptInvitation(Request $request);
    public function requestAction(Request $request);
 
}