<?php

function applications_list($user_id)
{
    echo '<ul class="collection">';

    $user_applications = sql_select('users', 'applications', "id='{$user_id}'", true)['applications'];

    foreach ($user_applications as $user_application) {
        $client = sql_select('clients', 'redirect_url,user_id,name,logo_url,description,suspended', "client_id='{$user_application}'", true);
        $owner = sql_select('users', 'first_name,last_name', "id='{$client['user_id']}'", true);

        echo <<<HTML
        <li class="collection-item avatar">
            <img class="circle" src="https://{$allowed_domain}/favicon.ico" onerror="this.src='https://betaplus.ams3.cdn.digitaloceanspaces.com/luca/images/not_found.png'"> <span class="title">{$domain_title}</span>
            <p>{$allowed_domain}</p>
            <p>
                <a href="?goto={$allowed_domain}&CSRFtoken={$CSRFtoken}" class="btn-small waves-effect waves-light orange" target="_blank">Open</a>
            </p>
            <a class="secondary-content" href="?delete={$allowed_domain}&CSRFtoken={$CSRFtoken}" onclick="return confirm('Are you sure?')"><i class="material-icons orange-text">delete</i></a>
        </li>
HTML;
    }

    echo '</ul>';
}

function application_list($client_id)
{
    //
}

function application_revoke($client_id, $CSRFtoken)
{
    //
}
