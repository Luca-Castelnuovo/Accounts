<?php

function user_register($CSRFtoken, $recaptcha_response, $username, $password, $password_confirm, $first_name, $last_name, $email, $picture_url)
{
    csrf_val($CSRFtoken);

    $recaptcha_response = check_data($recaptcha_response, true, 'Username', true, true, '/user/register');
    $url = "https://www.google.com/recaptcha/api/siteverify?secret={$GLOBALS['config']->recaptcha->secret_key}&response={$recaptcha_response}";
    $response = json_decode(file_get_contents($url));

    if (!$response->success) {
        redirect('/user/register', 'Please try again.');
    }

    $username = check_data($username, true, 'Username', true, true, '/user/register');
    $password = check_data($password, true, 'Password', true, true, '/user/register');
    $password_confirm = check_data($password_confirm, true, 'Password Confirm', true, true, '/user/register');
    $first_name = check_data($first_name, true, 'First Name', true, true, '/user/register');
    $last_name = check_data($last_name, true, 'Last Name', true, true, '/user/register');
    $email = check_data($email, true, 'Email', true, true, '/user/register');
    $picture_url = check_data($picture_url, false, '', true);
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
        redirect('/user/register', 'Passwords don\'t match.');
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
    $expires = time() + 31449600;
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
    <!DOCTYPE html><html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office"><head><title></title> <!--[if !mso]><!-- --><meta http-equiv="X-UA-Compatible" content="IE=edge"> <!--<![endif]--><meta http-equiv="Content-Type" content="text/html; charset=UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><style type="text/css">#outlook a{padding:0}.ReadMsgBody{width:100%}.ExternalClass{width:100%}.ExternalClass *{line-height:100%}body{margin:0;padding:0;-webkit-text-size-adjust:100%;-ms-text-size-adjust:100%}table,td{border-collapse:collapse;mso-table-lspace:0pt;mso-table-rspace:0pt}img{border:0;height:auto;line-height:100%;outline:none;text-decoration:none;-ms-interpolation-mode:bicubic}p{display:block;margin:13px 0}</style><!--[if !mso]><!--><style type="text/css">@media only screen and (max-width:480px){@-ms-viewport{width:320px}@viewport{width:320px}}</style><!--<![endif]--><!--[if mso]><xml> <o:OfficeDocumentSettings> <o:AllowPNG/> <o:PixelsPerInch>96</o:PixelsPerInch> </o:OfficeDocumentSettings></xml><![endif]--><!--[if lte mso 11]><style type="text/css">.outlook-group-fix{width:100% !important}</style><![endif]--><!--[if !mso]><!--><link href="https://fonts.googleapis.com/css?family=Ubuntu:300,400,500,700" rel="stylesheet" type="text/css"><style type="text/css">@import url(https://fonts.googleapis.com/css?family=Ubuntu:300,400,500,700);</style><!--<![endif]--><style type="text/css">@media only screen and (min-width:480px){.mj-column-per-100{width:100%!important}}</style></head><body style="background: #FFFFFF;"><div class="mj-container" style="background-color:#FFFFFF;"><!--[if mso | IE]><table role="presentation" border="0" cellpadding="0" cellspacing="0" width="600" align="center" style="width:600px;"><tr><td style="line-height:0px;font-size:0px;mso-line-height-rule:exactly;"> <![endif]--><div style="margin:0px auto;max-width:600px;"><table role="presentation" cellpadding="0" cellspacing="0" style="font-size:0px;width:100%;" align="center" border="0"><tbody><tr><td style="text-align:center;vertical-align:top;direction:ltr;font-size:0px;padding:9px 0px 9px 0px;"><!--[if mso | IE]><table role="presentation" border="0" cellpadding="0" cellspacing="0"><tr><td style="vertical-align:top;width:600px;"> <![endif]--><div class="mj-column-per-100 outlook-group-fix" style="vertical-align:top;display:inline-block;direction:ltr;font-size:13px;text-align:left;width:100%;"><table role="presentation" cellpadding="0" cellspacing="0" style="vertical-align:top;" width="100%" border="0"><tbody><tr><td style="word-wrap:break-word;font-size:0px;padding:0px 0px 0px 0px;" align="center"><table role="presentation" cellpadding="0" cellspacing="0" style="border-collapse:collapse;border-spacing:0px;" align="center" border="0"><tbody><tr><td style="width:150px;"><a href="https://accounts.lucacastelnuovo.nl" target="_blank"><img alt="Logo || LTC Auth" title="" height="auto" src="./favicon.png" style="border:none;border-radius:0px;display:block;font-size:13px;outline:none;text-decoration:none;width:100%;height:auto;" width="150"></a></td></tr></tbody></table></td></tr></tbody></table></div><!--[if mso | IE]></td></tr></table> <![endif]--></td></tr></tbody></table></div><!--[if mso | IE]></td></tr></table> <![endif]--> <!--[if mso | IE]><table role="presentation" border="0" cellpadding="0" cellspacing="0" width="600" align="center" style="width:600px;"><tr><td style="line-height:0px;font-size:0px;mso-line-height-rule:exactly;"> <![endif]--><div style="margin:0px auto;max-width:600px;"><table role="presentation" cellpadding="0" cellspacing="0" style="font-size:0px;width:100%;" align="center" border="0"><tbody><tr><td style="text-align:center;vertical-align:top;direction:ltr;font-size:0px;padding:9px 0px 9px 0px;"><!--[if mso | IE]><table role="presentation" border="0" cellpadding="0" cellspacing="0"><tr><td style="vertical-align:top;width:600px;"> <![endif]--><div class="mj-column-per-100 outlook-group-fix" style="vertical-align:top;display:inline-block;direction:ltr;font-size:13px;text-align:left;width:100%;"><table role="presentation" cellpadding="0" cellspacing="0" width="100%" border="0"><tbody><tr><td style="word-wrap:break-word;font-size:0px;padding:15px 15px 15px 15px;" align="left"><div style="cursor:auto;color:#000000;font-family:Ubuntu, Helvetica, Arial, sans-serif;font-size:11px;line-height:1.5;text-align:left;"><p><span style="font-size:20px;">Almost done,&#xA0;<strong>{$first_name} {$last_name}</strong>! To complete your sign up, we need to verify your email address: <b>{$email}</b>.</span></p></div></td></tr></tbody></table></div><!--[if mso | IE]></td></tr></table> <![endif]--></td></tr></tbody></table></div><!--[if mso | IE]></td></tr></table> <![endif]--> <!--[if mso | IE]><table role="presentation" border="0" cellpadding="0" cellspacing="0" width="600" align="center" style="width:600px;"><tr><td style="line-height:0px;font-size:0px;mso-line-height-rule:exactly;"> <![endif]--><div style="margin:0px auto;max-width:600px;"><table role="presentation" cellpadding="0" cellspacing="0" style="font-size:0px;width:100%;" align="center" border="0"><tbody><tr><td style="text-align:center;vertical-align:top;direction:ltr;font-size:0px;padding:9px 0px 9px 0px;"><!--[if mso | IE]><table role="presentation" border="0" cellpadding="0" cellspacing="0"><tr><td style="vertical-align:top;width:600px;"> <![endif]--><div class="mj-column-per-100 outlook-group-fix" style="vertical-align:top;display:inline-block;direction:ltr;font-size:13px;text-align:left;width:100%;"><table role="presentation" cellpadding="0" cellspacing="0" width="100%" border="0"><tbody><tr><td style="word-wrap:break-word;font-size:0px;padding:0px 20px 0px 20px;" align="center"><table role="presentation" cellpadding="0" cellspacing="0" style="border-collapse:separate;width:100%;" align="center" border="0"><tbody><tr><td style="border:0px solid #000;border-radius:24px;color:#fff;cursor:auto;padding:14px 40px;" align="center" valign="middle" bgcolor="#2962FF"><a href="https://accounts.lucacastelnuovo.nl/user/verify?token={$token}" style="text-decoration:none;background:#2962FF;color:#fff;font-family:Ubuntu, Helvetica, Arial, sans-serif, Helvetica, Arial, sans-serif;font-size:20px;font-weight:normal;line-height:120%;text-transform:none;margin:0px;" target="_blank">Verify email address</a></td></tr></tbody></table></td></tr></tbody></table></div><!--[if mso | IE]></td></tr></table> <![endif]--></td></tr></tbody></table></div><!--[if mso | IE]></td></tr></table> <![endif]--> <!--[if mso | IE]><table role="presentation" border="0" cellpadding="0" cellspacing="0" width="600" align="center" style="width:600px;"><tr><td style="line-height:0px;font-size:0px;mso-line-height-rule:exactly;"> <![endif]--><div style="margin:0px auto;max-width:600px;"><table role="presentation" cellpadding="0" cellspacing="0" style="font-size:0px;width:100%;" align="center" border="0"><tbody><tr><td style="text-align:center;vertical-align:top;direction:ltr;font-size:0px;padding:9px 0px 9px 0px;"><!--[if mso | IE]><table role="presentation" border="0" cellpadding="0" cellspacing="0"><tr><td style="vertical-align:top;width:600px;"> <![endif]--><div class="mj-column-per-100 outlook-group-fix" style="vertical-align:top;display:inline-block;direction:ltr;font-size:13px;text-align:left;width:100%;"><table role="presentation" cellpadding="0" cellspacing="0" width="100%" border="0"><tbody><tr><td style="word-wrap:break-word;font-size:0px;padding:15px 15px 15px 15px;" align="left"><div style="cursor:auto;color:#000000;font-family:Ubuntu, Helvetica, Arial, sans-serif;font-size:11px;line-height:1.5;text-align:left;"><p><span style="font-size:12px;"><span style="color:#999999;">Button not working? Paste the following link into your browser:&#xA0;</span><a href="https://accounts.lucacastelnuovo.nl/user/verify?token={$token}"><span style="color:#999999;">https://accounts.lucacastelnuovo.nl/user/verify?token={$token}</span></a></span></p><p></p><p><span style="color:#999999;"><span style="font-size:12px;">You&#x2019;re receiving this email because you recently created a new account. If this wasn&#x2019;t you, please ignore this email.</span></span></p></div></td></tr></tbody></table></div><!--[if mso | IE]></td></tr></table> <![endif]--></td></tr></tbody></table></div><!--[if mso | IE]></td></tr></table> <![endif]--></div></body></html>
HTML;

    send_mail($to, $subject, $body);

    redirect('/', 'Account registered. Please verify your mail.');
}


