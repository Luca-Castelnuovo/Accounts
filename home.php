<?php

require($_SERVER['DOCUMENT_ROOT'] . '/includes/init.php');

loggedin();

page_header('Home');

if (isset($_GET['revoke']) && !empty($_GET['revoke']) && isset($_GET['CSRFtoken']) && !empty($_GET['CSRFtoken'])) {
    application_revoke($_SESSION['id'], $_GET['revoke'], $_GET['CSRFtoken']);
}

?>

<?php
if (isset($_GET['application']) && !empty($_GET['application'])) {
    ?>
    <div class="row">
        <?= application_info($_GET['application']); ?>
    </div>
<?php
} else {
        ?>
<div class="row">
    <h4>Authorized Apps</h4>
    <?= applications_list($_SESSION['id']); ?>
</div>

<?php
    } ?>

<?= page_footer(); ?>
