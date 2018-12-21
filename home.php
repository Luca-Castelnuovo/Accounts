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
<?php if ($_SESSION['developer']) { ?>
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
<?php } ?>
<?php if ($_SESSION['admin']) { ?>
<div class="row">
    <h4>Admin Panel</h4>
    <div class="row">
        <a href="/admin/users" class="col s12 m5 btn waves-effect blue accent-4">Users</a>
        <div class="col m2"></div>
        <a href="/admin/clients" class="col s12 m5 btn waves-effect blue accent-4">Clients</a>
    </div>
    <div class="row">
        <a href="/admin/revoke?CSRFtoken=<?= csrf_gen() ?>&type=access_token" class="col s12 m5 btn waves-effect blue accent-4" onclick="return confirm('Are you sure?')">Revoke access tokens</a>
        <div class="col m2"></div>
        <a href="/admin/revoke?CSRFtoken=<?= csrf_gen() ?>&type=authorization_code" class="col s12 m5 btn waves-effect blue accent-4" onclick="return confirm('Are you sure?')">Revoke authorization codes</a>
    </div>
    <div class="row">
        <a href="/admin/revoke?CSRFtoken=<?= csrf_gen() ?>&type=all" class="col s12 btn waves-effect red accent-4" onclick="return confirm('Are you sure?')">Revoke access tokens, authorization codes, user applications</a>
    </div>
</div>
<?php } ?>

<?= page_footer(); ?>
