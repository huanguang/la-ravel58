<?php
use App\User;
//生成商品编号
function get_product_sn($brand_id = 00)
{
    //当品牌供应商id不足2位时前面自动补0
    $brand_id = sprintf("%02d", $brand_id);
    //品牌供应商id+当前日期+四位随机数
    $product_sn = $brand_id . mt_rand(1000, 9999);
    return $product_sn;
}

//生成订单编号
function get_order_sn()
{
    //当前时间+4位随机数
    $order_sn = date('YmdHis', time()) . mt_rand(1000, 9999);
    return $order_sn;
}

//生成提现交易流水号
function get_req_sn($type)
{
    $req_sn = $type . date('Ymd', time()) . mt_rand(10000000, 99999999);
    return $req_sn;
}

//图片链接前拼接域名
function set_img_domain($img_url = "")
{
    return config('hosts') . '/' . $img_url;
}

//替换富文本编辑器中的图片链接
function get_img_thumb($content = "")
{
    $pregRule = "/<[img|IMG].*?src=[\'|\"](.*?(?:[\.jpg|\.jpeg|\.png|\.gif|\.bmp]))[\'|\"].*?[\/]?>/";
    $content = preg_replace($pregRule, '<img src="${1}" style="">', $content);
    $pregRule2 = "/<p><[img|IMG].*?src/";
    $content = preg_replace($pregRule2, '<p style="margin: 0px;padding: 0px;"><img src', $content);
    return $content;
}

function NoRand($begin = 0, $end = 1000000, $limit = 20)
{

    $rand_array = range($begin, $end);

    shuffle($rand_array);//调用现成的数组随机排列函数

    return array_slice($rand_array, 0, $limit);//截取前$limit个

}

//生产guid
function guid()
{
    if (function_exists('com_create_guid')) {
        return com_create_guid();
    } else {
        mt_srand((double)microtime() * 10000);//optional for php 4.2.0 and up.
        $charid = strtoupper(md5(uniqid(rand(), true)));
        $hyphen = chr(45);// "-"
        $uuid = chr(123)// "{"
            . substr($charid, 0, 8) . $hyphen
            . substr($charid, 8, 4) . $hyphen
            . substr($charid, 12, 4) . $hyphen
            . substr($charid, 16, 4) . $hyphen
            . substr($charid, 20, 12)
            . chr(125);// "}"
        return $uuid;
    }
}


//快递查询
function get_express($express, $com = 'shunfeng')
{
    if (empty($express)) {
        return '';
    }


    $cache_key = 'express' . $express . $com;
    $data = \Illuminate\Support\Facades\Cache::remember($cache_key, 10, function () use ($express, $com) {

        //参数设置
        $key = config('express.key') ?? 'VfEWkUgD6642';                        //客户授权key
        $customer = config('express.customer') ?? '26C6DD57217BC25BB4C8F092D9BCF3BC';                    //查询公司编号
        $param = array(
            'com' => $com,            //快递公司编码
            'num' => $express,    //快递单号
            'phone' => '',                //手机号
            'from' => '',                //出发地城市
            'to' => '',                    //目的地城市
            'resultv2' => '1'            //开启行政区域解析
        );
        //请求参数
        $post_data = array();
        $post_data["customer"] = $customer;
        $post_data["param"] = json_encode($param);
        $sign = md5($post_data["param"] . $key . $post_data["customer"]);
        $post_data["sign"] = strtoupper($sign);

        $url = 'http://poll.kuaidi100.com/poll/query.do';    //实时查询请求地址

        $params = "";
        foreach ($post_data as $k => $v) {
            $params .= "$k=" . urlencode($v) . "&";        //默认UTF-8编码格式
        }
        $post_data = substr($params, 0, -1);

        //发送post请求
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);
        $data = str_replace("\"", '"', $result);
        $data = json_decode($data);
//        dd($data);die;
        return $data;
    });

    return $data;
}

