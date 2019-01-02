<?php

require($_SERVER['DOCUMENT_ROOT'] . '/includes/init.php');

loggedin_admin();

page_header('Logs');

?>

<div class="row">
    <table class="responsive-table">
        <thead>
            <tr>
                <th>Action</th>
                <th>Time</th>
                <th>User_ID</th>
                <th>Client_ID</th>
                <th>IP</th>
            </tr>
        </thead>

        <tbody>
            <?php
            $logs = sql_select('logs', 'id,action,time,user_id,client_id,ip', "true ORDER BY time DESC", false);

            while ($log_item = $logs->fetch_assoc()) {
                echo <<<HTML
                <tr id="{$log_item['id']}">
                    <td>{$log_item['action']}</td>
                    <td>{$log_item['time']}</td>
                    <td>{$log_item['user_id']}</td>
                    <td>{$log_item['client_id']}</td>
                    <td>{$log_item['ip']}</td>
                </tr>
HTML;
            }
            ?>
        </tbody>
    </table>
</div>

<?= page_footer(); ?>
