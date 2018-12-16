<?php

session_start();

$GLOBALS['config'] = require $_SERVER['DOCUMENT_ROOT'] . '/includes/config.php';

require $_SERVER['DOCUMENT_ROOT'] . '/includes/output.php';
require $_SERVER['DOCUMENT_ROOT'] . '/includes/security.php';
require $_SERVER['DOCUMENT_ROOT'] . '/includes/sql.php';

require $_SERVER['DOCUMENT_ROOT'] . '/includes/panel/applications.php';
require $_SERVER['DOCUMENT_ROOT'] . '/includes/panel/authentication.php';
require $_SERVER['DOCUMENT_ROOT'] . '/includes/panel/clients.php';
require $_SERVER['DOCUMENT_ROOT'] . '/includes/panel/security.php';
require $_SERVER['DOCUMENT_ROOT'] . '/includes/panel/template.php';
require $_SERVER['DOCUMENT_ROOT'] . '/includes/panel/user.php';

require $_SERVER['DOCUMENT_ROOT'] . '/includes/auth/validation.php';
