<?php
/**
 * Plugin Name: Events List
 * Description: Manage and display time-based events with expiration logic and shortcode rendering.
 * Version: 1.1.3
 * Author: FM Dev
 * Text Domain: events-list
 */

defined('ABSPATH') || exit;

require_once plugin_dir_path(__FILE__) . 'includes/cpt.php';
require_once plugin_dir_path(__FILE__) . 'includes/meta-boxes.php';
require_once plugin_dir_path(__FILE__) . 'includes/shortcodes.php';
require_once plugin_dir_path(__FILE__) . 'includes/settings.php';
require_once plugin_dir_path(__FILE__) . 'includes/cron.php';
require_once plugin_dir_path(__FILE__) . 'includes/admin-ui.php';
require_once plugin_dir_path(__FILE__) . 'includes/github-updater.php';

add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style('events-list-style', plugin_dir_url(__FILE__) . 'assets/frontend.css');
});

register_activation_hook(__FILE__, function () {
    if (!wp_next_scheduled('el_check_expired_events')) {
        wp_schedule_event(time(), 'daily', 'el_check_expired_events');
    }
});

register_deactivation_hook(__FILE__, function () {
    wp_clear_scheduled_hook('el_check_expired_events');
});
