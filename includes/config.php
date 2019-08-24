<?php

// $cofigKey = getenv('config_key');
$cofigKey = "USfXCHolMNTj_kS3LsV1Gg/BmyVli_1m0iC0aycSDFHwA";
$configClient = new \ConfigCat\ConfigCatClient($cofigKey);

if ($appActive = $client->getValue("appActive", true)) {
    http_response_code(503);
    exit('App is temporarily disabled.');
}

return (object) array(
    'app' => (object) array(
        'domain' => $client->getValue("appDomain", "accounts.lucacastelnuovo.nl"),
        'url' => "https://{$client->getValue("appDomain", "accounts.lucacastelnuovo.nl")}",
    ),

    'database' => (object) array(
        'host' => $client->getValue("dbHost", "localhost"),
        'user' => $client->getValue("dbUser", ""),
        'password' => $client->getValue("dbPassword", ""),
        'database' => $client->getValue("dbDatabase", ""),
    ),

    'security' => (object) array(
        'allowed_hosts' => (object) array(
            'redirect' => [$client->getValue("securityAllowedHost", ""), 'localhost'],
        ),

        'hmac'=> $client->getValue("securityHMAC", ""),
    ),

    'auth' => (object) array(
        'length' => (object) array(
            'client_id' => $client->getValue("authLengthClientID", 32),
            'client_secret' => $client->getValue("authLengthClientSecret", 64),
            'authorization_code' => $client->getValue("authLengthAuthorizationCode", 64),
            'access_token' => $client->getValue("authLengthAccessToken", 128),
        ),

        'expires' => (object) array(
            'authorization_code' => $client->getValue("authExpiresAuthorizationCode", 120),
            'access_token' => $client->getValue("authExpiresAccessToken", 86400),
            'remember_me' => $client->getValue("authExpiresRememberMe", 2592000),
            'verify_mail' => $client->getValue("authExpiresVerifyMail", 31449600),
            'reset_password' => $client->getValue("authExpiresResetPassword", 10800),
        ),
    ),

    'recaptcha' => (object) array(
        'secret_key' => $client->getValue("recaptchaSecretKey", ""),
        'public_key' => $client->getValue("recaptchaPublicKey", ""),
    ),

    'client_id' => $client->getValue("clientID", ""),
    'client_secret' => $client->getValue("clientSecret", ""),
);

