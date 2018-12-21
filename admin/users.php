<?php

require($_SERVER['DOCUMENT_ROOT'] . '/includes/init.php');

loggedin_admin();

if (isset($_GET['CSRFtoken']) && isset($_GET['id']) && isset($_GET['type']) && isset($_GET['status'])) {
    $id = check_data($_GET['id'], false, '', true);
    $status = check_data($_GET['status'], false, '', true);
    switch ($_GET['type']) {
        case 'verified':
            sql_update('users', [
                'email_verified' => $status
            ], "id='{$id}'");
            redirect("/admin/users#{$id}", 'User updated.');
            break;
        case 'developer':
            sql_update('users', [
                'developer' => $status
            ], "id='{$id}'");
            redirect("/admin/users#{$id}", 'User updated.');
            break;
        case 'admin':
            sql_update('users', [
                'admin' => $status
            ], "id='{$id}'");
            redirect("/admin/users#{$id}", 'User updated.');
            break;
    }
}

page_header('Users');

?>

<div class="row">
    <table class="responsive-table">
        <thead>
            <tr>
                <th>Picture</th>
                <th>Username</th>
                <th>Email</th>
                <th>Verified</th>
                <th>Developer</th>
                <th>Admin</th>
                <th>Created</th>
            </tr>
        </thead>

        <tbody>
            <?php
            $users = sql_select('users', 'id,username,email,email_verified,picture_url,created,applications,developer,admin', "true", false);

            while ($user = $users->fetch_assoc()) {
                $CSRFtoken = csrf_gen();

                if ($user['email_verified']) {
                    $verified_class = 'green';
                    $verified_text = 'True';
                    $verified_toggle = '0';
                } else {
                    $verified_class = 'red';
                    $verified_text = 'False';
                    $verified_toggle = '1';
                }

                if ($user['developer']) {
                    $developer_class = 'green';
                    $developer_text = 'True';
                    $developer_toggle = '0';
                } else {
                    $developer_class = 'red';
                    $developer_text = 'False';
                    $developer_toggle = '1';
                }

                if ($user['admin']) {
                    $admin_class = 'green';
                    $admin_text = 'True';
                    $admin_toggle = '0';
                } else {
                    $admin_class = 'red';
                    $admin_text = 'False';
                    $admin_toggle = '1';
                }

                echo <<<HTML
                <tr id="{$user['id']}">
                    <td><img src="{$user['picture_url']}" class="responsive-img" width="100"></td>
                    <td>{$user['username']}</td>
                    <td><a href="mailto:{$user['email']}">{$user['email']}</a></td>
                    <td><a href="/admin/users?CSRFtoken={$CSRFtoken}&id={$user['id']}&type=verified&status={$verified_toggle}" class="btn waves-effect {$verified_class} accent-4">{$verified_text}</a></td>
                    <td><a href="/admin/users?CSRFtoken={$CSRFtoken}&id={$user['id']}&type=developer&status={$developer_toggle}" class="btn waves-effect {$developer_class} accent-4">{$developer_text}</a></td>
                    <td><a href="/admin/users?CSRFtoken={$CSRFtoken}&id={$user['id']}&type=admin&status={$admin_toggle}" class="btn waves-effect {$admin_class} accent-4">{$admin_text}</a></td>
                    <td>{$user['created']}</td>
                </tr>
HTML;
            }
            ?>
        </tbody>
    </table>
</div>

<?= page_footer(); ?>
