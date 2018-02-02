<?php

/**
 * YYF - A simple, secure, and efficient PHP RESTful Framework.
 *
 * @link https://github.com/YunYinORG/YYF/
 *
 * @license Apache2.0
 * @copyright 2015-2017 NewFuture@yunyin.org
 */

namespace Interfaces;

/**
 * PHP53兼容
 */
interface JsonSerializable
{
    public function jsonSerialize();
}
