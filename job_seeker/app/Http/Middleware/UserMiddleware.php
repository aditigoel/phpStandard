<?php

namespace App\Http\Middleware;

use App\User;
use App\UserRole;
use App\Models\UserChallengeCategory;
use Auth;
use Closure;

class UserMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $request['data'] = User::where('id', Auth::user()->id)

        ->select('id','name','email','role_id','status')
           ->with(['role' => function ($q) {
                $q->select('id', 'name');
            }
            ])->first();

        return $next($request);
    }
}
