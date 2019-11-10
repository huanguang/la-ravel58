<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Encore\Admin\Config\Config;
use Illuminate\Support\Facades\Schema;
use Laravel\Passport\Bridge\PersonalAccessGrant;
use League\OAuth2\Server\AuthorizationServer;
class AppServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $table = config('admin.extensions.config.table', 'admin_config');
        if (Schema::hasTable($table)) {
            Config::load();
        }
        app('api.exception')->register(function (\Exception $exception) {
            $request = \Illuminate\Http\Request::capture();
            return app('App\Exceptions\Handler')->render($request, $exception);
        });
        //修改token有效期
        $this->app->get(AuthorizationServer::class)
            ->enableGrantType(new PersonalAccessGrant(), new \DateInterval('PT2S'));
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //接管异常
        \API::error(function (\Illuminate\Validation\ValidationException $exception){
            $data =$exception->validator->getMessageBag();
            $msg = collect($data)->first();
            if(is_array($msg)){
                $msg = $msg[0];
            }
            return response()->json(['message'=>$msg,'status_code'=>422], 200);
        });
        \API::error(function (\Dingo\Api\Exception\ValidationHttpException $exception){
            $errors = $exception->getErrors();
            return response()->json(['message'=>$errors->first(),'status_code'=>422], 200);
        });
    }
}
