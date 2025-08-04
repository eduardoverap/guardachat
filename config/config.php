<?php
// Set UTF-8 encoding
mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');
mb_regex_encoding('UTF-8');

// Set timezone
date_default_timezone_set('America/Lima');

// App paths
define('APP_ROOT', __DIR__ . '/..');
define('BASE_URL', str_replace('public/index.php', '', $_SERVER['PHP_SELF']));
define('DB_PATH', APP_ROOT . '/database/app.db');
define('DB_CONN', APP_ROOT . '/config/database.php');
define('JOURNAL_LOG', APP_ROOT . '/storage/journal.log');

// Global constants
define('CID_REGEX', '/^[a-z0-9]{8}-[a-z0-9]{4}-[a-z0-9]{4}-[a-z0-9]{4}-[a-z0-9]{12}$/');
