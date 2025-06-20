<?php
defined('ABSPATH') || exit;

add_action('init', 'el_register_event_post_type');
function el_register_event_post_type() {
    register_post_type('event_item', array(
        'labels' => array(
            'name' => 'Events',
            'singular_name' => 'Event',
            'add_new' => 'Add New',
            'add_new_item' => 'Add New Event',
            'edit_item' => 'Edit Event',
            'new_item' => 'New Event',
            'view_item' => 'View Event',
            'search_items' => 'Search Events',
            'not_found' => 'No events found',
            'menu_name' => 'Events List'
        ),
        'public' => true,
        'show_ui' => true,
        'menu_icon' => 'dashicons-calendar-alt',
        'supports' => array('title', 'editor', 'thumbnail'),
        'has_archive' => false,
        'rewrite' => array('slug' => 'event-item'),
    ));
}

add_action('init', 'el_register_event_taxonomy');
function el_register_event_taxonomy() {
    register_taxonomy('event_category', 'event_item', array(
        'labels' => array(
            'name' => 'Event Categories',
            'singular_name' => 'Event Category',
            'add_new_item' => 'Add New Category'
        ),
        'hierarchical' => true,
        'show_ui' => true,
        'show_admin_column' => true,
        'rewrite' => array('slug' => 'event-category'),
    ));
}
