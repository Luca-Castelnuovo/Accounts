<?php

require($_SERVER['DOCUMENT_ROOT'] . '/includes/init.php');

loggedin_admin();

if (!empty($_GET['delete']) && !empty($_GET['CSRFtoken'])) {
    csrf_val($_GET['CSRFtoken'], '/admin/clients');

    $client_id = clean_data($_GET['delete']);
    $client = sql_select('clients', 'user_id', "client_id='{$client_id}'", true);

    sql_delete('clients', "client_id='{$client_id}'");
    sql_delete('access_tokens', "client_id='{$client_id}'");
    sql_delete('authorization_codes', "client_id='{$client_id}'");

    $users_with_client = sql_select('users', 'id', "applications LIKE '%{$client_id}%'", false);

    while ($user_with_client = $users_with_client->fetch_assoc()) {
        $user = sql_select('users', 'applications', "id='{$user_with_client['id']}'", true);
        $applications = json_decode($user['applications'], true);
        unset($applications[$client_id]);
        sql_update('users', ['applications' => $applications], "id='{$user_with_client['id']}'");
    }

    redirect('/admin/clients', 'Client deleted.');
}

page_header('Clients');

?>

<div class="row">
    <style>i {
        color: #2962FF;
    }
    </style>
    <ul class="collection">
        <?php

        $clients = sql_select('clients', 'client_id,user_id,name,logo_url,description', "true", false);

        $CSRFtoken = csrf_gen();

        while ($client = $clients->fetch_assoc()) {
            $description = nl2br($client['description']);
            $user = sql_select('users', 'id,username', "id='{$client['user_id']}'", true);

            echo <<<HTML
            <li class="collection-item avatar">
                <img class="circle" src="{$client['logo_url']}" onerror="this.src='https://github.com/identicons/{$client['client_id']}.png'">
                <span class="title">{$client['name']} - By <a href="/admin/users#{$user['id']}">{$user['username']}</a></span>
                <p>{$description}</p>
                <a href="/admin/clients?delete={$client['client_id']}&CSRFtoken={$CSRFtoken}" class="secondary-content icon-blue accent-4"><i class="material-icons">delete</i></a>
            </li>
HTML;
        }
        ?>
    </ul>
</div>

<?= page_footer(); ?>
