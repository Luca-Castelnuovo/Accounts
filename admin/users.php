<?php

require($_SERVER['DOCUMENT_ROOT'] . '/includes/init.php');

loggedin_admin();

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
