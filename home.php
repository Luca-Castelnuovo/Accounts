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
    <div class="row">
        <div class="col s12 m9">
            <h4>Your clients</h4>
        </div>
        <div class="col s12 m9">
            <a href="/client/add" class="btn-large waves-effect blue accent-4">Create client</a>
        </div>
    </div>
    <?= clients_list($_SESSION['id']); ?>
</div>

<?= page_footer(); ?>
