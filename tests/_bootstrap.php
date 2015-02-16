<?php
// This is global bootstrap for autoloading
define('ALLEDIAFRAMEWORK_SOURCE_PATH', __DIR__ . '/../src');

// Load the bootstrap file from Alledia Test Framework
$config = json_decode(file_get_contents(__DIR__ . '/config.json'));
require_once $config->allediaTestFrameworkPath . '/tests/bootstrap_joomla3.php';

// Load Alledia Framework
require_once ALLEDIAFRAMEWORK_SOURCE_PATH . '/include.php';
