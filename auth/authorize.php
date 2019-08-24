<?php

require($_SERVER['DOCUMENT_ROOT'] . '/includes/init.php');

// Validate user
loggedin();

// Required
$client_id = check_data($_GET['client_id'], true, 'Client_ID', true);

// Optional
$scopes = check_data($_GET['scope'], false, '', true);
$redirect_uri = check_data($_GET['redirect_uri'], false, '', true);
$state = check_data($_GET['state'], false, '', true);

// Validate state
if (isset($state) && !empty($state)) {
    if (strlen($state) > 128) {
        response(false, 'state_too_long_max_length_128');
    }
}

// Query client
$client = sql_select('clients', 'id,redirect_uri,name,logo_url,description', "client_id='{$client_id}'", true);

// Validate client exists
if (!isset($client['id']) || empty($client['id'])) {
    response(false, 'incorrect_client_credentials');
}

// If not isset callback use default callback
if (empty($redirect_uri)) {
    $redirect_uri = $client['redirect_uri'];
} else {
    if (substr($redirect_uri, 0, strlen($client['redirect_uri'])) !== $client['redirect_uri']) {
        redirect("{$client['redirect_uri']}?error=redirect_uri_mismatch");
    }
}

// List scopes
if (!empty($scopes)) {
    $scope_array = explode(',', $scopes);
} else {
    $scope_array = [];
}

if (!in_array('basic:read', $scope_array) && !in_array('basic', $scope_array)) {
    array_push($scope_array, 'basic:read');
}

$scope_array = array_unique($scope_array);

foreach ($scope_array as $scope) {
    $scope = check_data($scope, false, '', true);
    $scope_sql = sql_select('scopes', 'id,title,description,icon', "scope='{$scope}'", true);

    if (!isset($scope_sql['id'])) {
        if (($key = array_search($scope, $scope_array)) !== false) {
            unset($scope_array[$key]);
        }
    }
}

// Query user
$user = sql_select('users', 'id,username,picture_url,applications', "id='{$_SESSION['id']}'", true);
$user_applications = json_decode($user['applications'], true);
if (!is_array($user_applications)) {
    $user_applications = [];
}

// Create authorization_token
$scope_intersect = array_values(array_intersect($user_applications[$client_id], $scope_array));
sort($scope_intersect);
sort($scope_array);
if (array_key_exists($client_id, $user_applications) && $scope_intersect == $scope_array) {
    $user_application_match = true;
} else {
    $user_application_match = false;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' || $user_application_match) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        csrf_val($_POST['CSRFtoken'], "/auth/authorize?client_id={$client_id}&scope={$scopes}&redirect_uri={$redirect_uri}&state={$state}");
        captcha_val($_POST['g-recaptcha-response'], "/auth/authorize?client_id={$client_id}&scope={$scopes}&redirect_uri={$redirect_uri}&state={$state}");
    }

    // Create authorization token
    $authorization_code = gen($GLOBALS['config']->auth->length->authorization_code);
    $expires = expires($GLOBALS['config']->auth->expires->authorization_code);
    $scope_sql = json_encode($scope_array);

    sql_insert('authorization_codes', [
        'authorization_code' => $authorization_code,
        'client_id' => $client_id,
        'user_id' => $user['id'],
        'expires' => $expires,
        'scope' => $scope_sql,
        'state' => $state,
    ]);

    //add client_id to user apps (http://sandbox.onlinephpfunctions.com/code/290eb12087de69c3f08d7be132c3100123a75e81)
    if (!array_key_exists($client_id, $user_applications) || !$user_application_match) {
        // Fix scope being null
        if (!is_array($user_applications[$client_id])) {
            $user_applications[$client_id] = [];
        }

        //Merge requested scopes with existing scopes
        $not_unique_scopes = array_merge($user_applications[$client_id], $scope_array);

        // Remove duplicate scope
        $unique_scopes = array_unique($not_unique_scopes);

        // Remove the old scope for client_id
        unset($user_applications[$client_id]);

        // Turn new scopes into assoc array
        $unique_scopes_assoc = [$client_id => $unique_scopes];

        // Merge other clients with current client
        $user_applications = json_encode($user_applications + $unique_scopes_assoc);

        // Update user
        sql_update('users', ['applications' => $user_applications], "id='{$user['id']}'");
    }

    // Redirect user with authorization_code
    if (strpos($redirect_uri, 'http://') === false && strpos($redirect_uri, 'https://') === false) {
        $redirect_uri = 'http://' . $redirect_uri;
    }

    if (strpos($redirect_uri, '?') !== false) {
        $redirect_uri_glue = '&';
    } else {
        $redirect_uri_glue = '?';
    }

    if (empty($state)) {
        redirect("{$redirect_uri}{$redirect_uri_glue}code={$authorization_code}");
    } else {
        redirect("{$redirect_uri}{$redirect_uri_glue}code={$authorization_code}&state={$state}");
    }
}

?>
<!DOCTYPE html>
<html>

<head>
    <title>Authorize Application</title>

    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />
    <link rel="stylesheet" href="<?= cdnPath('/general/css/materialize.css') ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>

    <link rel="shortcut icon" href="/images/favicon.ico">
    <link rel="apple-touch-icon" sizes="180x180" href="/images/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/images/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/images/favicon-16x16.png">
    <link rel="mask-icon" href="/images/safari-pinned-tab.svg" color="#2962ff">
    <link rel="manifest" href="/site.webmanifest">

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
                            <a href="<?= strtok($redirect_uri, '?') ?>?error=authorization_denied" id="authorizeBtnDeny" class="col s12 m5 btn-large waves-effect white black-text">Deny</a>
                            <div class="col m2"></div>
                            <button id="authorizeBtnAllow" class="col s12 m5 btn-large waves-effect blue accent-4">Allow</button>
                        </form>
                    </div>
                    <div id="authorizeRedirect" class="row">
                        <p class="center grey-text">
                            Authorizing will redirect to
                            <br>
                            <b><?= strtok($redirect_uri, '?') ?></b>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="<?= cdnPath('/general/js/materialize.js') ?>"></script>
    <script src="https://www.google.com/recaptcha/api.js?render=6LeuWIEUAAAAAF6aZy05cC5uNot2veX4IbsBxjza"></script>
    <script src="/js/authorize.js"></script>
    <?= alert_display() ?>
</body>

</html>
