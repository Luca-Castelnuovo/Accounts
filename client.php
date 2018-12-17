<?php

require($_SERVER['DOCUMENT_ROOT'] . '/includes/init.php');

loggedin();

page_header('Client');

if (isset($_GET['delete']) && !empty($_GET['delete']) && isset($_GET['CSRFtoken']) && !empty($_GET['CSRFtoken'])) {
    client_delete($_SESSION['id'], $_GET['delete'], $_GET['CSRFtoken']);
}

if (isset($_GET['reset']) && !empty($_GET['reset']) && isset($_GET['CSRFtoken']) && !empty($_GET['CSRFtoken'])) {
    client_reset($_SESSION['id'], $_GET['reset'], $_GET['CSRFtoken']);
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
    redirect('/home');
}

?>

<div class="row">
    <?= client_info($_SESSION['id'], $_GET['id']); ?>
</div>

<?= page_footer(); ?>
