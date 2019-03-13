<?php

namespace App\Interfaces;


use Illuminate\Http\Request;
use App\Http\Requests\ProJob\AddProJobRequest;
use App\Http\Requests\ProJob\EditProJobRequest;
use App\Http\Requests\ProJob\InviteProJobRequest;
interface ProJobsInterface
{
    public function add(AddProJobRequest $request);

    public function edit(EditProJobRequest $request);

    public function delete(Request $request);

    public function getSingleJob(Request $request);

    public function getAllJobs(Request $request);

    public function inviteJob(InviteProJobRequest $request);


  
}
