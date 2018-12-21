<?php

require($_SERVER['DOCUMENT_ROOT'] . '/includes/init.php');

loggedin_admin();

$token_GET = check_data($_GET['token'], true, 'Token', true, true, '/home');
$token = sql_select('general_tokens', 'revoked,expires,user_id', "token='{$token_GET}' AND type='developer_request'", true);

if ($token['expires'] <= time() || $token['revoked']) {
    redirect('/home', 'Link already used or expired.');
}

if ($_GET['status']) {
    sql_update('users', [
        'developer' => '1'
    ], "username='{$token['user_id']}'");
}

sql_update('general_tokens', [
    'revoked' => '1'
], "token='{$token_GET}' AND type='developer_request'");

redirect("/admin/users#{$token['user_id']}", 'Developer privileges granted.');
