<?php

require($_SERVER['DOCUMENT_ROOT'] . '/includes/init.php');

loggedin();

page_header('Home');

?>

<div class="row">
    <h4>Authorized Apps</h4>
    <?= applications_list($_SESSION['id']); ?>
</div>
<div class="row">
    <h4>Your clients</h4>
    <?= clients_list($_SESSION['id']); ?>
</div>

<?= page_footer(); ?>
