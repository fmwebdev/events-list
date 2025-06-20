<?php
defined('ABSPATH') || exit;

add_action('add_meta_boxes', function () {
    add_meta_box('el_event_details', 'Event Details', 'el_event_details_box', 'event_item', 'normal', 'default');
});

function el_event_details_box($post) {
    $date_time = get_post_meta($post->ID, '_el_event_date_time', true);
    $date_time_end = get_post_meta($post->ID, '_el_event_date_time_end', true);
    $venue = get_post_meta($post->ID, '_el_event_venue', true);
    $url = get_post_meta($post->ID, '_el_event_url', true);
    $target_blank = get_post_meta($post->ID, '_el_event_target_blank', true);
    wp_nonce_field('el_event_details_nonce', 'el_event_nonce');
    ?>
    <p><label>Start Date & Time:</label><br><input type="datetime-local" name="el_event_date_time" value="<?php echo esc_attr($date_time); ?>" /></p>
    <p><label>End Date & Time:</label><br><input type="datetime-local" name="el_event_date_time_end" value="<?php echo esc_attr($date_time_end); ?>" /></p>
    <p><label>Venue Location:</label><br><input type="text" name="el_event_venue" value="<?php echo esc_attr($venue); ?>" class="widefat" /></p>
    <p><label>Event Link (optional):</label><br><input type="url" name="el_event_url" value="<?php echo esc_url($url); ?>" class="widefat" /></p>
    <p><label><input type="checkbox" name="el_event_target_blank" value="1" <?php checked($target_blank, '1'); ?> /> Open in new window</label></p>
    <?php
}

add_action('save_post_event_item', function ($post_id) {
    if (!isset($_POST['el_event_nonce']) || !wp_verify_nonce($_POST['el_event_nonce'], 'el_event_details_nonce')) return;
    update_post_meta($post_id, '_el_event_date_time', sanitize_text_field($_POST['el_event_date_time']));
    update_post_meta($post_id, '_el_event_date_time_end', sanitize_text_field($_POST['el_event_date_time_end']));
    update_post_meta($post_id, '_el_event_venue', sanitize_text_field($_POST['el_event_venue']));
    update_post_meta($post_id, '_el_event_url', esc_url_raw($_POST['el_event_url']));
    update_post_meta($post_id, '_el_event_target_blank', isset($_POST['el_event_target_blank']) ? '1' : '0');
});
