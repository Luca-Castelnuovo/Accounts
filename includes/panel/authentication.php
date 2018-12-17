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


function rememberme()
{
    // TODO: build remember me system
    redirect('/?deleteRemember&reset', 'Function not implemented');

    // $remember_request = request('https://auth.lucacastelnuovo.nl/remember.php', ["type" => "validate", "cookie" => "{$_COOKIE['REMEMBERME']}", "server_token" => "{$GLOBALS['config']->server_token}"], '/?reset&deleteRemember');
    // $check_request = request('https://auth.lucacastelnuovo.nl/check.php', ["token" => "{$remember_request['token']}", "server_token" => "{$GLOBALS['config']->server_token}"], '/');
    //
    // if (in_array('admin.account.lucacastelnuovo.nl', allowed_domains($remember_request['token']))) {
    //     $_SESSION['admin'] = true;
    // }
    //
    // $_SESSION['logged_in'] = true;
    // $_SESSION['ip'] = $_SERVER['REMOTE_ADDR'];
    // $_SESSION['token'] = $remember_request['token'];
    // $_SESSION['expires'] = $remember_request['expires'];
    //
    // session_regenerate_id(true);
    //
    // if (isset($_SESSION['return_url'])) {
    //     redirect($_SESSION['return_url'], 'You are logged in');
    // } else {
    //     redirect('/home', 'You are logged in');
    // }
}


function logout()
{
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
    setcookie($name, $value, time() + $lifeTime, '/', $GLOBALS['config']->app->domain, true, true);
}

function cookie_delete($name)
{
    setcookie($name, null, time() - 3600, '/', $GLOBALS['config']->app->domain, true, true);
}
