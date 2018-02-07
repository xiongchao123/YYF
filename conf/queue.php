<?php

//queue config

return [
    'driver' => 'redis',
    'host'  =>  '10.10.83.175',
    'port'  =>  6378,
    'password'  =>  null,
    'database'  =>  5,     // default 0
    'prefix'    => 'yaf',  //default resque
    'queue'     => 'default',   //default queue
];