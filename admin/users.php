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
                <td>Alvin</td>
                <td>Eclair</td>
                <td>$0.87</td>
            </tr>
            <tr>
                <td>Alan</td>
                <td>Jellybean</td>
                <td>$3.76</td>
            </tr>
            <tr>
                <td>Jonathan</td>
                <td>Lollipop</td>
                <td>$7.00</td>
            </tr>
        </tbody>
    </table>
</div>

<?= page_footer(); ?>
