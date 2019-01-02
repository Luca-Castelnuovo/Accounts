<?php

require($_SERVER['DOCUMENT_ROOT'] . '/includes/init.php');

loggedin();

if (isset($$_GET['destroy_remember_me']) && isset($_GET['CSRFtoken'])) {
    csrf_val($_GET['CSRFtoken'], '/settings');

    sql_update('general_tokens', [
        'revoked' => '1'
    ], "user_id='{$_SESSION['id']}' AND type='remember_me'");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_val($_POST['CSRFtoken'], '/settings');

    $user_id = $_SESSION['id'];
    $first_name = check_data($_POST['first_name'], true, 'First Name', true, true, '/user/settings');
    $last_name = check_data($_POST['last_name'], true, 'Last Name', true, true, '/user/settings');
    $email = check_data($_POST['email'], true, 'Email', true, true, '/user/settings');
    $picture_url = check_data($_POST['picture_url'], true, 'Picture', true, true, '/user/settings');

    $user = sql_select('users', 'username,email,password', "username='{$username}' OR email='{$email}'", true);

    // Check if email is taken
    if ($email != $user['email']) {
        $existing_email = sql_select('users', 'id', "email='{$email}'", false);
        if ($existing_email->num_rows > 0) {
            redirect('/user/settings', 'Email is taken, please choose another.');
        }
    }

    // Check if user wants to update password
    if (!empty($_POST['password'])) {
        $password = check_data($_POST['password'], true, 'Old Password', true, true, '/user/settings');
        $new_password = check_data($_POST['password_confirm'], true, 'New Password', true, true, '/user/settings');
        $new_password_confirm = check_data($_POST['new_password_confirm'], true, 'New Password Confirm', true, true, '/user/settings');

        // Verify old password
        if (!password_verify($password, $user['password'])) {
            redirect('/user/settings', 'Current password is incorrect.');
        }

        // Verify new password
        if ($new_password === $new_password_confirm) {
            $new_password = password_hash($new_password, PASSWORD_BCRYPT);

            sql_update('users', [
                'password' => $new_password
            ], "id='{$user_id}'");
        }
    }

    sql_update('users', [
        'first_name' => $first_name,
        'last_name' => $last_name,
        'email' => $email,
        'picture_url' => $picture_url,
    ], "id='{$user_id}'");

    redirect('/user/register', 'Account updated.');
}

$user = sql_select('users', 'first_name,last_name,email,picture_url', "id='{$_SESSION['id']}'", true);

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
                    <label for="password_confirm">New Password</label>
                    <input type="password" id="password_confirm" name="password_confirm" />
                </div>
                <div class="input-field col s12 m6">
                    <label for="new_password_confirm">Confirm New Password</label>
                    <input type="password" id="new_password_confirm" name="new_password_confirm" />
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
                <button class="col s12 btn-large waves-effect blue accent-4" type="submit">Update Account</button>
            </div>
        </div>

        <div class="row">
            <div class="col s12">
                <a class="col s12 btn-large waves-effect blue accent-4" href="?destroy_remember_me&CSRFtoken=<?= csrf_gen() ?>" onclick="return confirm('Are you sure?')">Log out on other devices</a>
            </div>
        </div>
    </form>
</div>
<script src="/js/settings.js"></script>

<?= page_footer(); ?>
