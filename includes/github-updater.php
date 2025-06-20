<?php
defined('ABSPATH') || exit;

// Use YahnisElsts' Plugin Update Checker library (bundled locally)
require_once plugin_dir_path(__FILE__) . 'plugin-update-checker/plugin-update-checker.php';

$updateChecker = Puc_v4_Factory::buildUpdateChecker(
    'https://github.com/fmwebdev/events-list/',
    plugin_dir_path(__DIR__) . 'events-list.php',
    'events-list'
);

// Optional: If your GitHub repo doesn't use tags/releases, set branch
$updateChecker->setBranch('main');