/**
 * 求两个日期之间相差的天数
 * (针对1970年1月1日之后，求之前可以采用泰勒公式)
 * @param string $day1
 * @param string $day2
 * @return number
 */
function diffBetweenTwoDays($day1, $day2)
{
    $second1 = strtotime($day1);
    $second2 = strtotime($day2);

    if ($second1 < $second2) {
        $tmp = $second2;
        $second2 = $second1;
        $second1 = $tmp;
    }
    return ($second1 - $second2) / 86400;
}

/**
 * 计算天数和计划金额是否相符合
 * @param int $day 天数
 * @param string $money 账单金额
 * @param string $moneyPlan 计划金额
 * @param string $bankCardRate 银行卡信息费率信息
 */
function calculationDayMoney($day, $money, $moneyPlan, $bankCardRate)
{
    //计算当前的计划金额和天数是否与对应的计划金额
    if ($day && $money && $moneyPlan) {
        //获取单笔手续费和费率
        $rate = config('kft.rate');//刷卡费率
        $ServiceCharge = config('kft.Single_rate');//单笔刷卡手续费
        //根据天数获取判断能不能还完账单金额
        $arr = [];
        $newMoney = 0;
        for ($i = 0; $i < $day; $i++) {
            //获取每天需要还款的金额   还款金额-(还款金额*费率)-(还款笔数*每笔手续费+刷卡笔数*每笔手续费)

        }

    } else {
        return false;
    }
}

/**
 * 根据计划金额获取需要最少需要多少钱才能还完
 * @param $money
 * @param $moneyPlan
 * @param $bankCardRate
 */
function getMoneyDay($money, $moneyPlan, $bankCardRate)
{

}

/**
 * 获取每天还款金额
 * @param $repayment_money
 * @param $dayNum
 * @param $KftBankCardInfos
 * @param $nums
 * @return array
 */
function DailyRepaymentMoney($repayment_money, $dayNum, $KftBankCardInfos, $nums, $dayArr, $statement_money, $type = 1,$arr,$rate,$single_payment)
{
    $dayArrNew = [];
    if(!empty($dayArr)){
        for ($i= 0;$i<count($dayArr);$i++){
            $dayArrNew[] = strtotime($dayArr[$i]);
        }
    }
    $dayArr = $dayArrNew;
    asort($dayArr);
    $dayArr = array_values($dayArr);
    $dayMoneyArr = [];
//    if ($type == 1) {
//        //快付通
//        $rate = $KftBankCardInfos->rate ?? config('kft.rate');
//        $single_payment = $KftBankCardInfos->single_payment ?? config('kft.Single_rate');
//        if($repayment_money > 1000){
//            $rate = $KftBankCardInfos->rate ?? config('kft.rate_min');
//            $single_payment = $KftBankCardInfos->single_payment ?? config('kft.Single_rate_min');
//        }
//
//    } else {
//        $rate = $KftBankCardInfos->rate ?? config('new_pay.rate');
//        $single_payment = $KftBankCardInfos->single_payment ?? config('new_pay.Single_rate');
//    }
    for ($i = 0; $i < $dayNum; $i++) {
        //print_r($KftBankCardInfos['rate']);die;

        $data['repayment_money'] = $arr[$i] - ($arr[$i] * ($rate / 100)) - ($single_payment);
        $data['pay_by_card_money'] = ($arr[$i]) * 100;
        $data['repayment_money'] = ($data['repayment_money']) * 100;
        $data['date'] = date('Y-m-d',$dayArr[$i]);
        $data['date_time'] = $dayArr[$i];
        //计算每天刷卡金额

        $dayMoneyArr[] = $data;
    }
    return $dayMoneyArr;
}

/**
 * 获取每天还款金额
 * @param $repayment_money
 * @param $dayNum
 * @param $KftBankCardInfos
 * @param $nums
 * @return array
 */
