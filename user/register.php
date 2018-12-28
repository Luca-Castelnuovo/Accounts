<?php

require($_SERVER['DOCUMENT_ROOT'] . '/includes/init.php');

if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']) {
    redirect('/home');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_val($_POST['CSRFtoken']);
    captcha_val($_POST['g-recaptcha-response'], '/user/register');

    $username = check_data($_POST['username'], true, 'Username', true, true, '/user/register');
    $password = check_data($_POST['password'], true, 'Password', true, true, '/user/register');
    $password_confirm = check_data($_POST['password_confirm'], true, 'Password Confirm', true, true, '/user/register');
    $first_name = check_data($_POST['first_name'], true, 'First Name', true, true, '/user/register');
    $last_name = check_data($_POST['last_name'], true, 'Last Name', true, true, '/user/register');
    $email = check_data($_POST['email'], true, 'Email', true, true, '/user/register');
    $picture_url = check_data($_POST['picture_url'], false, '', true);
    $created = date("Y-m-d H:i:s");

    // Check if username is taken
    $existing_username = sql_select('users', 'id', "username='{$username}'", false);
    if ($existing_username->num_rows > 0) {
        redirect('/user/register', 'Username is taken, please choose another.');
    }

    // Check if email is taken
    $existing_email = sql_select('users', 'id', "email='{$email}'", false);
    if ($existing_email->num_rows > 0) {
        redirect('/user/register', 'Email is taken, please choose another.');
    }

    // Check if passwords match
    if ($password !== $password_confirm) {
        redirect('/user/register', "Passwords don't match.");
    }

    // Hash password
    $password = password_hash($password, PASSWORD_BCRYPT);

    sql_insert('users', [
        'username' => $username,
        'password' => $password,
        'first_name' => $first_name,
        'last_name' => $last_name,
        'email' => $email,
        'email_verified' => '0',
        'picture_url' => $picture_url,
        'created' => $created,
    ]);


    // create conformation token
    $expires = expires($GLOBALS['config']->auth->expires->verify_mail);
    $token = gen(64);

    sql_insert('general_tokens', [
        'revoked' => '0',
        'type' => 'verify_email',
        'user_id' => $username,
        'expires' => $expires,
        'token' => $token
    ]);

    //send conformation mail
    $to = $email;
    $subject = 'Please verify your email address || LTC Auth';
    $body = <<<HTML
    <!DOCTYPE html><html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office"><head><title></title> <!--[if !mso]><!-- --><meta http-equiv="X-UA-Compatible" content="IE=edge"> <!--<![endif]--><meta http-equiv="Content-Type" content="text/html; charset=UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><style type="text/css">#outlook a{padding:0}.ReadMsgBody{width:100%}.ExternalClass{width:100%}.ExternalClass *{line-height:100%}body{margin:0;padding:0;-webkit-text-size-adjust:100%;-ms-text-size-adjust:100%}table,td{border-collapse:collapse;mso-table-lspace:0pt;mso-table-rspace:0pt}img{border:0;height:auto;line-height:100%;outline:none;text-decoration:none;-ms-interpolation-mode:bicubic}p{display:block;margin:13px 0}</style><!--[if !mso]><!--><style type="text/css">@media only screen and (max-width:480px){@-ms-viewport{width:320px}@viewport{width:320px}}</style><!--<![endif]--><!--[if mso]><xml> <o:OfficeDocumentSettings> <o:AllowPNG/> <o:PixelsPerInch>96</o:PixelsPerInch> </o:OfficeDocumentSettings></xml><![endif]--><!--[if lte mso 11]><style type="text/css">.outlook-group-fix{width:100% !important}</style><![endif]--><!--[if !mso]><!--><link href="https://fonts.googleapis.com/css?family=Ubuntu:300,400,500,700" rel="stylesheet" type="text/css"><style type="text/css">@import url(https://fonts.googleapis.com/css?family=Ubuntu:300,400,500,700);</style><!--<![endif]--><style type="text/css">@media only screen and (min-width:480px){.mj-column-per-100{width:100%!important}}</style></head><body style="background: #FFFFFF;"><div class="mj-container" style="background-color:#FFFFFF;"><!--[if mso | IE]><table role="presentation" border="0" cellpadding="0" cellspacing="0" width="600" align="center" style="width:600px;"><tr><td style="line-height:0px;font-size:0px;mso-line-height-rule:exactly;"> <![endif]--><div style="margin:0px auto;max-width:600px;"><table role="presentation" cellpadding="0" cellspacing="0" style="font-size:0px;width:100%;" align="center" border="0"><tbody><tr><td style="text-align:center;vertical-align:top;direction:ltr;font-size:0px;padding:9px 0px 9px 0px;"><!--[if mso | IE]><table role="presentation" border="0" cellpadding="0" cellspacing="0"><tr><td style="vertical-align:top;width:600px;"> <![endif]--><div class="mj-column-per-100 outlook-group-fix" style="vertical-align:top;display:inline-block;direction:ltr;font-size:13px;text-align:left;width:100%;"><table role="presentation" cellpadding="0" cellspacing="0" style="vertical-align:top;" width="100%" border="0"><tbody><tr><td style="word-wrap:break-word;font-size:0px;padding:0px 0px 0px 0px;" align="center"><table role="presentation" cellpadding="0" cellspacing="0" style="border-collapse:collapse;border-spacing:0px;" align="center" border="0"><tbody><tr><td style="width:150px;"><a href="https://accounts.lucacastelnuovo.nl" target="_blank"><img alt="Logo || LTC Auth" title="" height="auto" src="https://accounts.lucacastelnuovo.nl/favicon.png" style="border:none;border-radius:0px;display:block;font-size:13px;outline:none;text-decoration:none;width:100%;height:auto;" width="150"></a></td></tr></tbody></table></td></tr></tbody></table></div><!--[if mso | IE]></td></tr></table> <![endif]--></td></tr></tbody></table></div><!--[if mso | IE]></td></tr></table> <![endif]--> <!--[if mso | IE]><table role="presentation" border="0" cellpadding="0" cellspacing="0" width="600" align="center" style="width:600px;"><tr><td style="line-height:0px;font-size:0px;mso-line-height-rule:exactly;"> <![endif]--><div style="margin:0px auto;max-width:600px;"><table role="presentation" cellpadding="0" cellspacing="0" style="font-size:0px;width:100%;" align="center" border="0"><tbody><tr><td style="text-align:center;vertical-align:top;direction:ltr;font-size:0px;padding:9px 0px 9px 0px;"><!--[if mso | IE]><table role="presentation" border="0" cellpadding="0" cellspacing="0"><tr><td style="vertical-align:top;width:600px;"> <![endif]--><div class="mj-column-per-100 outlook-group-fix" style="vertical-align:top;display:inline-block;direction:ltr;font-size:13px;text-align:left;width:100%;"><table role="presentation" cellpadding="0" cellspacing="0" width="100%" border="0"><tbody><tr><td style="word-wrap:break-word;font-size:0px;padding:15px 15px 15px 15px;" align="left"><div style="cursor:auto;color:#000000;font-family:Ubuntu, Helvetica, Arial, sans-serif;font-size:11px;line-height:1.5;text-align:left;"><p><span style="font-size:20px;">Almost done,&#xA0;<strong>{$first_name} {$last_name}</strong>! To complete your sign up, we need to verify your email address: <b>{$email}</b>.</span></p></div></td></tr></tbody></table></div><!--[if mso | IE]></td></tr></table> <![endif]--></td></tr></tbody></table></div><!--[if mso | IE]></td></tr></table> <![endif]--> <!--[if mso | IE]><table role="presentation" border="0" cellpadding="0" cellspacing="0" width="600" align="center" style="width:600px;"><tr><td style="line-height:0px;font-size:0px;mso-line-height-rule:exactly;"> <![endif]--><div style="margin:0px auto;max-width:600px;"><table role="presentation" cellpadding="0" cellspacing="0" style="font-size:0px;width:100%;" align="center" border="0"><tbody><tr><td style="text-align:center;vertical-align:top;direction:ltr;font-size:0px;padding:9px 0px 9px 0px;"><!--[if mso | IE]><table role="presentation" border="0" cellpadding="0" cellspacing="0"><tr><td style="vertical-align:top;width:600px;"> <![endif]--><div class="mj-column-per-100 outlook-group-fix" style="vertical-align:top;display:inline-block;direction:ltr;font-size:13px;text-align:left;width:100%;"><table role="presentation" cellpadding="0" cellspacing="0" width="100%" border="0"><tbody><tr><td style="word-wrap:break-word;font-size:0px;padding:0px 20px 0px 20px;" align="center"><table role="presentation" cellpadding="0" cellspacing="0" style="border-collapse:separate;width:100%;" align="center" border="0"><tbody><tr><td style="border:0px solid #000;border-radius:24px;color:#fff;cursor:auto;padding:14px 40px;" align="center" valign="middle" bgcolor="#2962FF"><a href="https://accounts.lucacastelnuovo.nl/user/verify?token={$token}" style="text-decoration:none;background:#2962FF;color:#fff;font-family:Ubuntu, Helvetica, Arial, sans-serif, Helvetica, Arial, sans-serif;font-size:20px;font-weight:normal;line-height:120%;text-transform:none;margin:0px;" target="_blank">Verify email address</a></td></tr></tbody></table></td></tr></tbody></table></div><!--[if mso | IE]></td></tr></table> <![endif]--></td></tr></tbody></table></div><!--[if mso | IE]></td></tr></table> <![endif]--> <!--[if mso | IE]><table role="presentation" border="0" cellpadding="0" cellspacing="0" width="600" align="center" style="width:600px;"><tr><td style="line-height:0px;font-size:0px;mso-line-height-rule:exactly;"> <![endif]--><div style="margin:0px auto;max-width:600px;"><table role="presentation" cellpadding="0" cellspacing="0" style="font-size:0px;width:100%;" align="center" border="0"><tbody><tr><td style="text-align:center;vertical-align:top;direction:ltr;font-size:0px;padding:9px 0px 9px 0px;"><!--[if mso | IE]><table role="presentation" border="0" cellpadding="0" cellspacing="0"><tr><td style="vertical-align:top;width:600px;"> <![endif]--><div class="mj-column-per-100 outlook-group-fix" style="vertical-align:top;display:inline-block;direction:ltr;font-size:13px;text-align:left;width:100%;"><table role="presentation" cellpadding="0" cellspacing="0" width="100%" border="0"><tbody><tr><td style="word-wrap:break-word;font-size:0px;padding:15px 15px 15px 15px;" align="left"><div style="cursor:auto;color:#000000;font-family:Ubuntu, Helvetica, Arial, sans-serif;font-size:11px;line-height:1.5;text-align:left;"><p><span style="font-size:12px;"><span style="color:#999999;">Button not working? Paste the following link into your browser:&#xA0;</span><a href="https://accounts.lucacastelnuovo.nl/user/verify?token={$token}"><span style="color:#999999;">https://accounts.lucacastelnuovo.nl/user/verify?token={$token}</span></a></span></p><p></p><p><span style="color:#999999;"><span style="font-size:12px;">You&#x2019;re receiving this email because you recently created a new account. If this wasn&#x2019;t you, please ignore this email.</span></span></p></div></td></tr></tbody></table></div><!--[if mso | IE]></td></tr></table> <![endif]--></td></tr></tbody></table></div><!--[if mso | IE]></td></tr></table> <![endif]--></div></body></html>
