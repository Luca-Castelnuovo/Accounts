<?php

require($_SERVER['DOCUMENT_ROOT'] . '/includes/init.php');

loggedin_developer();

$token = request(
    'https://accounts.lucacastelnuovo.nl/auth/token',
    [
        "grant_type" => "client_credentials",
        "client_id" => "{$GLOBALS['config']->client_id}",
        "client_secret" => "{$GLOBALS['config']->client_secret}"
    ]
);

print_r($token);

$to = 'o6469821@nwytg.net';
$subject = 'example';
$body = 'test';

print_r(request(
    'https://api.lucacastelnuovo.nl/mail/',
    [
        "access_token" => "{$token['access_token']}",
        "to" => "{$to}",
        "subject" => "{$subject}",
        "body" => "{$body}",
        "from_name" => "LTC Auth"
    ]
));
