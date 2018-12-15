<?php

require($_SERVER['DOCUMENT_ROOT'] . '/includes/init.php');

session_start();

loggedin();

page_header('Home');

?>

<div class="row">
    <h4>Authorized Apps</h4>
    <?php

    echo '<ul class="collection">';

    $authorized_applications = allowed_domains($_SESSION['token']);

    $query =
        "SELECT
            client_id";

    sort($allowed_domains);

    foreach ($allowed_domains as $allowed_domain) {
        if ($allowed_domain != 'account.lucacastelnuovo.nl') {
            $domain_title = ucfirst(explode('.', $allowed_domain)[0]);
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
    }

    echo '</ul>';

    ?>
</div>

<?= page_footer(); ?>
