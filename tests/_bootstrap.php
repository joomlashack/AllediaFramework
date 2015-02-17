<?php
// This is global bootstrap for autoloading
define('ALLEDIAFRAMEWORK_SOURCE_PATH', __DIR__ . '/../src');

$config = json_decode(file_get_contents(__DIR__ . '/config.json'));

// Load the bootstrap file from Alledia Builder
require_once $config->allediaBuilderPath . '/src/codeception/_bootstrap.php';

// Load Alledia Framework
require_once ALLEDIAFRAMEWORK_SOURCE_PATH . '/include.php';
