<?php
defined('ABSPATH') || exit;
add_action('add_meta_boxes', function () {
    add_meta_box('el_event_details', 'Event Details', 'el_event_details_box', 'event_item', 'normal', 'default');
});
function el_event_details_box($post) {
    $start = get_post_meta($post->ID, '_el_event_date_time', true);
    $end = get_post_meta($post->ID, '_el_event_date_time_end', true);
    $venue = get_post_meta($post->ID, '_el_event_venue', true);
    $url = get_post_meta($post->ID, '_el_event_url', true);
    $blank = get_post_meta($post->ID, '_el_event_target_blank', true);
    wp_nonce_field('el_event_details_nonce', 'el_event_nonce');
    echo '<p><label>Start:</label><br><input type="datetime-local" name="el_event_date_time" value="' . esc_attr($start) . '" /></p>';
    echo '<p><label>End:</label><br><input type="datetime-local" name="el_event_date_time_end" value="' . esc_attr($end) . '" /></p>';
    echo '<p><label>Venue:</label><br><input type="text" name="el_event_venue" value="' . esc_attr($venue) . '" class="widefat" /></p>';
    echo '<p><label>Link:</label><br><input type="url" name="el_event_url" value="' . esc_url($url) . '" class="widefat" /></p>';
    echo '<p><label><input type="checkbox" name="el_event_target_blank" value="1" ' . checked($blank, '1', false) . '/> Open in new window</label></p>';
}
add_action('save_post_event_item', function ($post_id) {
    if (!isset($_POST['el_event_nonce']) || !wp_verify_nonce($_POST['el_event_nonce'], 'el_event_details_nonce')) return;
    update_post_meta($post_id, '_el_event_date_time', sanitize_text_field($_POST['el_event_date_time']));
    update_post_meta($post_id, '_el_event_date_time_end', sanitize_text_field($_POST['el_event_date_time_end']));
    update_post_meta($post_id, '_el_event_venue', sanitize_text_field($_POST['el_event_venue']));
    update_post_meta($post_id, '_el_event_url', esc_url_raw($_POST['el_event_url']));
    update_post_meta($post_id, '_el_event_target_blank', isset($_POST['el_event_target_blank']) ? '1' : '0');
});
