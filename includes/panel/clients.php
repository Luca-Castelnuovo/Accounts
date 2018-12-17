<?php

function clients_list($user_id)
{
    echo '<ul class="collection">';

    $user_id = clean_data($user_id);
    $clients = sql_select('clients', 'name,logo_url,description', "user_id='{$user_id}'", false);

    while ($client = $clients->fetch_assoc()) {
        echo <<<HTML
        <li class="collection-item avatar">
            <a href="/client?id={$client_id}">
                <img class="circle" src="{$client['logo_url']}" onerror="this.src='https://github.com/identicons/{$client_id}.png'"> <span class="title">{$client['name']}</span>
                <p>{$client['description']}</p>
            </a>
        </li>
HTML;
    }

    echo '</ul>';
}


function client_info($user_id, $client_id)
{
    $user_id = clean_data($user_id);
    $client_id = clean_data($client_id);

    $client = sql_select('clients', 'client_id,client_secret,redirect_uri,logo_url,description,suspended', "client_id='{$client_id}'", true);

    if (!isset($client['client_id']) || empty($client['client_id'])) {
        redirect('/home');
    }

    $CSRFtoken = csrf_gen();

    echo <<<HTML
    <style>
        .mb-0{margin-bottom:0}
    </style>
    <div class="row mb-0">
        <div class="col s12 m2">
            <img class="responsive-img" src="{$client['logo_url']}" onerror="this.src='https://github.com/identicons/{$client_id}.png'" width="75">
        </div>
        <div class="col s12 m10">
            <h4>
                {$client['name']}
            </h4>
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="noform">
            <div class="row">
                <div class="input-field col s12 m6">
                    <label for="client_id">Client ID</label>
                    <input type="text" id="client_id" value="{$client['client_id']}" />
                </div>
                <div class="input-field col s12 m6">
                    <label for="client_secret">Client Secret</label>
                    <input type="text" id="client_secret" value="{$client['client_id']}"/>
                </div>
            </div>
            <div class="row">
                <a href="?reset={$client_id}&CSRFtoken={$CSRFtoken}" class="btn-small waves-effect red accent-4" onclick="return confirm('Are you sure?')">Reset client secret</a>
            </div>
        </div>
        <form method="post">
            <div class="row">
                <div class="input-field col s12">
                    <label for="logo_url">Logo URL</label>
                    <input type="text" id="logo_url" name="logo_url" required value="{$client['logo_url']}"/>
                </div>
            </div>
            <div class="row">
                <div class="input-field col s12">
                    <label for="name">Client name</label>
                    <input type="text" id="name" name="name" required value="{$client['name']}"/>
                </div>
            </div>
            <div class="row">
                <div class="input-field col s12">
                    <label for="description">Description</label>
                    <input type="text" id="description" name="description" required value="{$client['description']}"/>
                </div>
            </div>
            <div class="row">
                <div class="input-field col s12">
                    <label for="redirect_uri">Redirect URI</label>
                    <input type="email" id="redirect_uri" name="redirect_uri" required value="{$client['redirect_uri']}"/>
                </div>
            </div>

            <div class="row">
                <div class="col s12">
                    <input type="hidden" name="CSRFtoken" value="{$CSRFtoken}"/>
                    <button class="col s12 btn-large waves-effect blue accent-4" type="submit">Update Client</button>
                </div>
            </div>
            <div class="row">
                <div class="col s12">
                    <a href="?delete={$client_id}&CSRFtoken={$CSRFtoken}" class="col s12 btn-small waves-effect red accent-4" onclick="return confirm('Are you sure?')">Delete Client</a>
                </div>
            </div>
        </form>
    </div>
    <div class="row">
        <a href="/home"><i class="material-icons">arrow_back</i> Go back</a>
    </div>
HTML;
}


function client_delete($user_id, $client_id, $CSRFtoken)
{
    csrf_val($CSRFtoken);

    $user_id = clean_data($user_id);
    $client_id = clean_data($client_id);

    //delete client and all codes,tokens associated with id

    redirect('/home', 'Client deleted.');
}


function client_reset($user_id, $client_id, $CSRFtoken)
{
    csrf_val($CSRFtoken);

    $user_id = clean_data($user_id);
    $client_id = clean_data($client_id);

    //reset secret

    redirect('/client?id=' . $client_id, 'Client secret reset.');
}
