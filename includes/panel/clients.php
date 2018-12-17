<?php

function clients_list($user_id)
{
    echo '<ul class="collection">';

    $user_id = clean_data($user_id);
    $clients = sql_select('clients', 'client_id,name,logo_url,description', "user_id='{$user_id}'", false);

    while ($client = $clients->fetch_assoc()) {
        echo <<<HTML
        <li class="collection-item avatar">
            <a href="/client?id={$client['client_id']}">
                <img class="circle" src="{$client['logo_url']}" onerror="this.src='https://github.com/identicons/{$client['client_id']}.png'"> <span class="title">{$client['name']}</span>
            </a>
            <p>{$client['description']}</p>
        </li>
HTML;
    }

    echo '</ul>';
}


function client_info($user_id, $client_id)
{
    $user_id = clean_data($user_id);
    $client_id = clean_data($client_id);

    $client = sql_select('clients', 'client_id,client_secret,redirect_uri,logo_url,description,suspended,name', "client_id='{$client_id}'", true);

    if (!isset($client['client_id']) || empty($client['client_id'])) {
        redirect('/home');
    }

    $CSRFtoken = csrf_gen();

    echo <<<HTML
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
                <div class="input-field col s12">
                    <label for="client_id">Client ID</label>
                    <input type="text" id="client_id" value="{$client['client_id']}" readonly />
                </div>
            </div>
            <div class="row">
                <div class="input-field col s12 m9">
                    <label for="client_secret">Client Secret</label>
                    <input type="text" id="client_secret" value="{$client['client_secret']}" readonly />
                </div>
                <div class="col s12 m3">
                    <a href="?reset={$client_id}&CSRFtoken={$CSRFtoken}" class="btn-large waves-effect red accent-4" onclick="return confirm('Are you sure?')">Reset client secret</a>
                </div>
            </div>
        </div>
        <div class="section"></div>
        <form method="post">
            <div class="row">
                <div class="input-field col s12">
                    <label for="name">Client name</label>
                    <input type="text" id="name" name="name" required value="{$client['name']}"/>
                </div>
            </div>
            <div class="row">
                <div class="input-field col s12">
                    <label for="redirect_uri">Redirect URI</label>
                    <input type="text" id="redirect_uri" name="redirect_uri" required value="{$client['redirect_uri']}"/>
                </div>
            </div>
            <div class="row">
                <div class="input-field col s12">
                    <label for="logo_url">Logo URL</label>
                    <input type="text" id="logo_url" name="logo_url" value="{$client['logo_url']}"/>
                </div>
            </div>
            <div class="row">
                <div class="input-field col s12">
                    <label for="description">Description</label>
                    <input type="text" id="description" name="description" value="{$client['description']}"/>
                </div>
            </div>

            <div class="row">
                <input type="hidden" name="client_id" value="{$client['client_id']}"/>
                <input type="hidden" name="CSRFtoken" value="{$CSRFtoken}"/>
                <div class="col s12 m8">
                    <button class="col s12 btn-large waves-effect blue accent-4" type="submit">Update Client</button>
                </div>
                <div class="col s12 m4">
                    <a href="?delete={$client_id}&CSRFtoken={$CSRFtoken}" class="col s12 btn-large waves-effect red accent-4" onclick="return confirm('Are you sure?')">Delete Client</a>
                </div>
            </div>
        </form>
    </div>
HTML;
}


function client_delete($CSRFtoken, $user_id, $client_id)
{
    csrf_val($CSRFtoken);

    $user_id = clean_data($user_id);
    $client_id = clean_data($client_id);

    $client = sql_select('clients', 'user_id', "client_id='{$client_id}'", true);

    if ($client['user_id'] != $user_id) {
        redirect('/home');
    }

    sql_delete('clients', "client_id='{$client_id}'");
    sql_delete('access_tokens', "client_id='{$client_id}'");
    sql_delete('authorization_codes', "client_id='{$client_id}'");

    redirect('/home', 'Client deleted.');
}


function client_reset($CSRFtoken, $user_id, $client_id)
{
    csrf_val($CSRFtoken);

    $user_id = clean_data($user_id);
    $client_id = clean_data($client_id);

    $client = sql_select('clients', 'user_id', "client_id='{$client_id}'", true);

    if ($client['user_id'] != $user_id) {
        redirect('/home');
    }

    $client_secret = gen(64);

    sql_update('clients', ['client_secret' => $client_secret], "client_id='{$client_id}'");

    redirect('/client?id=' . $client_id, 'Client secret reset.');
}

function client_update($CSRFtoken, $user_id, $client_id, $logo_url, $name, $description, $redirect_uri)
{
    csrf_val($CSRFtoken);

    $user_id = clean_data($user_id);
    $client_id = clean_data($client_id);

    $client = sql_select('clients', 'user_id', "client_id='{$client_id}'", true);

    if ($client['user_id'] != $user_id) {
        redirect('/home');
    }

    $logo_url = clean_data($logo_url);
    $name = clean_data($name);
    $description = clean_data($description);
    $redirect_uri = clean_data($redirect_uri);

    sql_update('clients', [
        'logo_url' => $logo_url,
        'name' => $name,
        'description' => $description,
        'redirect_uri' => $redirect_uri,
    ], "client_id='{$client_id}'");
}

function client_create($CSRFtoken, $user_id, $logo_url, $name, $description, $redirect_uri)
{
    csrf_val($CSRFtoken);

    $client_id = gen(32);
    $client_secret = gen(64);

    $redirect_uri = clean_data($redirect_uri);
    $user_id = clean_data($user_id);
    $name = clean_data($name);
    $logo_url = clean_data($logo_url);
    $description = clean_data($description);

    sql_insert('clients', [
        'client_id' => $client_id,
        'client_secret' => $client_secret,
        'redirect_uri' => $redirect_uri,
        'user_id' => $user_id,
        'name' => $name,
        'logo_url' => $logo_url,
        'description' => $description,
    ]);

    redirect('/home', 'Client created');
}
