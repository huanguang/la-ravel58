<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
use App\User;
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
$api = app('Dingo\Api\Routing\Router');
$api->version('v1',['middleware' => 'api.throttle', 'limit' => 100, 'expires' => 5], function ($api) {
    $api->group(['middleware' => 'auth:api'], function($api) {
        $api->post('aaa',function (Request $request){
            throw new Symfony\Component\HttpKernel\Exception\ConflictHttpException('');
        });//app首页
    });

});
//Route::prefix('aaa')->middleware('client.credentials')->group(function(){
//    //$customer_id = auth('api')->user()->id;
//    //dump($customer_id);die;
//});

