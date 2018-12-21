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
                <th>Email (with link)</th>
                <th>Verified  (toggle btn)</th>
                <th>Developer (toggle btn)</th>
                <th>Admin  (toggle btn)</th>
                <th>Created</th>
            </tr>
        </thead>

        <tbody>
            <tr>
                <td><img src="https://avatars3.githubusercontent.com/u/26206253"></td>
                <td>ltcastelnuovo</td>
                <td><a href="mailto:ltcastelnuovo@gmail.com">ltcastelnuovo@gmail.com</a></td>
                <td>1</td>
                <td>1</td>
                <td>1</td>
                <td>2018-12-14 19:09:47</td>
            </tr>
        </tbody>
    </table>
</div>

<?= page_footer(); ?>
