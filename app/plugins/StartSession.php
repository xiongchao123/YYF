<?php
/** 
 * 插件类定义
 * SessionPlugin.php
 */
class StartSessionPlugin extends Yaf_Plugin_Abstract {
    //在路由之前触发，这个是7个事件中, 最早的一个. 但是一些全局自定的工作, 还是应该放在Bootstrap中去完成
    public function routerStartup(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response) {
        //初始化session
        $config=Config::get("session");
        switch ($config['driver']){
            case "redis":
                ini_set("session.save_handler","redis");
                session_save_path($config['redis']);
                break;
            case "file" :
                ini_set("session.save_handler","file");
                session_save_path($config['files']);
                break;
            //TODO memcache
            default:
                throw new Exception('未定义方式'.$config['driver']);
        }
        if($sid = Cookie::get($config['cookie']['name'])){
            $sid =Session::decryptToken($sid);
        }
        Session::start($sid,[
            'name'=>$config['cookie']['name'],
            'gc_maxlifetime'=>(int)$config['cookie']['expire'],
            'cookie_lifetime'=>(int)$config['cookie']['expire'],
            'cookie_domain' => $config['cookie']['domain']
        ]);
        //XSRF-TOKEN
        // Cookie::set("X-XSRF-TOKEN",Session::token(),"",(int)$config['cookie']['expire']);
       Cookie::set($config['cookie']['name'],Session::token(),"",(int)$config['cookie']['expire']);
    }
    //路由结束之后触发，此时路由一定正确完成, 否则这个事件不会触发
    public function routerShutdown(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response) {
    }

    //分发循环结束之后触发，此时表示所有的业务逻辑都已经运行完成, 但是响应还没有发送
    public function dispatchLoopShutdown(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response) {

    }

    //响应之前
    public function preResponse(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response) {

    }
}