function DailyPayByCardMoney($repayment_money, $dayNum, $KftBankCardInfos, $nums, $dayArr, $statement_money)
{
    asort($dayArr);
    $dayArr = array_values($dayArr);
    $dayMoneyArr[] = $repayment_money;
    $dayMoneyCount = $repayment_money;
    $dayMoneyCount2 = 0;
    $skMoney = $repayment_money;
    for ($i = 0; $i < $dayNum; $i++) {
        //计算每天还款金额
//        $data['repayment_money'] = $skMoney = $skMoney - ($skMoney * ($KftBankCardInfos['rate'] / 100)) -($KftBankCardInfos['single_payment'] * $nums);
//        $data['date'] = $dayArr[$i];
        $dayMoneyArr[] = $skMoney = $skMoney - ($skMoney * ($KftBankCardInfos['rate'] / 100)) - ($KftBankCardInfos['single_payment'] * $nums);
        $dayMoneyCount += $skMoney;
        $dayMoneyCount2 += ($skMoney * ($KftBankCardInfos['rate'] / 100)) + ($KftBankCardInfos['single_payment'] * $nums);
    }
    print_r($dayMoneyArr);
    die;
    return $dayMoneyArr;
}

/**
 * 获取每天刷卡的金额
 * @param $div
 * @param $total
 * @param $type
 * @return array
 */
function randomDivInts($div, $total, $type = 1)
{

    $remain = $total;
    $a = [];
    for ($i = 1; $i < $div + 1; $i++) {
        $max = $remain - (config('wisdom.min_money') * 100 * ($div - $i));
        $min = config('wisdom.min_money') * 100;
//        if($type == 1){
//            //平均分配，上下浮动30%
//            $day_money = $total/$div;
//
//            $max = $remain - (($day_money * 1.3) *($div-$i));
//            $min = $day_money * 0.7;
//        }
        if ($i == $div) {
            $e = $remain;
        } else {
            $e = rand($min, $max);
        }
        $remain = $remain - $e;
        $a[] = $e;
    }
//    $max_sum=($div-1)*$div/2;
//    $p=$div; $min=config('wisdom.min_money');
//    $a=array();
//    for($i=0; $i<$div-1; $i++){
//        $max=($remain-$max_sum)/($div-$i);
//        if($max > config('wisdom.max_money')){
//            $max=config('wisdom.max_money');
//        }
//        $e=rand($min,$max);
//        $min=$e+1; $max_sum-=--$p;
//        $remain-=$e;
//        $a[$e]=true;
//    }
//    $a=array_keys($a);
//    $a[]=$remain;
    return $a;
}

/**
 * 将金额拆分成不等份
 * @param $div
 * @param $total
 * @param $type
 * @return array
 */
function randomDivInt($div, $total, $type = 1)
{
    if ($total < 2000 && $total > 1980) {
        $r = 0.99;
    } elseif ($total < 1980 && $total > 1950) {
        $r = 0.98;
    } elseif ($total < 1950 && $total > 1900) {
        $r = 0.97;
    } elseif ($total < 1900 && $total > 1800) {
        $r = 0.95;
    } elseif ($total < 1800) {
        $r = 0.85;
    } else {
        $r = 0.7;
    }
    if ($div == 3 && $total < 30000000) {
        //判断如果小于3000的话，不能大于1000单笔
        $max = $total / $div;
        $min = ($total / $div) * $r;
        if($total - $min - $min > 10000000){
            $min = ($total - 10000000)/2;
        }
    } elseif ($div == 2 && $total < 20000000) {
        //判断如果小于2000的话，不能大于1000单笔
        $max = $total / $div;
        $min = ($total / $div) * $r;
        if($total - $min > 10000000){
            $min = $total - 10000000;
        }
    } else {
        $max = 0;
        $min = ($total / $div) * $r;
    }

    $remain = $total;
    $a = [];
    for ($i = 1; $i < $div + 1; $i++) {
        $max = $max > 0 ? $max : $remain - ($div - $i);
        //$min = isset($min) ? $min :config('wisdom.min_money') * 100;
        if ($i == $div) {
            $e = $remain;
        } else {
            $e = rand($min, $max);
        }
        $remain = $remain - $e;
        $a[] = $e;
    }
//    $max_sum=($div-1)*$div/2;
//    $p=$div; $min=config('wisdom.min_money');
//    $a=array();
//    for($i=0; $i<$div-1; $i++){
//        $max=($remain-$max_sum)/($div-$i);
//        if($max > config('wisdom.max_money')){
//            $max=config('wisdom.max_money');
//        }
//        $e=rand($min,$max);
//        $min=$e+1; $max_sum-=--$p;
//        $remain-=$e;
//        $a[$e]=true;
//    }
//    $a=array_keys($a);
//    $a[]=$remain;
    return $a;
}

