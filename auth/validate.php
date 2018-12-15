<?php

require($_SERVER['DOCUMENT_ROOT'] . '/includes/init.php');

// add validation before somebody can try to verify token

$access_token = check_data($_REQUEST['access_token'], true, 'access_token', true);

$access = sql_select('access_tokens', 'client_id,user_id,expires,scope', "access_token='{$access_token}'", true);

if ($access['expires'] <= time()) {
    response(false, 'bad_access_token');
}

response(true, 'access_token_valid', ['client_id' => $access['client_id'], 'user_id' => $access['user_id'], 'expires' => $access['expires'], 'scope'=> json_decode($access['scope'])]);
