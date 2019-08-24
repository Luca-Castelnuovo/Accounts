<?php

$configKey = getenv('CONFIG_KEY');
$configClient = new \ConfigCat\ConfigCatClient($configKey);

if (!$configClient->getValue("appActive", false)) {
    http_response_code(503);
    exit('App is temporarily disabled.');
}

return (object) array(
    'app' => (object) array(
        'domain' => $configClient->getValue("appDomain", "accounts.lucacastelnuovo.nl"),
        'url' => "https://{$configClient->getValue("appDomain", "accounts.lucacastelnuovo.nl")}",
    ),

    'database' => (object) array(
        'host' => $configClient->getValue("dbHost", "localhost"),
        'user' => $configClient->getValue("dbUser", ""),
        'password' => $configClient->getValue("dbPassword", ""),
        'database' => $configClient->getValue("dbDatabase", ""),
    ),

    'security' => (object) array(
        'allowed_hosts' => (object) array(
            'redirect' => [$configClient->getValue("securityAllowedHost", ""), 'localhost'],
        ),

        'hmac'=> $configClient->getValue("securityHMAC", ""),
    ),

    'auth' => (object) array(
        'length' => (object) array(
            'client_id' => $configClient->getValue("authLengthClientID", 32),
            'client_secret' => $configClient->getValue("authLengthClientSecret", 64),
            'authorization_code' => $configClient->getValue("authLengthAuthorizationCode", 64),
            'access_token' => $configClient->getValue("authLengthAccessToken", 128),
        ),

        'expires' => (object) array(
            'authorization_code' => $configClient->getValue("authExpiresAuthorizationCode", 120),
            'access_token' => $configClient->getValue("authExpiresAccessToken", 86400),
            'remember_me' => $configClient->getValue("authExpiresRememberMe", 2592000),
            'verify_mail' => $configClient->getValue("authExpiresVerifyMail", 31449600),
            'reset_password' => $configClient->getValue("authExpiresResetPassword", 10800),
        ),
    ),

    'recaptcha' => (object) array(
        'secret_key' => $configClient->getValue("recaptchaSecretKey", ""),
        'public_key' => $configClient->getValue("recaptchaPublicKey", ""),
    ),

    'client_id' => $configClient->getValue("clientID", ""),
    'client_secret' => $configClient->getValue("clientSecret", ""),
);

