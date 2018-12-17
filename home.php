<?php

require($_SERVER['DOCUMENT_ROOT'] . '/includes/init.php');

loggedin();

page_header('Home');

?>

<style>
    .mt-1_52rem{margin-top:1.52rem}
</style>
<div class="row">
    <h4>Authorized Apps</h4>
    <?= applications_list($_SESSION['id']); ?>
</div>
<div class="row">
    <div class="row">
        <div class="col s12 m9">
            <h4>Your clients</h4>
        </div>
        <div class="col s12 m3">
            <a href="/client/add" class="btn waves-effect blue accent-4 mt-1_52rem">Create client</a>
        </div>
    </div>
    <?= clients_list($_SESSION['id']); ?>
</div>

<?= page_footer(); ?>
