<?php

require($_SERVER['DOCUMENT_ROOT'] . '/includes/init.php');

loggedin();

page_header('Home');
?>

<?php
if (isset($_GET['application']) && !empty($_GET['application'])) {
    ?>
    <div class="row">
        <?= application_info($_GET['application']); ?>
    </div>
<?php
} else {
        ?>
<div class="row">
    <h4>Authorized Apps</h4>
    <?= applications_list($_SESSION['id']); ?>
</div>

<?php
    } ?>

<?= page_footer(); ?>
