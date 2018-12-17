<?php

require($_SERVER['DOCUMENT_ROOT'] . '/includes/init.php');

loggedin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    user_register($_POST['CSRFtoken'], $_POST['username'], $_POST['password'], $_POST['password_confirm'], $_POST['first_name'], $_POST['last_name'], $_POST['email'], $_POST['picture_url']);
}

page_header('Settings');

?>

<div class="row">
    <h4>Settings</h4>
    <form method="post">
        <div id="settings-user">
            <div class="row">
                <div class="input-field col s12 m6">
                    <label for="first_name">First Name</label>
                    <input type="text" id="first_name" name="first_name" required/>
                </div>
                <div class="input-field col s12 m6">
                    <label for="last_name">Last Name</label>
                    <input type="text" id="last_name" name="last_name" required/>
                </div>
            </div>
            <div class="row">
                <div class="input-field col s12">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required/>
                </div>
            </div>
            <div class="row">
                <div class="input-field col s12">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required/>
                </div>
            </div>
            <div class="row">
                <div class="input-field col s12">
                    <label for="picture_url">Picture URL</label>
                    <input type="text" id="picture_url" name="picture_url" required/>
                </div>
            </div>

            <div class="row">
                <div class="col s12">
                    <a id="settings-password-btn" class="col s12 btn-small waves-effect blue accent-4">Change password</a>
                </div>
            </div>
        </div>

        <div id="settings-password">
            <div class="row">
                <div class="input-field col s12">
                    <label for="password">Old Password</label>
                    <input type="password" id="password" name="password" required />
                </div>
            </div>
            <div class="row">
                <div class="input-field col s12 m6">
                    <label for="password">New Password</label>
                    <input type="password" id="password" name="password" required />
                </div>
                <div class="input-field col s12 m6">
                    <label for="password_confirm">Confirm New Password</label>
                    <input type="password" id="password_confirm" name="password_confirm" required />
                </div>
            </div>

            <div class="row">
                <div class="col s12">
                    <a id="settings-user-btn" class="col s12 btn-small waves-effect blue accent-4">Change other settings</a>
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

<?= page_footer(); ?>
