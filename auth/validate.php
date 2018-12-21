<?php
$no_session = true;
require($_SERVER['DOCUMENT_ROOT'] . '/includes/init.php');

$access_token = check_data($_REQUEST['access_token'], true, 'access_token', true);

$access = sql_select('access_tokens', 'expires,scope', "access_token='{$access_token}'", true);

if ($access['expires'] <= time()) {
    response(false, 'bad_access_token');
}

//Return json
response(true, '', ['expires' => $access['expires'], 'scope'=> json_decode($access['scope'])]);