function pkcs5_pad($text, $blocksize)
{
    $pad = $blocksize - (strlen($text) % $blocksize);
    return $text . str_repeat(chr($pad), $pad);
}

function pkcs5_unpad($text)
{
    $pad = ord($text{strlen($text) - 1});
    if ($pad > strlen($text)) {
        return false;
    }
    return substr($text, 0, -1 * $pad);
}

function des3Encryption($key, $input)
{
    $input = pkcs5_pad($input, 8);
    $td = mcrypt_module_open('tripledes', '', 'ecb', '');
    $iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
    mcrypt_generic_init($td, $key, $iv);
    $en_res = mcrypt_generic($td, $input);
    mcrypt_generic_deinit($td);
    mcrypt_module_close($td);
    return $en_res;
}


////查询终端粉丝数量
//function getTeNum($user_id = 0)
//{
//    $user = new \App\User();
//    //查询所有比自己晚注册的用户
//    $total_user = $user->where('id','>', $user_id)->get()->toArray();
//    //保存子级id
//    $te_user[] = $user_id;
//    //保存子级数量
//    $num = 0;
//    while (count($te_user) != 0) {
//        $two = [];
//        foreach ($total_user as $item) {
//            if(in_array($item['pid'],$te_user) && $item['is_terminal'] == 0){
//                $two[] = $item['id'];
//                $num ++;
//            }
//
//        }
//        $te_user = $two;
//    }
//    return $num;
//}


//查询终端粉丝数量
function getTeNum($user_id = 0)
{
    $user = new \App\User();
    //查子级
    $te_user = $user->where('pid','=', $user_id)->where('is_terminal',0)->select('id')->get()->toArray();
    //保存子级数量
    $num = count($te_user);
    while (count($te_user) != 0) {
        $te_user = $user->whereIn('pid', $te_user)->where('is_terminal',0)->select('id')->get()->toArray();
        $num += count($te_user);
    }
    return $num;
}
//
////查询今日新增终端粉丝数量
//function getNewTeNum($user_id = 0)
//{
//    $user = new \App\User();
//    //查询所有比自己晚注册的用户
//    $total_user = $user->where('id','>', $user_id)->get()->toArray();
//    //保存子级id
//    $te_user[] = $user_id;
//    //保存子级数量
//    $num = 0;
//    while (count($te_user) != 0) {
//        $two = [];
//        foreach ($total_user as $item) {
//            if(in_array($item['pid'],$te_user) && $item['is_terminal'] == 0){
//                $two[] = $item['id'];
//                if(strtotime($item['created_at']) > strtotime(date('Y-m-d'))){
//                    $num ++;
//                }
//            }
//
//        }
//        $te_user = $two;
//    }
//    return $num;
//}


//查询今日新增终端粉丝数量
function getNewTeNum($user_id = 0)
{
    $user = new \App\User();
    $num = 0;
    $te_user_id = [];
    //查子级
    $te_user = $user->where('pid','=', $user_id)->where('is_terminal',0)->get();
    //保存今日新增子级数量
    foreach ($te_user as $item) {
        if(strtotime($item['created_at']) > strtotime(date('Y-m-d'))){
            $num ++;
        }
        $te_user_id[] = $item['id'];
    }
    while (count($te_user) != 0) {
        $te_user = $user->whereIn('pid', $te_user_id)->where('is_terminal',0)->get();
        $te_user_id = [];
        foreach ($te_user as $item) {
            if(strtotime($item['created_at']) > strtotime(date('Y-m-d'))){
                $num ++;
            }
            $te_user_id[] = $item['id'];
        }
    }
    return $num;
}


