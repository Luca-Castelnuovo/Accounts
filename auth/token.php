<?php
$no_session = true;
require($_SERVER['DOCUMENT_ROOT'] . '/includes/init.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    response(false, 'invalid_request');
}

// Validate server
$client = client_validation($_POST['client_id'], $_POST['client_secret']);

// Input
$grant_type = check_data($_POST['grant_type'], true, 'grant_type', true);

switch ($grant_type) {
    case 'client_credentials':
        $authorization_code_sql['client_id'] = $client['id'];
        $authorization_code_sql['user_id'] = 'not_set';
        $authorization_code_sql['scope'] = json_encode(['client_credentials']);
        break;

    case 'authorization_code':
        $authorization_code = check_data($_POST['code'], true, 'authorization_code', true);
        $state = check_data($_POST['state'], false, '', true);

        // Validate authorization code
        $authorization_code_sql = sql_select(
            'authorization_codes',
            'client_id,user_id,expires,scope,state,token_id',
            "authorization_code='{$authorization_code}'",
            true
        );

        if ($authorization_code_sql['client_id'] != $client['id']) {
            response(false, 'bad_authorization_code');
        }

        if ($authorization_code_sql['expires'] <= time()) {
            response(false, 'bad_authorization_code');
        }

        if (isset($authorization_code_sql['token_id']) && !empty($authorization_code_sql['token_id'])) {
            response(false, 'bad_authorization_code');
        }

        if (isset($authorization_code_sql['state']) && !empty($authorization_code_sql['state'])) {
            if ($authorization_code_sql['state'] != $state) {
                response(false, 'bad_authorization_code');
            }
        }
        break;

    default:
        response(false, 'invalid_grant_type');
        break;
}

// Create access token`
$access_token = gen($GLOBALS['config']->auth->length->access_token);
$expires = expires($GLOBALS['config']->auth->expires->access_token);

sql_insert('access_tokens', [
    'access_token' => $access_token,
    'client_id' => $authorization_code_sql['client_id'],
    'user_id' => $authorization_code_sql['user_id'],
    'expires' => $expires,
    'scope' => $authorization_code_sql['scope'],
]);

$token_id = sql_select(
    'access_tokens',
    'id',
    "access_token='{$access_token}'",
    true
)['id'];

sql_update(
    'authorization_codes',
    ['token_id' => $token_id],
    "authorization_code='{$authorization_code}'"
);

$scope_array = json_decode($authorization_code_sql['scope']);

response(
    true,
    '',
    [
        'access_token' => $access_token,
        'scope' => $scope_array,
        'expires' => $GLOBALS['config']->auth->expires->access_token
    ]
);
