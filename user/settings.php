<?php

require($_SERVER['DOCUMENT_ROOT'] . '/includes/init.php');

loggedin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    user_update($_POST['CSRFtoken'], $_SESSION['id'], $_POST['username'], $_POST['password'], $_POST['password_confirm'], $_POST['first_name'], $_POST['last_name'], $_POST['email'], $_POST['picture_url']);
}

$user = sql_select('users', 'first_name,last_name,email,username,picture_url', "id='{$_SESSION['id']}'", true);

page_header('Settings');

?>

<style>
    .input-field input:focus + label {
        color: #2962FF !important;
    }

    .input-field input:focus {
        border-bottom: 1px solid #2962FF !important;
        box-shadow: 0 1px 0 0 #2962FF !important;
    }
</style>
<div class="row">
    <h4>Settings</h4>
    <form method="post">
        <div id="settings-user">
            <div class="row">
                <div class="input-field col s12 m6">
                    <label for="first_name">First Name</label>
                    <input type="text" id="first_name" name="first_name" required value="<?= $user['first_name'] ?>"/>
                </div>
                <div class="input-field col s12 m6">
                    <label for="last_name">Last Name</label>
                    <input type="text" id="last_name" name="last_name" required value="<?= $user['last_name'] ?>"/>
                </div>
            </div>
            <div class="row">
                <div class="input-field col s12">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required value="<?= $user['email'] ?>"/>
                </div>
            </div>
            <div class="row">
                <div class="input-field col s12">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required value="<?= $user['username'] ?>"/>
                </div>
            </div>
            <div class="row">
                <div class="input-field col s12">
                    <label for="picture_url">Picture URL</label>
                    <input type="text" id="picture_url" name="picture_url" required value="<?= $user['picture_url'] ?>"/>
                </div>
            </div>

            <div class="row">
                <div class="col s12">
                    <a id="settings-user-btn" class="col s12 btn-small waves-effect blue accent-4">Change password</a>
                </div>
            </div>
        </div>

        <div id="settings-password" class="hide">
            <div class="row">
                <div class="input-field col s12">
                    <label for="password">Old Password</label>
                    <input type="password" id="password" name="password" />
                </div>
            </div>
            <div class="row">
                <div class="input-field col s12 m6">
                    <label for="password">New Password</label>
                    <input type="password" id="password" name="password" />
                </div>
                <div class="input-field col s12 m6">
                    <label for="password_confirm">Confirm New Password</label>
                    <input type="password" id="password_confirm" name="password_confirm" />
                </div>
            </div>

            <div class="row">
                <div class="col s12">
                    <a id="settings-password-btn" class="col s12 btn-small waves-effect blue accent-4">Change other settings</a>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col s12">
                <input type="hidden" name="CSRFtoken" value="<?= csrf_gen() ?>"/>
                <button class="col s12 btn waves-effect blue accent-4" type="submit">Update Account</button>
            </div>
        </div>
    </form>
</div>
<script src="/js/settings.js"></script>

<?= page_footer(); ?>
