<?php

function loggedin()
{
    $encoded_url = urlencode($GLOBALS['config']->app->url . $_SERVER['REQUEST_URI']);

    if ((!$_SESSION['logged_in']) || ($_SESSION['ip'] != $_SERVER['REMOTE_ADDR']) || (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 1800))) {
        redirect("/?reset&redirect_uri={$encoded_url}", 'Please login');
    } else {
        $_SESSION['LAST_ACTIVITY'] = time();
    }
}


function rememberme($redirect_uri = null)
{
    list($user_id, $token, $mac) = explode(':', $_COOKIE['REMEMBERME']);

    $user_id = check_data($user_id, false, '', true);
    $token = check_data($token, false, '', true);
    $tokens = sql_select('general_tokens', 'expires', "token='{$token}' AND type='remember_me' AND user_id='{$user_id}' AND revoked='0'", true);

    if ($tokens['expires'] <= time() || !hash_equals(hash_hmac('sha512', $user_id . ':' . $token, $GLOBALS['config']->security->hmac), $mac)) {
        cookie_delete('REMEMBERME');
        redirect('/?reset', 'Please login');
    }

    $_SESSION['logged_in'] = true;
    $_SESSION['ip'] = $_SERVER['REMOTE_ADDR'];
    $_SESSION['id'] = $user_id;

    session_regenerate_id(true);

    if (isset($_GET['redirect_uri'])) {
        $url = url_decode($_GET['redirect_uri']);
        redirect($url, 'You are logged in.');
    } else {
        redirect('/home', 'You are logged in.');
    }
}


function logout()
{
    if (isset($_COOKIE['REMEMBERME'])) {
        list($user_id, $token, $mac) = explode(':', $_COOKIE['REMEMBERME']);

        $user_id = check_data($user_id, false, '', true);
        $token = check_data($token, false, '', true);
        $tokens = sql_select('general_tokens', 'expires', "token='{$token}' AND type='remember_me' AND user_id='{$user_id}' AND revoked='0'", true);

        if ($tokens['expires'] <= time() || !hash_equals(hash_hmac('sha512', $tokens['user_id'] . ':' . $token, $GLOBALS['config']->security->hmac), $mac)) {
            sql_update('general_tokens', [
                'revoked' => '1'
            ], "token='{$token}' AND type='remember_me' AND user_id='{$user_id}' AND revoked='0'");

            cookie_delete('REMEMBERME');
        }
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
        redirect('/?redirect_uri=' . urlencode($_GET['redirect_uri']), $alert);
    } else {
        redirect('/', $alert);
    }
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