//查询今日减少的终端粉丝数量
//function getDeTeNum($user_id = 0)
//{
//    $user = new \App\User();
//    //查询所有比自己晚注册的用户
//    $total_user = $user->where('id','>', $user_id)->get()->toArray();
//    //保存子级id
//    $te_user[] = $user_id;
//    //保存子级中今日成为终端的id
//    $de_user = [];
//    //保存子级数量
//    while (count($te_user) != 0) {
//        $two = [];
//        foreach ($total_user as $item) {
//            if(in_array($item['pid'],$te_user)){
//                if($item['is_terminal'] == 0){
//                    $two[] = $item['id'];
//                }elseif(strtotime($item['terminal_at']) > strtotime(date('Y-m-d'))){
//                    $de_user[] = $item['id'];
//                }
//            }
//
//        }
//        $te_user = $two;
//    }
//    $num = count($de_user);
//    foreach ($de_user as $value){
//        $num += getTeNum($value);
//    }
//    return $num;
//}

//查询今日减少的终端粉丝数量
function getDeTeNum($user_id = 0,$num = 0,$type = 1)
{
    if($user_id){
        //判断下级会员是否是终端代理，如果是终端代理，结束分销，不然无限级分销
        $userList = User::where([['pid','=',$user_id]])->select('id','pid','is_terminal')->get();
        if(!empty($userList)){
            foreach ($userList as $item){
                if($item->is_terminal == 1){
                    //断开的
                    //$num = $num + 1;
                    $num = getDeTeNum($item->id,$num+1,2);
                }else{
                    //没断的
                    if($type == 2){
                        $num = getDeTeNum($item->id,$num+1,2);
                    }else{
                        $num = getDeTeNum($item->id,$num,1);
                    }

                }
            }
        }
    }
    return $num;
//    $user = new \App\User();
//    $num = 0;
//    //查子级
//    $te_user = $user->where('pid','=', $user_id)->where('is_terminal',0)->select('id')->get()->toArray();
//    $de_user = $user->where('pid','=', $user_id)->where('is_terminal',1)->get();
//    //保存今日减少子级数量
//    foreach ($de_user as $item) {
//        if(strtotime($item['terminal_at']) > strtotime(date('Y-m-d'))){
//            $num++;
//            $num += getTeNum($item['id']);
//        }
//    }
//    while (count($te_user) != 0) {
//        $te_user = $user->whereIn('pid', $te_user)->where('is_terminal',0)->select('id')->get()->toArray();
//        $de_user = $user->whereIn('pid', $te_user)->where('is_terminal',1)->get();
//        foreach ($de_user as $item) {
//            if(strtotime($item['terminal_at']) > strtotime(date('Y-m-d'))){
//                $num++;
//                $num += getTeNum($item['id']);
//            }
//        }
//    }
    return $num;
}

//查询二级粉丝数量
function getFenNum($user_id = 0)
{
    $user = new \App\User();
    //查询一级粉丝
    $one_user = $user->where('pid','=', $user_id)->select('id')->get()->toArray();
    //查询二级粉丝
    $two_user_num = $user->whereIn('pid', $one_user)->count();
    //粉丝数量
    $num = count($one_user) + $two_user_num;
    return $num;
}

//查询今日新增二级粉丝数量
function getNewFenNum($user_id = 0)
{
    $user = new \App\User();
    //查询一级粉丝
    $one_user = $user->where('pid','=', $user_id)->select('id')->get()->toArray();
    //查询今日一级粉丝新增数量
    $one_user_num = $user->where('pid', $user_id)->where('created_at','>',date('Y-m-d 00:00:00'))->count();
    //查询二级粉丝数量
    $two_user_num = $user->whereIn('pid', $one_user)->where('created_at','>',date('Y-m-d 00:00:00'))->count();
    //粉丝数量
    $num = $one_user_num + $two_user_num;
    return $num;
}

