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


function csrf_val($CSRFtoken)
{
    if (!isset($_SESSION['CSRFtoken'])) {
        redirect('/', 'CSRF Error');
    }

    if (!(hash_equals($_SESSION['CSRFtoken'], $CSRFtoken))) {
        redirect('/', 'CSRF Error');
    } else {
        unset($_SESSION['CSRFtoken']);
    }
}
