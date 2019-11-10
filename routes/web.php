<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
use App\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
Route::get('/,', function () {
    echo  phpinfo();die;
    $status_time = microtime(true);
    $user = DB::table('users')->first();
    dump(microtime(true)-$status_time);die;
    $users = User::create(['name'=>'qwerasdzxc','email'=>'qwerasdzxc@qq.com','password'=>bcrypt('123456')]);
    $redis = new Redis();
    $redis->connect('127.0.0.1','6379');
    $redis->set('13178888554','123456789');
    $data = $redis->get('13178888554');
//    $redis->lPush('16620078599','1');
//    $redis->lPush('16620078599','2');
//    $redis->lPush('16620078599','3');
    //$redis->rPush('16620078599','右侧加入');
    //$redis->lPop('16620078599');//左侧弹出
    //$redis->rPop('16620078599');//右侧弹出
//    $num = $redis->lLen('16620078599'); 获取长度
//    $data = $redis->lRange('16620078599',0,-1);
//    echo $redis->hset('hash', 'cat', 'cat');echo '<br>';   // 1
//    echo $redis->hset('hash', 'cat', 'cat');echo '<br>';   // 0
//    echo $redis->hset('hash', 'cat', 'cat1');echo '<br>';   // 0
//    echo $redis->hset('hash', 'dog', 'dog');echo '<br>';   // 1
//    echo $redis->hset('hash', 'bird', 'bird');echo '<br>';   // 1
//    echo $redis->hset('hash', 'monkey', 'monkey');echo '<br>';   // 1
//    echo $redis->hGet('hash','cat');
//    $data = $redis->hKeys('hash');
//    $data = $redis->hVals('hash');
//    $data = $redis->hGetAll('hash');
//    $data = $redis->hLen('hash');
//    echo $redis->hDel('hash','asdas');
//    echo $redis->hDel('hash','cat');

//    echo $redis->sadd('set', 'cat');echo '<br>';         // 1
//    echo $redis->sadd('set', 'cat');echo '<br>';         // 0
//    echo $redis->sadd('set', 'dog');echo '<br>';        // 1
//    echo $redis->sadd('set', 'rabbit');echo '<br>';     // 1
//    echo $redis->sadd('set', 'bear');echo '<br>';      // 1
//    echo $redis->sadd('set', 'horse');echo '<br>';    // 1
//    $data = $redis->sMembers('set');
//    var_dump($redis->sIsMember('set','cat'));
//    var_dump($redis->sIsMember('set','cats'));
//    dump($redis->sCard('set'));
//    echo $redis->sPop('set');
//    dump($data);die;

//    $redis->sadd('set', 'horse');
//    $redis->sadd('set', 'cat');
//    $redis->sadd('set', 'dog');
//    $redis->sadd('set', 'bird');
//    $redis->sadd('set2', 'fish');
//    $redis->sadd('set2', 'dog');
//    $redis->sadd('set2', 'bird');
//    dump($redis->sMembers('set'));
//    dump($redis->sMembers('set2'));
//    dump($redis->sInter('set','set2'));
//    $redis->sInterStore('set3','set','set2');
//    dump($redis->sMembers('set3'));
//    dump($redis->sUnion('set','set2'));
//    dump($redis->sDiff('set','set2'));
//    $redis->sDiffStore('set4','set','set2');
//    dump($redis->sMembers('set4'));

    echo $redis->zadd('set5', 1, 'cat');echo '<br>';      // 1
    echo $redis->zadd('set5', 2, 'dog');echo '<br>';    // 1
    echo $redis->zadd('set5', 3, 'fish');echo '<br>';    // 1
    echo $redis->zadd('set5', 4, 'dog');echo '<br>';    // 0
    echo $redis->zadd('set5', 4, 'bird');echo '<br>';    // 1
    dump($redis->zRange('set5',0,-1));
    dump($redis->zRange('set5',0,-1,true));
    dump($redis->zScore('set5','dog'));
    dump($redis->zCard('set5'));
    dump($redis->zCount('set5',3,5));
    dump($redis->zRangeByScore('set5',3,5));
    dump($redis->zRangeByScore('set5',3,5,['withscores'=>true]));
    dump($redis->zRevRange('set5',1,2));
    dump($redis->zRevRange('set5',1,2,true));
    //有序集合中指定值的socre增加
    dump($redis->zscore('set5', 'dog'));echo '<br>';
    // 4
    $redis->zincrby('set5', 2, 'dog');
    dump($redis->zscore('set5', 'dog'));echo '<br>';
    dump($redis->zrange('set5', 0, -1, true));echo '<br>';
// Array ( [fish] => 3 [bird] => 4 [dog] => 6 )
    dump($redis->zremrangebyscore('set5', 3, 4));echo '<br>';
    // 2
    dump($redis->zrange('set5', 0, -1, true));echo '<br>';
    die;
    echo "Server is running: " . $redis->ping();die;
    return view('welcome');
});
