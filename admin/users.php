<?php

require($_SERVER['DOCUMENT_ROOT'] . '/includes/init.php');

loggedin_admin();

page_header('Users');

?>

<div class="row">
    <h4>Users</h4>
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Item Name</th>
                <th>Item Price</th>
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