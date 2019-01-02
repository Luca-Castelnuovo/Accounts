<?php

//Reponse to client
function response($success, $message, $extra = null)
{
    $output = ["success" => $success];

    if (isset($message) && !empty($message)) {
        if ($success) {
            $output = array_merge($output, ["message" => $message]);
        } else {
            $output = array_merge($output, ["error" => $message]);
        }
    }

    if (!empty($extra)) {
        $output = array_merge($output, $extra);
    }

    echo json_encode($output);
    exit;
}


//Make POST request
function request($url, $data)
{
    $curl = curl_init();

    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $result = curl_exec($curl);
    curl_close($curl);
    $result = json_decode($result, true);

    return $result;
}


//Redirect user
function redirect($to, $alert = null)
{
    if (!empty($alert)) {
        alert_set($alert);
    }

    header('location: ' . $to);
    exit;
}


// Send mails to users
function send_mail($to, $subject, $body)
{
    $access_token = request(
        'https://accounts.lucacastelnuovo.nl/auth/token',
        [
            "grant_type" => "client_credentials",
            "client_id" => "{$GLOBALS['config']->client_id}",
            "client_secret" => "{$GLOBALS['config']->client_secret}"
        ]
    )['access_token'];

    return request(
        'https://api.lucacastelnuovo.nl/mail/',
        [
            "access_token" => "{$access_token}",
            "to" => "{$to}",
            "subject" => "{$subject}",
            "body" => "{$body}",
            "from_name" => "LTC Auth"
        ]
    );
}


// Log stuff
function log_action($action, $client_id = 'not_set', $user_id = 'not_set') {
    $action = clean_data($action);
    $time = date('Y-m-d H:i:s');
    $user_id = clean_data($user_id);
    $client_id = clean_data($client_id);
    $ip = $_SERVER['REMOTE_ADDR'];

    sql_insert('general_tokens', [
        'action' => $action,
        'time' => $time,
        'user_id' => $user_id,
        'client_id' => $client_id,
        'ip' => $ip
    ]);
}


##################
# Message System #
##################

function alert_set($alert)
{
    if (isset($_SESSION)) {
        $_SESSION['alert'] = $alert;
    } else {
        response(false, 'Session not started.');
    }
}


function alert_display()
{
    if (isset($_SESSION)) {
        if (isset($_SESSION['alert']) && !empty($_SESSION['alert'])) {
            echo "<script>M.toast({html: \"{$_SESSION['alert']}\"});</script>";
            unset($_SESSION['alert']);
        }
    }
}
