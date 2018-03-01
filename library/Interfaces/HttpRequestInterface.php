<?php
/**
 * Created by Administrator.
 * Author: Administrator
 * Date: 2018/2/23
 */

namespace Interfaces;


interface HttpRequestInterface
{
    /**
     * 设置cookie(可选)
     * @param string $cookie
     * @return mixed
     */
    public function set_cookie($cookie = '');

    /**
     * 设置用户浏览器信息(可选)
     * @param string $useragent
     * @return mixed
     */
    public function set_useragent($useragent = '');

    /**
     * 设置头部伪造IP(可选)，对某些依靠头部信息判断ip的网站有效
     * @param string $ip ： 需要伪造的ip
     * @return mixed
     */
    public function set_forgeip($ip);

    /**
     * 添加请求头部信息
     * @param $k
     * @param string $v
     * @return mixed
     */
    public function set_header($k,$v = '');

    /**
     * 设置超时时间(可选)
     * @param $sec
     * @return mixed
     */
    public function set_timeout($sec);

    /**
     * 设置代理(可选)，如对方限制了ip访问或需要隐藏真实ip
     * @param string $host	：代理服务器（ip或者域名）
     * @param int	 $port	：代理服务端口
     * @param string $user	：用户名（如果有）
     * @param string $pass	：用户密码（如果有）
     */
    public function set_proxy($host,$port = '',$user = '',$pass = '');

    /**
     * 请求url
     * @param string $url		: 请求的地址
     * @param <array|string> $postdata :该项如果填写则为POST方式，否则为GET方式;如需上传文件,需在文件路径前加上@符号
     * @param string $referer	: 来路地址，用于伪造来路
     */
    public function request($url,$postdata = '',$referer='');

    /**
     * 获取响应的所有信息
     */
    public function get_response();

    /**
     * 获取响应的header信息
     * @param string $key	: (可选)header头部信息标示如获取Set-Cookie
     * @return string
     */
    public function get_header($key = '');

    /**
     * 获取响应的cookie
     * @param boolean $assoc : 选择true将返回数组否则返回字符串
     */
    public function get_cookie($assoc = false);

    /**
     * 获取响应的页面数据即页面代码
     */
    public function get_data();
    /**
     * 获取响应的网页编码
     */
    public function get_charset();

    /**
     * 获取连接资源的信息（返回数组）
     */
    public function get_info();

    /**
     * 获取响应的网页状态码 (注：200为正常响应)
     */
    public function get_statcode();

    /**
     * @param  $method :方法
     * 获取请求方法
     */
    public function  set_method($method);

    /**
     * 获取请求方法
     */
    public function  get_method();

    /**
     * 获取请求时间
     */
    public function get_requesttime();

}