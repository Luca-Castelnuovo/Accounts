<?php

require($_SERVER['DOCUMENT_ROOT'] . '/includes/init.php');

loggedin_admin();

csrf_val($_GET['CSRFtoken']);

switch ($_GET['type']) {
    case 'access_token':
        sql_delete('access_tokens', 'true');
        redirect('/home', 'Access_tokens revoked.');
        break;

    case 'authorization_code':
        sql_delete('authorization_codes', 'true');
        redirect('/home', 'Authorization_codes revoked.');
        break;

    case 'all':
        sql_delete('authorization_codes', 'true');
        sql_delete('access_tokens', 'true');
        sql_update('users', ['applications' => '[]'], "admin='0'");
        redirect('/home', 'Everything revoked.');
        break;

    default:
        redirect('/home', 'Invalid type.');
        break;
}
