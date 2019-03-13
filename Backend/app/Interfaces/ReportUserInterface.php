<?php

namespace App\Interfaces;


use Illuminate\Http\Request;
use App\Http\Requests\ReportUser\ReportUserRequest;
use App\Http\Requests\ReportUser\UnReportUserRequest;


interface ReportUserInterface
{
   public function reportUser(ReportUserRequest $request);

    public function getReportedUser(Request $request);

    public function unReportUser(UnReportUserRequest $request);
}
