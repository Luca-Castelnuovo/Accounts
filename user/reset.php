<?php

require($_SERVER['DOCUMENT_ROOT'] . '/includes/init.php');

if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']) {
    redirect('/home');
}

$token_GET = check_data($_REQUEST['token'], true, 'Token', true, true, '/');

$token = sql_select('general_tokens', 'revoked,expires,user_id', "token='{$token_GET}' AND type='reset_password'", true);

if ($token['expires'] <= time() || $token['revoked']) {
    redirect('/', 'Link already used or expired.');
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_val($_POST['CSRFtoken']);

    $recaptcha_response = check_data($_POST['g-recaptcha-response'], true, 'Recaptcha', true, true, '/user/register');
    $url = "https://www.google.com/recaptcha/api/siteverify?secret={$GLOBALS['config']->recaptcha->secret_key}&response={$recaptcha_response}";
    $response = json_decode(file_get_contents($url));

    if (!$response->success) {
        redirect('/user/reset?token=' . $token_GET, 'Please try again.');
    }

    $password = check_data($_POST['password'], true, 'Password', true, true, '/user/reset?token=' . $token_GET);
    $password_confirm = check_data($_POST['password_confirm'], true, 'Password Confirm', true, true, '/user/reset?token=' . $token_GET);

    // Check if passwords match
    if ($password !== $password_confirm) {
        redirect('/user/reset?token=' . $token_GET, "Passwords don't match.");
    }

    // Hash password
    $password = password_hash($password, PASSWORD_BCRYPT);

    sql_update('users', [
        'password' => $password
    ], "email='{$token['user_id']}'");

    sql_update('general_tokens', [
        'revoked' => '1'
    ], "token='{$token_GET}'");

    redirect('/', 'Password has been reset.');
}

?>
<!DOCTYPE html>
<html>

<head>
    <title>Forgot Password</title>

    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
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
                <h3>Forgot Password</h3>
            </div>
            <div class="card-content">
                <form method="post">
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
                        <div class="col s12">
                            <div id="loaderContainer" class="progress">
                                <div class="indeterminate"></div>
                            </div>

                            <input type="hidden" name="CSRFtoken" value="<?= csrf_gen() ?>"/>
                            <input id="recaptchaResponse" type="hidden" name="g-recaptcha-response" value="null">
                            <button id="submitBtn" class="col s12 btn waves-effect blue accent-4" type="submit">Set new password</button>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
    <script src="https://www.google.com/recaptcha/api.js?render=6LeuWIEUAAAAAF6aZy05cC5uNot2veX4IbsBxjza"></script>
    <script src="/js/reset.js"></script>
    <?= alert_display() ?>
</body>

</html>
