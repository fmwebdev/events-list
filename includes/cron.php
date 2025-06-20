<?php
defined('ABSPATH') || exit;

add_action('el_check_expired_events', 'el_mark_expired_events');
add_action('save_post_event_item', 'el_mark_single_event_expiry');
add_action('wp_insert_post', function($post_id, $post, $update) {
    if ($post->post_type === 'event_item') {
        el_check_and_update_expiry($post_id);
    }
}, 10, 3);

function el_mark_expired_events() {
    $query = new WP_Query(array('post_type' => 'event_item', 'posts_per_page' => -1, 'post_status' => 'publish'));
    foreach ($query->posts as $post) {
        el_check_and_update_expiry($post->ID);
    }
}

function el_mark_single_event_expiry($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    el_check_and_update_expiry($post_id);
}

function el_check_and_update_expiry($post_id) {
    $start = get_post_meta($post_id, '_el_event_date_time', true);
    $end = get_post_meta($post_id, '_el_event_date_time_end', true);
    $expire_timestamp = $end ? strtotime($end) : strtotime($start);
    $now = current_time('timestamp');
    update_post_meta($post_id, '_el_event_expired', $expire_timestamp < $now ? '1' : '0');
}
