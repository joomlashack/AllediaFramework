<?php
// This is global bootstrap for autoloading
define('ALLEDIAFRAMEWORK_SOURCE_PATH', __DIR__ . '/../src');

$config = json_decode(file_get_contents(__DIR__ . '/config.json'));

// Load the bootstrap file from Alledia Test Framework
require_once $config->allediaTestFrameworkPath . '/tests/_bootstrap.php';

// Load Alledia Framework
require_once ALLEDIAFRAMEWORK_SOURCE_PATH . '/include.php';
