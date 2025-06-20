<?php
defined('ABSPATH') || exit;
require_once plugin_dir_path(__FILE__) . 'plugin-update-checker/plugin-update-checker.php';
$updateChecker = Puc_v4_Factory::buildUpdateChecker(
    'https://github.com/fmwebdev/events-list/',
    __FILE__,
    'events-list'
);
$updateChecker->setBranch('main');
