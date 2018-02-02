<?php

class CsrfTokenPlugin extends Yaf_Plugin_Abstract
{
    //在路由之前触发，这个是7个事件中, 最早的一个. 但是一些全局自定的工作, 还是应该放在Bootstrap中去完成
    public function routerStartup(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response)
    {
        //GET请求通行
        if (!$request->isGet()) {
            //进行Auth认证
            $filters = [];
            if (file_exists(APP_PATH . "/conf/CsrfFilter.php")) {
                $filters = require APP_PATH . "/conf/CsrfFilter.php";
            }
            //请求的/module/controller/action
            $uri = $request->getRequestUri();
            if (!in_array($uri, $filters)) {
                $method = $request->getMethod();
                if ($method !== "POST") {
                    $inputs = file_get_contents("php://input");
                    parse_str($inputs, $_POST);
                }
                if (!$token = ($_POST['_token'] ?? null)) {
                    $token = $request->getServer("HTTP_X_CSRF_TOKEN", null);
                }
                if (!$token || !Session::verifyToken($token)) {
                    //响应服务端500
                    header('HTTP/1.1 500 Internal Server Error');
                    echo "This $method request is invalid";
                    exit;
                }
            }
        }
    }

    //路由结束之后触发，此时路由一定正确完成, 否则这个事件不会触发
    public function routerShutdown(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response)
    {

    }
}