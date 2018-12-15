<?php

function client_validation($client_id, $client_secret)
{
    $client_id = check_data($client_id, true, 'client_id', true);
    $client_secret = check_data($client_secret, true, 'client_secret', true);

    $client = sql_select('clients', 'client_secret', "client_id='{$client_id}'", true);

    if ($client['client_secret'] != $client_secret) {
        response(false, 'incorrect_client_credentials');
    } else {
        return ['status' => true, 'id' => $client_id, 'secret' => $client_secret];
    }
}
