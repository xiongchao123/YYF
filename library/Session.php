<?php
/**
 * YYF - A simple, secure, and efficient PHP RESTful Framework.
 *
 * @link https://github.com/YunYinORG/YYF/
 *
 * @license Apache2.0
 * @copyright 2015-2017 NewFuture@yunyin.org
 */

/**
 *Session seession管理
 *
 * @author NewFuture
 */
class Session
{
    private static $_id;


    /**
     * @param string $id session_id
     * @param array $options
     *
     * @return string [session id]
     */
    public static function start($id = null, array $options = [])
    {
        if (!$sid = self::$_id) {
            if (($sid = $id) || Input::I('SERVER.HTTP_SESSION_ID', $sid, 'ctype_alnum')) {
                session_id($sid);
                session_start($options);
            } else {
                session_start($options);
                $sid = session_id();
            }
            self::$_id = $sid;
        }
        return $sid;
    }

    /**
     * @return string token
     */
    public static function token()
    {
        return Aes::encrypt(self::start(), Config::getSecret("encrypt.key_token"), true);
    }

    /**
     * @param $token
     * @return string
     */
    public static function decryptToken($token)
    {
        return Aes::decrypt($token, Config::getSecret("encrypt.key_token"), true);
    }

    /**
     * @param $token
     * @return bool
     */
    public static function verifyToken($token)
    {
        return self::start() === self::decryptToken($token);
        /* $decryptToken=self::decryptToken($token);
         if(self::start() === $decryptToken)
             return true;
         if(self::start() === json_decode($decryptToken,true))
             return true;
         return false;*/
    }

    /**
     * 设置session
     *
     * @param string $name 键值
     * @param mixed $value 对应值
     */
    public static function set($name, $value)
    {
        self::start();
        return $_SESSION[$name] = $value;
    }

    /**
     * 读取
     *
     * @param string $name 键值
     */
    public static function get($name)
    {
        self::start();
        return isset($_SESSION[$name]) ? $_SESSION[$name] : null;
    }

    /**
     * 删除
     *
     * @param string $name 键值
     */
    public static function del($name)
    {
        self::start();
        unset($_SESSION[$name]);
    }

    /**
     * 清空session
     */
    public static function flush()
    {
        self::start();
        unset($_SESSION);
        session_unset();
        session_destroy();
        self::$_id = null;
    }
}
