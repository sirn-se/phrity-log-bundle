<?php

namespace Phrity\Log;

/*
 * We need to overwrite some native functions to test properly. For that to work,
 * the tests and this file needs to run in the same namespace as the code it is testing.
 */

function mkdir($pathname, $mode = 0777, $recursive = false)
{
    global $override_mkdir;
    if (!is_null($override_mkdir)) {
        return $override_mkdir;
    }
    return \mkdir($pathname, $mode, $recursive);
}

function is_dir($filename)
{
    global $override_is_dir;
    if (!is_null($override_is_dir)) {
        return $override_is_dir;
    }
    return \is_dir($filename);
}

function file_put_contents($filename, $data, $flags)
{
    global $override_file_put_contents;
    if (!is_null($override_file_put_contents)) {
        return $override_file_put_contents;
    }
    return \file_put_contents($filename, $data, $flags);
}

function php_sapi_name()
{
    global $override_php_sapi_name;
    if (!is_null($override_php_sapi_name)) {
        return $override_php_sapi_name;
    }
    return \php_sapi_name();
}

function defined($name)
{
    global $override_defined;
    if (!is_null($override_defined)) {
        return $override_defined;
    }
    return \defined($name);
}

function trigger_error($error_msg, $error_type = E_USER_NOTICE)
{
    global $override_trigger_error;
    if (!is_null($override_trigger_error)) {
        return $override_trigger_error;
    }
    return \trigger_error($error_msg, $error_type);
}
