<?php
defined('ABSPATH') || exit;

require_once plugin_dir_path(__FILE__) . 'plugin-update-checker/plugin-update-checker.php';

$myUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
    'https://github.com/fmwebdev/events-list/',
    plugin_dir_path(__DIR__) . '../events-list.php',
    'events-list'
);

$myUpdateChecker->setBranch('main');
