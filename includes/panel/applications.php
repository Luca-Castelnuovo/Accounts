<?php

function applications_list($user_id)
{
    echo '<ul class="collection">';

    $user_applications = sql_select('users', 'applications', "id='{$user_id}'", true)['applications'];

    var_dump($user_applications);
    exit;

    foreach ($user_applications as $user_application) {
        $client = sql_select('clients', 'redirect_url,user_id,name,logo_url,description,suspended', "client_id='{$user_application}'", true);
        $owner = sql_select('users', 'username', "id='{$client['user_id']}'", true);

        echo <<<HTML
        <li class="collection-item avatar">
            <img class="circle" src="{$client['logo_url']}" onerror="this.src='https://github.com/identicons/{$user_application}.png'"> <span class="title">{$client['name']}</span>
            <p>Owned by <span class="blue-text">{$owner['username']}</span></p>
        </li>
HTML;
    }

    echo '</ul>';
}

function application_info($client_id)
{
    //
}

function application_revoke($client_id, $CSRFtoken)
{
    //
}
