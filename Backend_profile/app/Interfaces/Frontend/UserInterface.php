<?php

namespace App\Interfaces\Frontend;

use Illuminate\Http\Request;

interface UserInterface
{

    public function verifyUser($key);

    public function showResetForm(Request $request, $token = null);

    public function resetPassword(Request $request);

}
