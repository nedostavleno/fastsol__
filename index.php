<?php
/**
 * BelovTest
 * Init
 */
error_reporting(true);
@ini_set('display_errors', true);
@ini_set('html_errors', true);

define('ROOT_DIR', __DIR__);
define('APP_DIR', ROOT_DIR . '/App');
define('DESIGN_DIR', ROOT_DIR . '/Design');
define('CONFIGS_DIR', APP_DIR . '/Configs');

require_once ROOT_DIR . '/vendor/autoload.php';
require_once ROOT_DIR . '/routes.php';