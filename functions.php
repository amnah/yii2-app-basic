<?php


// -------------------------------------------------------------
// Application functions
// -------------------------------------------------------------
/**
 * Get env
 * @param string $key
 * @param mixed $default
 * @return mixed
 */
function env($key, $default = null)
{
    static $env;
    if ($env === null) {
        $env = require_once __DIR__ . '/env.php';
    }
    return array_key_exists($key, $env) ? $env[$key] : $default;
}

/**
 * Check if we force enable yii debug module
 * @return bool
 */
function isDebugEnabled()
{
    // store/return result
    static $result;
    if ($result !== null) {
        return $result;
    }

    // force debug module using $_GET param
    // enable this by manually entering the url "http://example.com?qwe"
    $debugPassword = env('DEBUG_PASSWORD');
    $cookieName    = '_forceDebug';
    $cookieExpire  = YII_ENV_PROD ? 60*15 : 60*60*24; // 15 minutes for production, 24 hrs for everything else

    // check $_GET and $_COOKIE
    $result = false;
    $isGetSet = isset($_GET[$debugPassword]);
    $isCookieSet = (isset($_COOKIE[$cookieName]) && $_COOKIE[$cookieName] === $debugPassword);
    if ($debugPassword && ($isGetSet || $isCookieSet)) {
        // set/refresh cookie
        $result = setcookie($cookieName, $debugPassword, time() + $cookieExpire);
    }
    return $result;
}
