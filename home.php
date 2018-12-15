<?php

require($_SERVER['DOCUMENT_ROOT'] . '/includes/init.php');

session_start();

loggedin();

page_header('Home');

echo $_SESSION['id'];

page_footer();
