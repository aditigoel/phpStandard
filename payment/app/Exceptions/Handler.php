<?php
namespace App\Exceptions;
use Exception;
use Illuminate\Auth\AuthenticationException as AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];
    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];
    /**
     * Report or log an exception.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }
    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
       
        
        // if ($request->wantsJson() && $exception instanceof \Symfony\Component\Debug\Exception\FatalErrorException) {
        //     return response()->json(['status' => 400, 'message' => $exception->getMessage()], 400);
        // }
        // if ($request->wantsJson() && $exception instanceof \Exception) {
        //     //dd($exception);
        //     if ($exception instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException) {
        //         return response()->json(['status' => 400, 'message' => '404 not found'], 400);
        //     }
        // } else {
        //     return response()->json(['status' => 400, 'message' => $exception->getMessage()], 400);
        // }
        // if ($request->wantsJson() && $exception instanceof \ModelNotFoundException) {
        //     return response()->json(['status' => 400, 'message' => $exception->getMessage()], 400);
        // }
        // if ($request->wantsJson() && $exception instanceof \QueryException) {
        //     return response()->json(['status' => 400, 'message' => $exception->getMessage()], 400);
        // }
        return parent::render($request, $exception);
    }
    /**
     * Convert an authentication exception into an unauthenticated response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Auth\AuthenticationException  $exception
     * @return \Illuminate\Http\Response
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson()) {
            return response()->json(['status' => 401, 'message' => 'Session Expired'], 401);
        }
        return redirect()->guest(route('login'));
    }
}