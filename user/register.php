<?php

require($_SERVER['DOCUMENT_ROOT'] . '/includes/init.php');

if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']) {
    redirect('/home');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    user_register($_POST['CSRFtoken'], $_POST['username'], $_POST['password'], $_POST['password_confirm'], $_POST['first_name'], $_POST['last_name'], $_POST['email'], $_POST['picture_url']);
}

?>
<!DOCTYPE html>
<html>

<head>
    <title>Register</title>

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
                            <input type="hidden" name="CSRFtoken" value="<?= csrf_gen() ?>"/>
                            <button class="col s12 btn waves-effect blue accent-4" type="submit">Register</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="row">
            <div class="col s12">
                <div class="card-panel p-0">
                    <div class="col s12 m6">
                        <a href="/" class="left">Login</a>
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
