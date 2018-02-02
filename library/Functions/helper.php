<?php

if (!function_exists('validate')) {
    function validate($data)
    {
        $result = [];
        foreach ($data as $option => $value) {
            switch ($option) {
                case "email":
                    $result[$option] = filter_var($value, FILTER_VALIDATE_EMAIL);
                    break;
                case "phone":
                    if (!is_numeric($value)) {
                        $result[$option] = false;
                        break;
                    }
                    $result[$option] = preg_match('#^13[\d]{9}$|^14[5,7]{1}\d{8}$|^15[^4]{1}\d{8}$|^17[0,6,7,8]{1}\d{8}$|^18[\d]{9}$#', $value) ? true : false;
                    break;
                case "url":
                    $result[$option] = filter_var($value, FILTER_VALIDATE_URL);
                    break;
                case "mac":
                    $result[$option] = filter_var($value, FILTER_VALIDATE_MAC);
                    break;
                default :
                    throw new Exception("Unknown validate option: " . $option);
            }
        }
        return $result;
    }
}

/**
 * 返回csrf-token
 */
if (!function_exists('csrf_token')) {
    function csrf_token()
    {
       return Session::token();
    }
}

/**
 * echo form  _token
 */
if (!function_exists('csrf_field')) {
    function csrf_field()
    {
       echo "<input type='hidden' name='_token' value='".Session::token()."'>";
    }
}