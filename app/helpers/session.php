<?php

function sessionStart() 
{
    if (!isset($_SESSION)) {
        session_start();
    }
}

function sessionSet($key, $value) 
{
    $_SESSION[$key] = $value;
}

function sessionGet($key) 
{
    return isset($_SESSION[$key]) ? $_SESSION[$key] : false;
}

function sessionUnset($key) 
{
    $session = sessionGet($key);

    if ($session) {
        unset($_SESSION[$key]);
        return true;
    }
    
    return false;
}

function sessionDestroy()
{
    session_unset();
    session_destroy();

    exit;
}
