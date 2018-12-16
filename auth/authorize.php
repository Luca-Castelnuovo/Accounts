<?php

require($_SERVER['DOCUMENT_ROOT'] . '/includes/init.php');

// Validate user
loggedin();

// Required
$client_id = check_data($_GET['client_id'], true, 'Client_ID', true);

// Optional
$scopes = check_data($_GET['scope'], false, '', true);
$return_to = check_data($_GET['return_to'], false, '', true);
$state = check_data($_GET['state'], false, '', true);

// Validate state
if (isset($state) && !empty($state)) {
    if (strlen($state) > 128) {
        response(false, 'state_to_long_max_length_128');
    }
}

// Query client
$client = sql_select('clients', 'id,redirect_url,name,logo_url,description', "client_id='{$client_id}'", true);

// Validate client exists
if (!isset($client['id']) || empty($client['id'])) {
    response(false, 'incorrect_client_credentials');
}

// If not isset callback use default callback
if (empty($return_to)) {
    $return_to = $client['redirect_url'];
}

// List scopes
if (empty($scopes)) {
    $scope_array = ['user:email'];
} else {
    $scope_array = explode(',', $scopes);
}

// Query user
$user = sql_select('users', 'id,username,picture_url,applications', "id='{$_SESSION['id']}'", true);
$user_applications = json_decode($user['applications']);
if (!is_array($user_applications)) {
    $user_applications = [];
}

// Create authorization_token
if ($_SERVER['REQUEST_METHOD'] === 'POST' || in_array($client_id, $user_applications)) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $CSRFtoken = check_data($_POST['CSRFtoken'], true, 'CSRF token', true, true, "/auth/authorize?client_id={$client_id}&scope={$scopes}&return_to={$return_to}&state={$state}");
        $recaptcha_response = check_data($_POST['g-recaptcha-response'], true, 'Recaptcha response', true, true, "/auth/authorize?client_id={$client_id}&scope={$scopes}&return_to={$return_to}&state={$state}");

        // Recpatcha validation
        $url = "https://www.google.com/recaptcha/api/siteverify?secret={$GLOBALS['config']->recaptcha->secret_key}&response={$recaptcha_response}";
        $response = json_decode(file_get_contents($url));

        if (!$response->success) {
            redirect("/auth/authorize?client_id={$client_id}&scope={$scopes}&return_to={$return_to}&state={$state}", 'Please try again.');
        }
    }

    // Create authorization token
    $authorization_code = gen($GLOBALS['config']->auth->length->authorization_code);
    $expires = time() + $GLOBALS['config']->auth->expires->authorization_code;
    $scope_sql = json_encode($scope_array);

    sql_insert('authorization_codes', [
        'authorization_code' => $authorization_code,
        'client_id' => $client_id,
        'user_id' => $user['id'],
        'expires' => $expires,
        'scope' => $scope_sql,
        'state' => $state,
    ]);

    //add client_id to user apps
    if (!in_array($client_id, $user_applications)) {
        $new_applications = [$client_id => $scope_array];
        $user_applications = json_encode($user_applications + $new_applications);

        sql_update('users', ['applications' => $user_applications], "id='{$user['id']}'");
    }

    // Redirect user with authorization_code
    if (strpos($return_to, 'http://') === false && strpos($return_to, 'https://') === false) {
        $return_to = 'http://' . $return_to;
    }

    if (strpos($return_to, '?') !== false) {
        $return_to_glue = '&';
    } else {
        $return_to_glue = '?';
    }

    if (empty($state)) {
        redirect("{$return_to}{$return_to_glue}code={$authorization_code}");
    } else {
        redirect("{$return_to}{$return_to_glue}code={$authorization_code}&state={$state}");
    }
}

?>
<!DOCTYPE html>
<html>

<head>
    <title>Authorize Application</title>

    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css" rel="stylesheet" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <link href="/manifest.json" rel="manifest" />

    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="mask-icon" href="/safari-pinned-tab.svg" color="#2962FF">

    <style>
        .collection .collection-item.avatar {min-height: 0;}
        .progress {background-color: #94b0ff;}
        .progress .indeterminate {background-color: #2962FF;}
    </style>
</head>

<body>
    <div class="section">
        <div class="container">
            <div class="row center">
                <div class="row col s4 xl2"></div>
                <div class="row col s4 xl8">
                    <img class="responsive-img" src="<?= $client['logo_url'] ?>" onerror="this.src='https://github.com/identicons/<?= $client_id ?>.png'" width="150" />
                </div>
                <div class="row col s4 xl2"></div>
            </div>
            <div class="row center">
                <h4>Authorize <?= $client['name'] ?></h4>
            </div>
            <div class="row">
                <div class="row">
                    <ul class="collection">
                        <li class="collection-item avatar">
                            <img src="<?= $user['picture_url'] ?>" onerror="this.src='https://github.com/identicons/<?= $user['username'] ?>.png'" class="circle" />
                            <span class="title"><?= $client['name'] ?></span>
                            <p class="grey-text">wants to access your <b><?= $user['username'] ?></b> account</p>
                        </li>
                        <?php

                        foreach ($scope_array as $scope) {
                            $scope = check_data($scope, false, '', true);

                            $scope = sql_select('scopes', 'title,description,icon', "scope='{$scope}'", true);

                            if (isset($scope['title']) && !empty($scope['title'])) {
                                echo <<<HTML
                                <li class="collection-item avatar">
                                    <i class="material-icons circle blue accent-4">{$scope['icon']}</i>
                                    <span class="title">{$scope['title']}</span>
                                    <p>{$scope['description']}</p>
                                </li>
HTML;
                            }
                        }

                        ?>
                    </ul>
                </div>
                <div class="row">
                    <div class="row">
                        <div id="loaderContainer" class="progress">
                            <div class="indeterminate"></div>
                        </div>
                        <form action="<?= $_SERVER['REQUEST_URI'] ?>" method="POST">
                            <input type="hidden" name="CSRFtoken" value="<?= csrf_gen() ?>">
                            <input id="recaptchaResponse" type="hidden" name="g-recaptcha-response" value="null">
                            <button id="authorizeBtn" class="col s12 btn-large waves-effect blue accent-4">Authorize <?= $client['name'] ?></button>
                        </form>
                    </div>
                    <div id="authorizeRedirect" class="row">
                        <p class="center grey-text">
                            Authorizing will redirect to
                            <br>
                            <b><?= strtok($return_to, '?') ?></b>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
    <script src="https://www.google.com/recaptcha/api.js?render=6LeuWIEUAAAAAF6aZy05cC5uNot2veX4IbsBxjza"></script>
    <script src="/js/authorize.js"></script>
    <?= alert_display() ?>
</body>

</html>
