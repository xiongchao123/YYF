<?php

namespace Resque;

use Resque\Resque\ResqueRedis;

class Queue
{
    protected static $config = null;
    protected static $dsn = null;
    protected static $database = 0;
    protected static $queue = null;
    protected static $prefix = null;

    /**
     * load config
     */
    private static function start()
    {
        if (!self::$config) {
            self::$config = require_once APP_PATH . '/conf/queue.php';
        }
        if (!self::$dsn) {
            $host = self::$config['host'] ?? '127.0.0.1';
            $port = self::$config['port'] ?? 6379;
            $password = self::$config['password'] ?? null;
            self::$dsn = "redis://:$password@$host:$port";
        }
        if (!self::$database) {
            self::$database = self::$config['database'] ?? 0;
        }
        if (!self::$queue) {
            self::$queue = self::$config['queue'];
        }
        if (!self::$prefix) {
            self::$prefix = self::$config['prefix'] ?? null;
        }
    }

    /**
     * set queue config
     * @param $host
     * @param $port
     * @param null $password
     * @param int $database
     */
    public static function setConfig($host, $port, $password = null, $database = 0)
    {
        self::$dsn = "redis://:$password@$host:$port";
        self::$database = $database;
        self::start();
    }

    /**
     * @param $class
     * @param null $args
     * @param bool $trackStatus
     * @param null $queue
     * @return bool|string  jobId
     */
    public static function push($class, $args = null, $trackStatus = false, $queue = null)
    {
        self::start();
        Resque::setBackend(self::$dsn, self::$database);
        if (self::$prefix) {
            ResqueRedis::prefix(self::$prefix);
        }
        if (!$queue) {
            $queue = self::$queue;
        }
        return Resque::enqueue($queue, $class, $args, $trackStatus);
    }
}