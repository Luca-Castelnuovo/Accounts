<?php

require($_SERVER['DOCUMENT_ROOT'] . '/includes/init.php');

loggedin_developer();

page_header('Client');

if (isset($_GET['delete']) && !empty($_GET['delete']) && isset($_GET['CSRFtoken']) && !empty($_GET['CSRFtoken'])) {
    client_delete($_GET['CSRFtoken'], $_SESSION['id'], $_GET['delete']);
}

if (isset($_GET['reset']) && !empty($_GET['reset']) && isset($_GET['CSRFtoken']) && !empty($_GET['CSRFtoken'])) {
    client_reset($_GET['CSRFtoken'], $_SESSION['id'], $_GET['reset']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    client_update($_POST['CSRFtoken'], $_SESSION['id'], $_POST['client_id'], $_POST['logo_url'], $_POST['name'], $_POST['description'], $_POST['redirect_uri']);
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
    redirect('/home');
}

?>

<style>
    .input-field input:focus + label {color: #2962FF !important;}

    .input-field input:focus {
        border-bottom: 1px solid #2962FF !important;
        box-shadow: 0 1px 0 0 #2962FF !important;
    }

    .input-field textarea:focus + label {
        color: #2962FF !important;
    }

    .row .input-field textarea:focus {
        border-bottom: 1px solid #2962FF !important;
        box-shadow: 0 1px 0 0 #2962FF !important
    }

    .mb-0{
        margin-bottom:0
    }
</style>
<div class="row">
    <?= client_info($_SESSION['id'], $_GET['id']); ?>
</div>

<?= page_footer(); ?>
