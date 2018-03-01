<?php

namespace Util;

use Interfaces\HttpRequestInterface;
use Util\Cache\Exception\InvalidArgumentException;

class DockerApi
{
    const DOCKER_URL = 'http://10.10.83.231:10030';
//    const DOCKER_URL = 'http://172.30.72.78:10030';

    const CREATE_URL = '/create';
    const CONTAINER_STATUS_URL = '/statusContainer';
    const GET_ONE_CONTAINER_URL = '/getOneContainer';
    const GET_ALL_CONTAINER_URL = '/getAllContainer';
    const START_CONTAINER_URL = '/startContainer';
    const STOP_CONTAINER_URL = '/stopContainer';
    const DELETE_CONTAINER_URL = '/delContainer';
    const CREATE_CONTAINER_PORT = '/addContainerPort';
    const RESTART_CONTAINER_URL = '/restartContainer';
    const ADD_URL_PORT=10038;
    const HTTP_TIMEOUT = 60;
    const SUCCESS_CODE=[
        '202'=>'请求成功',
        '210'=>'新增容器端口映射成功',
    ];
    const ERR_CODE=[
        '409'=>'容器不存在',
        '1001'=>'容器创建失败',
        '1002'=>'参数错误',
        '1003'=>'请求方法错误',
        '1004'=>'命令运行错误',
        '10040'=>'HTTP请求失败或资源不存在',
        '10041'=>'HTTP Auth认证失败',
    ];
    const CLIENT_USER = "EastMoneyDEVCLOUD";
    const CLIENT_KEY = "DevCloud/Auth_201710121048";
    private $httpRequest;
    private $api_url;

    public function __construct(HttpRequestInterface $httpRequest)
    {
        $this->api_url = self::DOCKER_URL;
        $this->httpRequest = $httpRequest;
        $this->httpRequest->set_header('Authorization','Basic '.base64_encode(self::CLIENT_USER.':'.self::CLIENT_KEY));
        $this->httpRequest->set_timeout(self::HTTP_TIMEOUT);
    }

    public function __destruct()
    {
        // TODO: Implement __destruct() method.
    }

    /**
     * create container
     * @param array $data
     * @return bool|string
     */
    public function create($data)
    {
        if(isset($data['name']) && isset($data['port']) && isset($data['image'])){
            $param=[
                'name'=>$data['name'],
                'port'=>$data['port'],
                'image'=>$data['image'],
                'des'=>$data['des'] ?? ''
            ];

            $this->httpRequest->request($this->api_url . self::CREATE_URL,$this->formatMessage($param));
            $json=$this->httpRequest->get_data();
//            var_dump($json);exit();
            return $this->validateResult(__FUNCTION__,$json);
        }
        return false;
    }

    /**
     * 容器状态监测
     * @param $cid : 容器ID
     * @return mixed
     */
    public function statusContainer($cid)
    {
        $param=['id'=>$cid];
        $this->httpRequest->request($this->api_url . self::CONTAINER_STATUS_URL,$this->formatMessage($param));
        $json=$this->httpRequest->get_data();
        return $this->validateResult(__FUNCTION__,$json);
    }

    /**
     * @param $cid
     * @return bool|string : 容器ID
     */
    public function getOneContainer($cid)
    {
        $param=['id'=>$cid];
        $this->httpRequest->request($this->api_url . self::GET_ONE_CONTAINER_URL,$this->formatMessage($param));
        $json=$this->httpRequest->get_data();
        return $this->validateResult(__FUNCTION__,$json);
    }

    /**
     * 获取所有容器
     * @return bool|string
     */
    public function getAllContainer()
    {
        $this->httpRequest->set_method('POST');
        $this->httpRequest->request($this->api_url . self::GET_ALL_CONTAINER_URL);
        $json=$this->httpRequest->get_data();
        return $this->validateResult(__FUNCTION__,$json);
    }

    /**
     * 启动容器
     * @param string $cid
     * @return bool|string
     */
    public function startContainer($cid){
        $param=['id'=>$cid];
        $this->httpRequest->request($this->api_url . self::START_CONTAINER_URL,$this->formatMessage($param));
        $json=$this->httpRequest->get_data();
        return $this->validateResult(__FUNCTION__,$json);
    }

    /**
     * 停止容器
     * @param string $cid
     * @return bool|string
     */
    public function stopContainer($cid){
        $param=['id'=>$cid];
        $this->httpRequest->request($this->api_url . self::STOP_CONTAINER_URL,$this->formatMessage($param));
        $json=$this->httpRequest->get_data();
        return $this->validateResult(__FUNCTION__,$json);
    }

    /**
     * 删除容器
     * @param string $cid
     * @return bool|string
     */
    public function delContainer($cid){
        $param=['id'=>$cid];
        \Logger::error($param);
        $this->httpRequest->request($this->api_url . self::DELETE_CONTAINER_URL,$this->formatMessage($param));
        $json=$this->httpRequest->get_data();
        \Logger::error($json);
        return $this->validateResult(__FUNCTION__,$json);
    }


    /**
     * create container port
     * @param $url string 宿主机IP地址
     * @param $data array  ['address'=>'','port'=>'{}']
     * @return bool|string
     */
    public function addContainerPort($url,$data){
        $this->httpRequest->request($url.':'.self::ADD_URL_PORT . self::CREATE_CONTAINER_PORT,$this->formatMessage($data));
        $json=$this->httpRequest->get_data();
        return $this->validateResult(__FUNCTION__,$json);
    }

    /**
     * 重启容器
     * @param string $cid
     * @return bool|string
     */
    public function restartContainer($cid){
        $param=['id'=>$cid];
        $this->httpRequest->request($this->api_url . self::RESTART_CONTAINER_URL,$this->formatMessage($param));
        $json=$this->httpRequest->get_data();
        return $this->validateResult(__FUNCTION__,$json);
    }

    /**
     * @param $message
     * @return string
     */
    private function formatMessage($message){
        if (is_array($message)) {
            return $this->array2string($message);
        }
//        elseif ($message instanceof Jsonable) {
//            return $this->array2string((array)$message);
//        }
//        elseif ($message instanceof Arrayable) {
//            return $this->array2string($message->toArray());
//        }
        return $message;
    }

    /**
     * @param $array
     * @return string
     */
    private function array2string($array){
        $string = [];
        if($array && is_array($array)){
            foreach ($array as $key=> $value){
                $string[] = $key.'='.$value;
            }
        }
        return implode('&',$string);
    }

    /**
     * @param $func
     * @param $json
     * @return string|bool
     */
    private function validateResult($func,$json){
        if(is_object(json_decode($json)))
            return $json;
        if(array_key_exists($json,self::SUCCESS_CODE))
            return true;
        try{
            if(array_key_exists($json,self::ERR_CODE)){
                \Logger::emergency('请求方法:'.$func.'失败,返回错误:'.self::ERR_CODE[$json]);
            }else{
                \Logger::emergency('请求方法:'.$func.'失败,返回错误:未知错误码'.$json);
            }
        }catch (\ErrorException $e){
            \Logger::error('请求方法:'.$func.'失败,返回错误:'.$json.',记录日志发生异常,异常信息:'.$e->getMessage());
        }catch (InvalidArgumentException $e){
            \Logger::error('请求方法:'.$func.'失败,返回错误:'.$json.',记录日志发生异常,异常信息:'.$e->getMessage());
        }
        return false;

    }


}