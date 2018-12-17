<?php

function loggedin()
{
    if (!$_SESSION['logged_in']) {
        redirect('/?reset&return_to=' . url_encode($GLOBALS['config']->app->url . $_SERVER['REQUEST_URI']), 'Please login');
    }

    if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 1800)) {
        redirect('/?reset&return_to=' . url_encode($GLOBALS['config']->app->url . $_SERVER['REQUEST_URI']), 'Please login');
    } else {
        $_SESSION['LAST_ACTIVITY'] = time();
    }

    if ($_SESSION['ip'] != $_SERVER['REMOTE_ADDR']) {
        redirect('/?reset&return_to=' . url_encode($GLOBALS['config']->app->url . $_SERVER['REQUEST_URI']), 'Please login');
    }
}


function rememberme()
{
    // TODO: build remember me system
    redirect('/?deleteRemember&reset', 'Function not implemented');

    // $remember_request = request('https://auth.lucacastelnuovo.nl/remember.php', ["type" => "validate", "cookie" => "{$_COOKIE['REMEMBERME']}", "server_token" => "{$GLOBALS['config']->server_token}"], '/?reset&deleteRemember');
    // $check_request = request('https://auth.lucacastelnuovo.nl/check.php', ["token" => "{$remember_request['token']}", "server_token" => "{$GLOBALS['config']->server_token}"], '/');
    //
    // if (in_array('admin.account.lucacastelnuovo.nl', allowed_domains($remember_request['token']))) {
    //     $_SESSION['admin'] = true;
    // }
    //
    // $_SESSION['logged_in'] = true;
    // $_SESSION['ip'] = $_SERVER['REMOTE_ADDR'];
    // $_SESSION['token'] = $remember_request['token'];
    // $_SESSION['expires'] = $remember_request['expires'];
    //
    // session_regenerate_id(true);
    //
    // if (isset($_SESSION['return_url'])) {
    //     redirect($_SESSION['return_url'], 'You are logged in');
    // } else {
    //     redirect('/home', 'You are logged in');
    // }
}


function logout()
{
    session_destroy();
    session_start();
    redirect('/', 'You are logged out.');
}


function reset_session()
{
    if (isset($_SESSION['alert'])) {
        $alert = $_SESSION['alert'];
    }

    session_destroy();
    session_start();

    if (isset($_GET['return_to'])) {
        redirect('/?return_to=' . $_GET['return_to'], $alert);
    } else {
        redirect('/', $alert);
    }
}


function url_encode($url)
{
    return urlencode($url);
}

function url_decode($url)
{
    $url = urldecode($url);
    $parse = parse_url($url);

    $allowed_hosts = $GLOBALS['config']->security->allowed_hosts->redirect;

    if (!in_array($parse['host'], $allowed_hosts)) {
        return false;
    } else {
        return $url;
    }
}


function cookie_set($name, $value, $lifeTime)
{
    setcookie($name, $value, time() + $lifeTime, '/', $GLOBALS['config']->app->domain, true, true);
}

function cookie_delete($name)
{
    setcookie($name, null, time() - 3600, '/', $GLOBALS['config']->app->domain, true, true);
}


