<?php
defined('WP_UNINSTALL_PLUGIN') || exit;

// Delete all event_item posts
$event_posts = get_posts(array(
    'post_type' => 'event_item',
    'numberposts' => -1,
    'post_status' => 'any',
    'fields' => 'ids'
));
foreach ($event_posts as $post_id) {
    wp_delete_post($post_id, true);
}

// Delete all event_category terms
$terms = get_terms(array(
    'taxonomy' => 'event_category',
    'hide_empty' => false,
    'fields' => 'ids'
));
foreach ($terms as $term_id) {
    wp_delete_term($term_id, 'event_category');
}

// Delete plugin options
delete_option('el_mini_list_count');
delete_option('el_full_list_count');
