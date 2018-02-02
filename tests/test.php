<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/31
 * Time: 14:27
 */


define('APP_PATH', dirname(__DIR__));

//create yaf app:; 创建YAFAPP
$app = new Yaf_Application(APP_PATH.'/conf/app.ini');

$app->getDispatcher()->dispatch(new Yaf_Request_Simple());

/*$options=[
    'cost'=>9,
];
$pas= password_hash("demo",PASSWORD_BCRYPT,$options);
echo strlen($pas);*/

/*$orm=new Orm("users");
$user=$orm->select('id AS uid,email');
var_dump($user);*/

$user=UsersModel::where('email','xiongcha1o@eastmoney.com')->select();
var_dump($user);
if($user){
    echo "[]is true";
}

/*$uid=$HASH=\Util\Hashids::getInstance()->encode(1);
echo $uid.PHP_EOL;
var_dump(\Util\Hashids::getInstance()->decode($uid."2"));*/