HTML;

    send_mail($to, $subject, $body);

    redirect('/', 'Account registered. Please verify your mail.');
}

?>
<!DOCTYPE html>
<html>

<head>
    <title>Register</title>

    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.lucacastelnuovo.nl/general/css/materialize.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>

    <link rel="shortcut icon" href="/images/favicon.ico">
    <link rel="apple-touch-icon" sizes="180x180" href="/images/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/images/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/images/favicon-16x16.png">
    <link rel="mask-icon" href="/images/safari-pinned-tab.svg" color="#2962ff">
    <link rel="manifest" href="/site.webmanifest">

    <style>
        .input-field input:focus + label {color: #2962FF !important;}

        .input-field input:focus {
            border-bottom: 1px solid #2962FF !important;
            box-shadow: 0 1px 0 0 #2962FF !important;
        }

        [type="checkbox"].filled-in:checked + span:not(.lever):after {
           border: 2px solid #2962FF !important;
           background-color: #2962FF !important;
        }

        .p-0 {padding: 0;}
        .progress {background-color: #94b0ff;}
        .progress .indeterminate {background-color: #2962FF;}
    </style>
</head>

<body>
<div class="row">
    <div class="col s12 m8 offset-m2 l4 offset-l4">
        <div class="card">
            <div class="card-action blue accent-4 white-text">
                <h3>Register</h3>
            </div>
            <div class="card-content">
                <form method="post">
                    <div class="row">
                        <div class="input-field col s12 m6">
                            <label for="first_name">First Name</label>
                            <input type="text" id="first_name" name="first_name" required/>
                        </div>
                        <div class="input-field col s12 m6">
                            <label for="last_name">Last Name</label>
                            <input type="text" id="last_name" name="last_name" required/>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col s12">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" required/>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col s12 m6">
                            <label for="password">Password</label>
                            <input type="password" id="password" name="password" required />
                        </div>
                        <div class="input-field col s12 m6">
                            <label for="password_confirm">Confirm Password</label>
                            <input type="password" id="password_confirm" name="password_confirm" required />
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col s12">
                            <label for="username">Username</label>
                            <input type="text" id="username" name="username" required/>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col s12">
                            <label for="picture_url">Picture URL</label>
                            <input type="text" id="picture_url" name="picture_url" required/>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col s12">
                            <div id="loaderContainer" class="progress">
                                <div class="indeterminate"></div>
                            </div>

                            <input type="hidden" name="CSRFtoken" value="<?= csrf_gen() ?>"/>
                            <input id="recaptchaResponse" type="hidden" name="g-recaptcha-response" value="null">
                            <button id="submitBtn" class="col s12 btn waves-effect blue accent-4" type="submit">Register</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="row">
            <div class="col s12">
                <div class="card-panel p-0">
                    <div class="col s12">
                        <a href="/" class="left">Login</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
    <script src="https://cdn.lucacastelnuovo.nl/general/js/materialize.js"></script>
    <script src="https://www.google.com/recaptcha/api.js?render=6LeuWIEUAAAAAF6aZy05cC5uNot2veX4IbsBxjza"></script>
    <script src="/js/register.js"></script>
    <?= alert_display() ?>
</body>

</html>
