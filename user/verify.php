<?php

require($_SERVER['DOCUMENT_ROOT'] . '/includes/init.php');

$token_GET = check_data($_REQUEST['token'], true, 'Token', true, true, '/');

$token = sql_select('general_tokens', 'revoked,expires,user_id', "token='{$token_GET}' AND type='verify_email'", true);

if ($token['expires'] <= time() || $token['revoked']) {
    redirect('/', 'Link already used or expired.');
}

sql_update('users', [
    'email_verified' => '1'
], "username='{$token['user_id']}'");

sql_update('general_tokens', [
    'revoked' => '1'
], "token='{$token_GET}'");

redirect('/', 'Your account is verified.');
