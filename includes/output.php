<?php

//Reponse to client
function response($success, $message, $extra = null)
{
    $output = ["success" => $success, "message" => strtolower($message)];

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