function forgot($email)
{
    $user = sql_select('users', 'id', "email='{$email}'", true);

    if (!isset($user['id']) || empty($user['id'])) {
        redirect('/', 'User account doesn\'t exist.');
    }

    $expires = time() + 10800;
    $token = gen(128);

    sql_insert('general_tokens', [
        'revoked' => '0',
        'type' => 'reset_password',
        'user_id' => $email,
        'expires' => $expires,
        'token' => $token
    ]);

    $to = $email;
    $subject = 'Please reset your password || LTC Auth';
    $body = <<<HTML
    <!DOCTYPE html><html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office"><head><title></title> <!--[if !mso]><!-- --><meta http-equiv="X-UA-Compatible" content="IE=edge"> <!--<![endif]--><meta http-equiv="Content-Type" content="text/html; charset=UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><style type="text/css">#outlook a{padding:0}.ReadMsgBody{width:100%}.ExternalClass{width:100%}.ExternalClass *{line-height:100%}body{margin:0;padding:0;-webkit-text-size-adjust:100%;-ms-text-size-adjust:100%}table,td{border-collapse:collapse;mso-table-lspace:0pt;mso-table-rspace:0pt}img{border:0;height:auto;line-height:100%;outline:none;text-decoration:none;-ms-interpolation-mode:bicubic}p{display:block;margin:13px 0}</style><!--[if !mso]><!--><style type="text/css">@media only screen and (max-width:480px){@-ms-viewport{width:320px}@viewport{width:320px}}</style><!--<![endif]--><!--[if mso]><xml> <o:OfficeDocumentSettings> <o:AllowPNG/> <o:PixelsPerInch>96</o:PixelsPerInch> </o:OfficeDocumentSettings></xml><![endif]--><!--[if lte mso 11]><style type="text/css">.outlook-group-fix{width:100% !important}</style><![endif]--><!--[if !mso]><!--><link href="https://fonts.googleapis.com/css?family=Ubuntu:300,400,500,700" rel="stylesheet" type="text/css"><style type="text/css">@import url(https://fonts.googleapis.com/css?family=Ubuntu:300,400,500,700);</style><!--<![endif]--><style type="text/css">@media only screen and (min-width:480px){.mj-column-per-100{width:100%!important}}</style></head><body style="background: #FFFFFF;"><div class="mj-container" style="background-color:#FFFFFF;"><!--[if mso | IE]><table role="presentation" border="0" cellpadding="0" cellspacing="0" width="600" align="center" style="width:600px;"><tr><td style="line-height:0px;font-size:0px;mso-line-height-rule:exactly;"> <![endif]--><div style="margin:0px auto;max-width:600px;"><table role="presentation" cellpadding="0" cellspacing="0" style="font-size:0px;width:100%;" align="center" border="0"><tbody><tr><td style="text-align:center;vertical-align:top;direction:ltr;font-size:0px;padding:9px 0px 9px 0px;"><!--[if mso | IE]><table role="presentation" border="0" cellpadding="0" cellspacing="0"><tr><td style="vertical-align:top;width:600px;"> <![endif]--><div class="mj-column-per-100 outlook-group-fix" style="vertical-align:top;display:inline-block;direction:ltr;font-size:13px;text-align:left;width:100%;"><table role="presentation" cellpadding="0" cellspacing="0" style="vertical-align:top;" width="100%" border="0"><tbody><tr><td style="word-wrap:break-word;font-size:0px;padding:0px 0px 0px 0px;" align="center"><table role="presentation" cellpadding="0" cellspacing="0" style="border-collapse:collapse;border-spacing:0px;" align="center" border="0"><tbody><tr><td style="width:150px;"><a href="https://accounts.lucacastelnuovo.nl" target="_blank"><img alt="Logo || LTC Auth" title="" height="auto" src="./images/favicon.png" style="border:none;border-radius:0px;display:block;font-size:13px;outline:none;text-decoration:none;width:100%;height:auto;" width="150"></a></td></tr></tbody></table></td></tr></tbody></table></div><!--[if mso | IE]></td></tr></table> <![endif]--></td></tr></tbody></table></div><!--[if mso | IE]></td></tr></table> <![endif]--> <!--[if mso | IE]><table role="presentation" border="0" cellpadding="0" cellspacing="0" width="600" align="center" style="width:600px;"><tr><td style="line-height:0px;font-size:0px;mso-line-height-rule:exactly;"> <![endif]--><div style="margin:0px auto;max-width:600px;"><table role="presentation" cellpadding="0" cellspacing="0" style="font-size:0px;width:100%;" align="center" border="0"><tbody><tr><td style="text-align:center;vertical-align:top;direction:ltr;font-size:0px;padding:9px 0px 9px 0px;"><!--[if mso | IE]><table role="presentation" border="0" cellpadding="0" cellspacing="0"><tr><td style="vertical-align:top;width:600px;"> <![endif]--><div class="mj-column-per-100 outlook-group-fix" style="vertical-align:top;display:inline-block;direction:ltr;font-size:13px;text-align:left;width:100%;"><table role="presentation" cellpadding="0" cellspacing="0" style="vertical-align:top;" width="100%" border="0"><tbody><tr><td style="word-wrap:break-word;font-size:0px;padding:15px 15px 15px 15px;" align="left"><div style="cursor:auto;color:#000000;font-family:Ubuntu, Helvetica, Arial, sans-serif;font-size:11px;line-height:1.5;text-align:left;"><p><span style="font-size:20px;">Dear,&#xA0;<strong>{$first_name} {$last_name},</strong></span></p><p><span style="font-size:20px;">We heard that your lost your password. Click the button to reset your password.</span></p></div></td></tr></tbody></table></div><!--[if mso | IE]></td></tr></table> <![endif]--></td></tr></tbody></table></div><!--[if mso | IE]></td></tr></table> <![endif]--> <!--[if mso | IE]><table role="presentation" border="0" cellpadding="0" cellspacing="0" width="600" align="center" style="width:600px;"><tr><td style="line-height:0px;font-size:0px;mso-line-height-rule:exactly;"> <![endif]--><div style="margin:0px auto;max-width:600px;"><table role="presentation" cellpadding="0" cellspacing="0" style="font-size:0px;width:100%;" align="center" border="0"><tbody><tr><td style="text-align:center;vertical-align:top;direction:ltr;font-size:0px;padding:9px 0px 9px 0px;"><!--[if mso | IE]><table role="presentation" border="0" cellpadding="0" cellspacing="0"><tr><td style="vertical-align:top;width:600px;"> <![endif]--><div class="mj-column-per-100 outlook-group-fix" style="vertical-align:top;display:inline-block;direction:ltr;font-size:13px;text-align:left;width:100%;"><table role="presentation" cellpadding="0" cellspacing="0" style="vertical-align:top;" width="100%" border="0"><tbody><tr><td style="word-wrap:break-word;font-size:0px;padding:0px 20px 0px 20px;" align="center"><table role="presentation" cellpadding="0" cellspacing="0" style="border-collapse:separate;width:100%;" align="center" border="0"><tbody><tr><td style="border:0px solid #000;border-radius:24px;color:#fff;cursor:auto;padding:14px 40px;" align="center" valign="middle" bgcolor="#2962FF"><a href="https://accounts.lucacastelnuovo.nl/user/reset?token={$token}" style="text-decoration:none;background:#2962FF;color:#fff;font-family:Ubuntu, Helvetica, Arial, sans-serif, Helvetica, Arial, sans-serif;font-size:20px;font-weight:normal;line-height:120%;text-transform:none;margin:0px;" target="_blank">Reset password</a></td></tr></tbody></table></td></tr></tbody></table></div><!--[if mso | IE]></td></tr></table> <![endif]--></td></tr></tbody></table></div><!--[if mso | IE]></td></tr></table> <![endif]--> <!--[if mso | IE]><table role="presentation" border="0" cellpadding="0" cellspacing="0" width="600" align="center" style="width:600px;"><tr><td style="line-height:0px;font-size:0px;mso-line-height-rule:exactly;"> <![endif]--><div style="margin:0px auto;max-width:600px;"><table role="presentation" cellpadding="0" cellspacing="0" style="font-size:0px;width:100%;" align="center" border="0"><tbody><tr><td style="text-align:center;vertical-align:top;direction:ltr;font-size:0px;padding:9px 0px 9px 0px;"><!--[if mso | IE]><table role="presentation" border="0" cellpadding="0" cellspacing="0"><tr><td style="vertical-align:top;width:600px;"> <![endif]--><div class="mj-column-per-100 outlook-group-fix" style="vertical-align:top;display:inline-block;direction:ltr;font-size:13px;text-align:left;width:100%;"><table role="presentation" cellpadding="0" cellspacing="0" style="vertical-align:top;" width="100%" border="0"><tbody><tr><td style="word-wrap:break-word;font-size:0px;padding:15px 15px 15px 15px;" align="left"><div style="cursor:auto;color:#000000;font-family:Ubuntu, Helvetica, Arial, sans-serif;font-size:11px;line-height:1.5;text-align:left;"><p><span style="font-size:16px;"><span style="color: rgb(34, 34, 34); font-family: " droid="" sans",="" sans-serif;"="">If you don&#x2019;t use this link within 3 hours, it will expire. To get a new password reset link, visit https://accounts.lucacastelnuovo.nl/user/forgot</span></span></p></div></td></tr></tbody></table></div><!--[if mso | IE]></td></tr></table> <![endif]--></td></tr></tbody></table></div><!--[if mso | IE]></td></tr></table> <![endif]--> <!--[if mso | IE]><table role="presentation" border="0" cellpadding="0" cellspacing="0" width="600" align="center" style="width:600px;"><tr><td style="line-height:0px;font-size:0px;mso-line-height-rule:exactly;"> <![endif]--><div style="margin:0px auto;max-width:600px;"><table role="presentation" cellpadding="0" cellspacing="0" style="font-size:0px;width:100%;" align="center" border="0"><tbody><tr><td style="text-align:center;vertical-align:top;direction:ltr;font-size:0px;padding:9px 0px 9px 0px;"><!--[if mso | IE]><table role="presentation" border="0" cellpadding="0" cellspacing="0"><tr><td style="vertical-align:top;width:600px;"> <![endif]--><div class="mj-column-per-100 outlook-group-fix" style="vertical-align:top;display:inline-block;direction:ltr;font-size:13px;text-align:left;width:100%;"><table role="presentation" cellpadding="0" cellspacing="0" style="vertical-align:top;" width="100%" border="0"><tbody><tr><td style="word-wrap:break-word;font-size:0px;padding:15px 15px 15px 15px;" align="left"><div style="cursor:auto;color:#000000;font-family:Ubuntu, Helvetica, Arial, sans-serif;font-size:11px;line-height:1.5;text-align:left;"><p><span style="font-size:12px;"><span style="color:#999999;">Button not working? Paste the following link into your browser:&#xA0;</span><a href="https://accounts.lucacastelnuovo.nl/user/reset?token={$token}"><span style="color:#999999;">https://accounts.lucacastelnuovo.nl/user/reset?token={$token}</span></a></span></p><p></p><p><span style="color:#999999;"><span style="font-size:12px;">You&#x2019;re receiving this email because you recently requested&#xA0;a new password. If this wasn&#x2019;t you, please ignore this email.</span></span></p></div></td></tr></tbody></table></div><!--[if mso | IE]></td></tr></table> <![endif]--></td></tr></tbody></table></div><!--[if mso | IE]></td></tr></table> <![endif]--></div></body></html>
HTML;

    // TODO: sendmail
    // send_mail($to, $subject, $body);

    redirect('/', 'Check your email for a reset password link.');
}
