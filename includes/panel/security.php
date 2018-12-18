<?php

function csrf_gen()
{
    if (isset($_SESSION['CSRFtoken'])) {
        return $_SESSION['CSRFtoken'];
    } else {
        $_SESSION['CSRFtoken'] = gen(32);
        return $_SESSION['CSRFtoken'];
    }
}


function csrf_val($CSRFtoken, $redirect = '/')
{
    if (!isset($_SESSION['CSRFtoken'])) {
        redirect($redirect, 'CSRF Error');
    }

    if (!(hash_equals($_SESSION['CSRFtoken'], $CSRFtoken))) {
        redirect($redirect, 'CSRF Error');
    } else {
        unset($_SESSION['CSRFtoken']);
    }
}


function captcha_val($resonse, $redirect = '/')
{
    $url = "https://www.google.com/recaptcha/api/siteverify?secret={$GLOBALS['config']->recaptcha->secret_key}&response={$resonse}";
    $response = json_decode(file_get_contents($url));

    if (!$response->success) {
        redirect($redirect, 'Please try again.');
    }
}
