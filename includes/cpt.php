<?php
defined('ABSPATH') || exit;
add_action('init', 'el_register_event_post_type');
function el_register_event_post_type() {
    register_post_type('event_item', array(
        'labels' => array(
            'name' => 'Events',
            'singular_name' => 'Event',
            'menu_name' => 'Events List'
        ),
        'public' => true,
        'menu_icon' => 'dashicons-calendar-alt',
        'supports' => array('title', 'editor', 'thumbnail'),
        'has_archive' => false,
        'rewrite' => array('slug' => 'event-item'),
    ));
}
add_action('init', 'el_register_event_taxonomy');
function el_register_event_taxonomy() {
    register_taxonomy('event_category', 'event_item', array(
        'labels' => array('name' => 'Event Categories'),
        'hierarchical' => true,
        'show_ui' => true,
        'show_admin_column' => true,
    ));
}
