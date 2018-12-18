<?php

require($_SERVER['DOCUMENT_ROOT'] . '/includes/init.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = check_data($_POST['username'], true, 'Username', true, true, '/');
    $password = check_data($_POST['password'], true, 'Password', true, true, '/');

    csrf_val($_POST['CSRFtoken'], '/');
    captcha_val($_POST['g-recaptcha-response'], '/');

    $user = sql_select('users', 'id,password,email_verified', "username='{$username}' OR email='{$username}'", true);

    if (!password_verify($password, $user['password'])) {
        redirect('/', 'Username or password is incorrect.');
    }

    if (!$user['email_verified']) {
        redirect('/', 'Account not verified, please check your mail.');
    }

    if ($_POST['rememberme']) {
        $expires = expires($GLOBALS['config']->auth->expires->remember_me);
        $token = gen(256);

        sql_insert('general_tokens', [
            'revoked' => '0',
            'type' => 'remember_me',
            'user_id' => $username,
            'expires' => $expires,
            'token' => $token
        ]);

        $cookie = $user['id'] . ':' . $token . ':' . hash_hmac('sha512', $cookie, $GLOBALS['config']->security->hmac);

        cookie_set('REMEMBERME', $cookie, $expires);
    }

    $_SESSION['logged_in'] = true;
    $_SESSION['ip'] = $_SERVER['REMOTE_ADDR'];
    $_SESSION['id'] = $user['id'];

    $redirect_uri = url_decode($_GET['redirect_uri']);

    session_regenerate_id(true);

    if ($redirect_uri) {
        redirect($redirect_uri);
    } else {
        redirect('/home', 'You are logged in.');
    }
}

if (isset($_GET['logout'])) {
    logout();
}

if (isset($_GET['reset'])) {
    reset_session();
}

if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']) {
    if (isset($_GET['redirect_uri'])) {
        $url = url_decode($_GET['redirect_uri']);
        redirect($url);
    } else {
        redirect('/home');
    }
}

if (isset($_COOKIE['REMEMBERME']) && !empty($_COOKIE['REMEMBERME'])) {
    rememberme($_GET['redirect_uri']);
}

?>
<!DOCTYPE html>
<html>

<head>
    <title>Sign In</title>

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
        .input-field input:focus + label {
            color: #2962FF !important;
        }

        .input-field input:focus {
            border-bottom: 1px solid #2962FF !important;
            box-shadow: 0 1px 0 0 #2962FF !important;
        }

        [type="checkbox"].filled-in:checked + span:not(.lever):after {
           border: 2px solid #2962FF !important;
           background-color: #2962FF !important;
        }

        .p-0 {
           padding: 0;
        }

        .progress {background-color: #94b0ff;}
        .progress .indeterminate {background-color: #2962FF;}
    </style>
</head>

<body>
<div class="row">
    <div class="col s12 m8 offset-m2 l4 offset-l4">
        <div class="card">
            <div class="card-action blue accent-4 white-text">
                <h3>Login</h3>
            </div>
            <div class="card-content">
                <form method="post">
                    <div class="row">
                        <div class="input-field col s12">
                            <label for="username">Username or Email</label>
                            <input type="text" id="username" name="username" required/>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col s12">
                            <label for="password">Password</label>
                            <input type="password" id="password" name="password" required />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col s12">
                            <label>
                                <input type="checkbox" class="filled-in" name="rememberme" value="true"/>
                                <span>Remember Me</span>
                            </label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col s12">
                            <div id="loaderContainer" class="progress">
                                <div class="indeterminate"></div>
                            </div>

                            <input type="hidden" name="CSRFtoken" value="<?= csrf_gen() ?>"/>
                            <input id="recaptchaResponse" type="hidden" name="g-recaptcha-response" value="null">
                            <button id="submitBtn" class="col s12 btn waves-effect blue accent-4" type="submit">Login</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="row">
            <div class="col s12">
                <div class="card-panel p-0">
                    <div class="col s12 m6">
                        <a href="/user/register" class="left">Register</a>
                    </div>
                    <div class="col s12 m6">
                        <a href="/user/forgot" class="right">Forgot Password</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
    <script src="https://www.google.com/recaptcha/api.js?render=6LeuWIEUAAAAAF6aZy05cC5uNot2veX4IbsBxjza"></script>
    <script src="/js/login.js"></script>
    <?= alert_display() ?>
</body>

</html>
