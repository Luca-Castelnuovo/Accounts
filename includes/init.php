<?php

if (!$no_session) {
    session_start();
}

require_once __DIR__ . '/../vendor/autoload.php';

$GLOBALS['config'] = require $_SERVER['DOCUMENT_ROOT'] . '/includes/config.php';

require $_SERVER['DOCUMENT_ROOT'] . '/includes/output.php';
require $_SERVER['DOCUMENT_ROOT'] . '/includes/security.php';
require $_SERVER['DOCUMENT_ROOT'] . '/includes/sql.php';

require $_SERVER['DOCUMENT_ROOT'] . '/includes/panel/applications.php';
require $_SERVER['DOCUMENT_ROOT'] . '/includes/panel/authentication.php';
require $_SERVER['DOCUMENT_ROOT'] . '/includes/panel/clients.php';
require $_SERVER['DOCUMENT_ROOT'] . '/includes/panel/security.php';
require $_SERVER['DOCUMENT_ROOT'] . '/includes/panel/template.php';

require $_SERVER['DOCUMENT_ROOT'] . '/includes/auth/validation.php';

// External
require '/var/www/logs.lucacastelnuovo.nl/logs.php';