function user_update($CSRFtoken, $user_id, $username, $first_name, $last_name, $email, $picture_url, $password =null, $new_password = null, $new_password_confirm = null)
{
    csrf_val($CSRFtoken);

    $username = check_data($username, true, 'Username', true, true, '/user/settings');
    $first_name = check_data($first_name, true, 'First Name', true, true, '/user/settings');
    $last_name = check_data($last_name, true, 'Last Name', true, true, '/user/settings');
    $email = check_data($email, true, 'Email', true, true, '/user/settings');
    $picture_url = check_data($picture_url, true, 'Picture', true, true, '/user/settings');

    $user = sql_select('users', 'username,email,password', "username='{$username}' OR email='{$email}'", true);

    // Check if username is taken
    if ($username != $user['username']) {
        $existing_username = sql_select('users', 'id', "username='{$username}'", false);
        if ($existing_username->num_rows > 0) {
            redirect('/user/settings', 'Username is taken, please choose another.');
        }
    }

    // Check if email is taken
    if ($email != $user['email']) {
        $existing_email = sql_select('users', 'id', "email='{$email}'", false);
        if ($existing_email->num_rows > 0) {
            redirect('/user/settings', 'Email is taken, please choose another.');
        }
    }

    // Check if user wants to update password
    if (!empty($password)) {
        $password = check_data($password, true, 'Old Password', true, true, '/user/settings');
        $new_password = check_data($new_password, true, 'New Password', true, true, '/user/settings');
        $new_password_confirm = check_data($new_password_confirm, true, 'New Password Confirm', true, true, '/user/settings');

        // Verify old password
        if (!password_verify($password, $user['password'])) {
            redirect('/user/settings', 'Current password is incorrect.');
        }

        // Verify new password
        if ($new_password === $new_password_confirm) {
            $new_password = password_hash($new_password, PASSWORD_BCRYPT);

            sql_update('users', [
                'password' => $new_password
            ], "id='{$user_id}'");
        }
    }

    sql_update('users', [
        'username' => $username,
        'first_name' => $first_name,
        'last_name' => $last_name,
        'email' => $email,
        'picture_url' => $picture_url,
    ], "id='{$user_id}'");

    redirect('/user/register', 'Account updated.');
}


function user_delete($CSRFtoken, $user_id)
{
    csrf_val($CSRFtoken);

    $user_id = check_data($user_id, true, 'User ID', true, true, '/user/settings');

    sql_delete('users', "id='{$user_id}'");

    redirect('/?reset', 'Account deleted.');
}
