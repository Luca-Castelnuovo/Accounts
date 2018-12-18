<?php

function loggedin()
{
    if (!$_SESSION['logged_in']) {
        redirect('/?reset&redirect_uri=' . url_encode($GLOBALS['config']->app->url . $_SERVER['REQUEST_URI']), 'Please login');
    }

    if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 1800)) {
        redirect('/?reset&redirect_uri=' . url_encode($GLOBALS['config']->app->url . $_SERVER['REQUEST_URI']), 'Please login');
    } else {
        $_SESSION['LAST_ACTIVITY'] = time();
    }

    if ($_SESSION['ip'] != $_SERVER['REMOTE_ADDR']) {
        redirect('/?reset&redirect_uri=' . url_encode($GLOBALS['config']->app->url . $_SERVER['REQUEST_URI']), 'Please login');
    }
}


function rememberme($redirect_uri = null)
{
    list($user_id, $token, $mac) = explode(':', $_COOKIE['REMEMBERME']);

    $user_id = check_data($user_id, false, '', true);
    $token = check_data($token, false, '', true);
    $mac = check_data($mac, false, '', true);

    $tokens = sql_select('general_tokens', 'expires', "token='{$token}' AND user_id='{$user_id}' AND type = 'remember_me' AND revoked='0'", true);

    var_dump($tokens);

    if ($tokens['expires'] <= time()) {
        cookie_delete('REMEMBERME');
        redirect('/?reset', 'Please login');
    }

    $_SESSION['logged_in'] = true;
    $_SESSION['ip'] = $_SERVER['REMOTE_ADDR'];
    $_SESSION['id'] = $user_id;

    session_regenerate_id(true);

    if (isset($_GET['redirect_uri'])) {
        $url = url_decode($_GET['redirect_uri']);
        redirect($url);
    } else {
        redirect('/home');
    }
}


function logout()
{
    if (isset($_COOKIE['REMEMBERME'])) {
        list($user_id, $token, $mac) = explode(':', $_COOKIE['REMEMBERME']);
        sql_delete('general_tokens', "user_id='{$user_id}' AND token='{$token}' AND type = 'remember_me'");
        cookie_delete('REMEMBERME');
    }

    session_destroy();
    session_start();
    redirect('/', 'You are logged out.');
}


function reset_session()
{
    if (isset($_SESSION['alert'])) {
        $alert = $_SESSION['alert'];
    }

    session_destroy();
    session_start();

    if (isset($_GET['redirect_uri'])) {
        redirect('/?redirect_uri=' . $_GET['redirect_uri'], $alert);
    } else {
        redirect('/', $alert);
    }
}


function url_encode($url)
{
    return urlencode($url);
}

function url_decode($url)
{
    $url = urldecode($url);
    $parse = parse_url($url);

    $allowed_hosts = $GLOBALS['config']->security->allowed_hosts->redirect;

    if (!in_array($parse['host'], $allowed_hosts)) {
        return false;
    } else {
        return $url;
    }
}


function cookie_set($name, $value, $lifeTime)
{
    setcookie($name, $value, expires($lifeTime), '/', $GLOBALS['config']->app->domain, true, true);
}

function cookie_delete($name)
{
    setcookie($name, null, time() - 3600, '/', $GLOBALS['config']->app->domain, true, true);
}
