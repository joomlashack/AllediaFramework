<?php
// This is global bootstrap for autoloading
define('ALLEDIAFRAMEWORK_SOURCE_PATH', __DIR__ . '/../src');

// Load the bootstrap file from Alledia Builder
require_once '/builder/src/codeception/_bootstrap.php';

// Load Alledia Framework
require_once ALLEDIAFRAMEWORK_SOURCE_PATH . '/include.php';
