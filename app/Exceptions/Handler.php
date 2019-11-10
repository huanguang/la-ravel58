<?php

namespace App\Exceptions;

use Exception;
use Encore\Admin\Reporter\Reporter;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Auth\AuthenticationException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        \Illuminate\Auth\AuthenticationException::class,
        \Illuminate\Auth\Access\AuthorizationException::class,
        \Symfony\Component\HttpKernel\Exception\HttpException::class,
        \Illuminate\Database\Eloquent\ModelNotFoundException::class,
        \Illuminate\Session\TokenMismatchException::class,
        \Illuminate\Validation\ValidationException::class,
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
//        if ($this->shouldReport($exception)) {
//            Reporter::report($exception);
//        }
//        if ($this->shouldReport($exception)) {
//            Reporter::report($exception);
//        }
//        if (app()->bound('sentry') && $this->shouldReport($exception)) {
//            app('sentry')->captureException($exception);
//        }
//        if($exception->getMessage() == 'Route [login] not defined.'){
//            //dump($exception->getMessage());die;
//            return ['sadsa'=>'asdsa'];
//            return response()->json(['message' => 'Unauthenticated.'], 401);
//        }
        parent::report($exception);
        //parent::report($exception);

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
        /**
         * 异常接管
         */
        $class = get_class($exception);
        switch ($class){
            case  'Symfony\Component\HttpKernel\Exception\HttpException':
                return response()->json(['message' => '内部服务器错误！','status_code'=>500],500);
                break;
            case  'Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException':
                return response()->json(['message' => '未登陆！','status_code'=>401]);
                break;
            case  'Illuminate\Auth\AuthenticationException':
                return response()->json(['message' => '未登陆！','status_code'=>401]);
                break;
            case  'Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException':
                return response()->json(['message' => '不允许的请求！','status_code'=>405],405);
                break;
            case  'Symfony\Component\HttpKernel\Exception\NotFoundHttpException':
                return response()->json(['message' => '资源不存在！','status_code'=>404],404);
                break;
            case  'InvalidArgumentException':
                if($exception->getMessage() == 'Route [login] not defined.'){
                    return response()->json(['message' => '未登陆！','status_code'=>401],401);
                }
                return response()->json(['message' => '内部服务器错误！','status_code'=>500],500);
                break;
            case  'Symfony\Component\HttpKernel\Exception\ConflictHttpException':
                return response()->json(['message' => '内部服务器错误！','status_code'=>500],500);
                break;
        }

        return parent::render($request, $exception);
    }

    /**
     * 将认证相关的异常转换为未认证的响应（401）
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Auth\AuthenticationException  $exception
     * @return \Illuminate\Http\Response
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson()) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        return redirect()->guest(route('login'));
    }
}
