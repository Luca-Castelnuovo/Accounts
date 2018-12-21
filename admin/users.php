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
            ], "username='{$id}'");
            break;
        case 'developer':
            sql_update('users', [
                'developer' => $status
            ], "username='{$id}'");
            break;
        case 'admin':
            sql_update('users', [
                'admin' => $status
            ], "username='{$id}'");
            break;
}
}

page_header('Users');

?>

<div class="row">
    <table>
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
            $users = sql_select('users', 'id,username,email,email_verified,picture_url,created,applications,developer,admin', "true", true);

            while ($user = $users->fetch_assoc()) {
                $CSRFtoken = csrf_gen();

                if ($user['email_verified']) {
                    $verified_class = 'green';
                    $verified_text = 'True';
                    $verified_toggle = 'false';
                } else {
                    $verified_class = 'red';
                    $verified_text = 'False';
                    $verified_toggle = 'true';
                }

                if ($user['developer']) {
                    $developer_text = 'green';
                    $developer_class = 'True';
                    $developer_toggle = 'false';
                } else {
                    $developer_text = 'red';
                    $developer_class = 'False';
                    $developer_toggle = 'true';
                }

                if ($user['admin']) {
                    $admin_class = 'green';
                    $admin_text = 'True';
                    $admin_toggle = 'false';
                } else {
                    $admin_class = 'red';
                    $admin_text = 'False';
                    $admin_toggle = 'true';
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
            <tr id="12">
                <td><img src="https://avatars3.githubusercontent.com/u/26206253" class="responsive-img" width="100"></td>
                <td>ltcastelnuovo</td>
                <td><a href="mailto:ltcastelnuovo@gmail.com">ltcastelnuovo@gmail.com</a></td>
                <td><a href="#!" class="btn waves-effect green accent-4">True</a></td>
                <td><a href="#!" class="btn waves-effect red accent-4">False</a></td>
                <td><a href="#!" class="btn waves-effect green accent-4">True</a></td>
                <td>2018-12-14 19:09:47</td>
            </tr>
        </tbody>
    </table>
</div>

<?= page_footer(); ?>
