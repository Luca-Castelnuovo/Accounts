<?php

require($_SERVER['DOCUMENT_ROOT'] . '/includes/init.php');

loggedin();

page_header('Application');

if (isset($_GET['revoke']) && !empty($_GET['revoke']) && isset($_GET['CSRFtoken']) && !empty($_GET['CSRFtoken'])) {
    application_revoke($_SESSION['id'], $_GET['revoke'], $_GET['CSRFtoken']);
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
    redirect('/home');
}

?>

<div class="row">
    <?= application_info($_SESSION['id'], $_GET['id']); ?>
</div>

<?= page_footer(); ?>