//查询未实名粉丝数量
function getNoRealFenNum($user_id = 0)
{
    $user = new \App\User();
    //查询一级粉丝
    $one_user = $user->where('pid','=', $user_id)->select('id')->get()->toArray();
    //查询一级粉丝数量
    $one_user_num = $user->where('pid', $user_id)->where('is_real_name','!=',3)->count();
    //查询二级粉丝数量
    $two_user_num = $user->whereIn('pid', $one_user)->where('is_real_name','!=',3)->count();
    //粉丝数量
    $num = $one_user_num + $two_user_num;
    return $num;
}

//查询已实名粉丝数量
function getRealFenNum($user_id = 0)
{
    $user = new \App\User();
    //查询一级粉丝
    $one_user = $user->where('pid','=', $user_id)->select('id')->get()->toArray();
    //查询一级粉丝数量
    $one_user_num = $user->where('pid', $user_id)->where('is_real_name','=',3)->count();
    //查询二级粉丝数量
    $two_user_num = $user->whereIn('pid', $one_user)->where('is_real_name','=',3)->count();
    //粉丝数量
    $num = $one_user_num + $two_user_num;
    return $num;
}

//获取指定位数的随机字符串
function getRand($length = 8){
    // 密码字符集，可任意添加你需要的字符
    $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $str = "";
    for ( $i = 0; $i < $length; $i++ )
    {
        $str .= $chars[ mt_rand(0, strlen($chars) - 1) ];
    }
    return $str ;
}

/**
 * 计算还款金额
 */
function jMoney($t = 25,$e = 0.0079,$re = 2,$money = 10000,$bei = 2){
    $k = ($money/$t) + ($money * $e) + ($re * $t);

    //$mmm = ($money * $e) + ($re * $t) + $money;
    $mmm = ($money + $t) / (1 - $e);
    $m = $k;
    $mm = ceil($t/2);

    $arr[] = $m;
    $arr2 = [];
    for ($i = 0; $i<$mm+1;$i++) {

        if($t%2 == 1){
            //直接取中间那天另外计算
            if(intval($t/2) >=$i){
                if ($i > 1) {
                    $m = $m - (($m * $e) + ($re));
                    $arr[] = $m;
                }
                //计算倒数的金额
                $arr2[] = ((($money * $e) + ($re * $t) + $money) / $t) * $bei - $m;
            }

        }else{
            if ($i > 1) {
                $m = $m - (($m * $e) + ($re));
                $arr[] = $m;
            }
            //计算倒数的金额
            $arr2[] = ((($money * $e) + ($re * $t) + $money) / $t) * $bei - $m;
        }

    }


    $new = array_merge($arr2,$arr);
    $new = array_unique($new);

    //计算中间一天的金额

    if($t%2 == 0){
        arsort($new);
        $new = array_values($new);
        unset($new[ceil($t/2)+1]);
    }
    $moneys = 0;
    foreach ($new as $key=>$value){
        $moneys += $value;
    }
    $moneys = $mmm- $moneys;

    $arr3[] = $moneys;

    $new = array_values($new);
    $new = array_merge($new,$arr3);

    arsort($new);
    $new = array_values($new);
    return $new;
}

/**
 * 二维数组去重复
 * @param $arr
 * @param $key
 * @return mixed
 */
function assoc_unique($arr, $key) {

    $tmp_arr = array();

    foreach ($arr as $k => $v) {

        if (in_array($v[$key], $tmp_arr)) {//搜索$v[$key]是否在$tmp_arr数组中存在，若存在返回true

            unset($arr[$k]);

        } else {

            $tmp_arr[] = $v[$key];

        }

    }

    sort($arr); //sort函数对数组进行排序

    return $arr;

}
