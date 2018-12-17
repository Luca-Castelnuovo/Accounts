<?php

require($_SERVER['DOCUMENT_ROOT'] . '/includes/init.php');

$client_id = check_data($_GET['client_id'], false, '', true);

if (isset($client_id) && !empty($client_id)) {
    $client = sql_select('clients', 'name,profile_url', 'client_id=' . $client_id, true);

    if (!isset($client['name']) || !isset($client['profile_url'])) {
        redirect('/');
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = check_data($_POST['username'], true, 'Username', true, true, '/');
    $password = check_data($_POST['password'], true, 'Password', true, true, '/');
    $rememberme = check_data($_POST['rememberme'], false, '', true);

    $user = sql_select('users', 'id,password,email_verified', "username='{$username}' OR email='{$username}'", true);

    if (!password_verify($password, $user['password'])) {
        redirect('/', 'Username or password is incorrect.');
    }

    if (!$user['email_verified']) {
        redirect('/', 'Account not verified, please check your mail.');
    }

    if ($rememberme) {
        // TODO: create remember system
        //cookie_set('REMEMBERME', $value, 2592000);
    }

    $_SESSION['logged_in'] = true;
    $_SESSION['ip'] = $_SERVER['REMOTE_ADDR'];
    $_SESSION['id'] = $user['id'];

    $return_to = url_decode($_GET['return_to']);

    if ($return_to) {
        redirect($return_to);
    } else {
        redirect('/home', 'You are logged in.');
    }
}

if (isset($_GET['deleteRemember']) || isset($_GET['logout'])) {
    cookie_delete('REMEMBERME');
}

if (isset($_GET['logout'])) {
    logout();
}

if (isset($_GET['reset'])) {
    reset_session();
}

if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']) {
    if (isset($_GET['return_to'])) {
        $url = url_decode($_GET['return_to']);
        redirect($url);
    } else {
        redirect('/home');
    }
}

if (isset($_COOKIE['REMEMBERME']) && !empty($_COOKIE['REMEMBERME'])) {
    rememberme();
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
                                <input type="checkbox" class="filled-in" name="remember" value="true"/>
                                <span>Remember Me</span>
                            </label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col s12">
                            <input type="hidden" name="CSRFtoken" value="<?= csrf_gen() ?>"/>
                            <button class="col s12 btn waves-effect blue accent-4" type="submit">Login</button>
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
    <?= alert_display() ?>
</body>

</html>
