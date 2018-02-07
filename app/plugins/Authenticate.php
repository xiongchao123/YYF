<?php

class AuthenticatePlugin extends Yaf_Plugin_Abstract
{
    //在路由之前触发，这个是7个事件中, 最早的一个. 但是一些全局自定的工作, 还是应该放在Bootstrap中去完成
    public function routerStartup(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response)
    {
        //进行Auth认证
        $filters = [];
        if (file_exists(APP_PATH . "/conf/AuthFilter.php")) {
            $filters = require APP_PATH . "/conf/AuthFilter.php";
        }
        //请求的/module/controller/action
        $uri = $request->getRequestUri();
        if (!in_array($uri, $filters)) {
            //判断当前用户是否认证
            $config = Config::get('session');
            if ($sid = Cookie::get($config['cookie']['name'])) {
                if(isset($_SESSION['uid'])){
                    $uid=\Util\Hashids::getInstance()->decode($_SESSION['uid']);
                    if($uid && UsersModel::where("id",$uid[0])->select("id")){
                        return;
                    }
                }
            }
            $response->setRedirect("/login");
        }
    }

    //路由结束之后触发，此时路由一定正确完成, 否则这个事件不会触发
    public function routerShutdown(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response)
    {

    }
}