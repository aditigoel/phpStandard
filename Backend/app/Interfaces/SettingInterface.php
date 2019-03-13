<?php

namespace App\Interfaces;


use Illuminate\Http\Request;
use App\Http\Requests\Settings\GetPagesRequest;
use App\Http\Requests\Settings\ContactUsRequest;
use App\Http\Requests\Settings\UserChangeEmailRequest;



interface SettingInterface
{
   public function settingsOnOff(Request $request);
    public function getPages(GetPagesRequest $request);
    public function contactUs(ContactUsRequest $request);
    public function changeEmail(UserChangeEmailRequest $request);
    
}
