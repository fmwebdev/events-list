<?php
defined('ABSPATH') || exit;

add_action('add_meta_boxes', 'el_add_event_meta_boxes');
function el_add_event_meta_boxes() {
    add_meta_box('el_event_details', 'Event Details', 'el_render_event_meta_box', 'event_item', 'normal', 'default');
}

function el_render_event_meta_box($post) {
    wp_nonce_field('el_save_event_meta', 'el_event_meta_nonce');

    $start_datetime = get_post_meta($post->ID, '_el_event_date_time', true);
    $end_datetime = get_post_meta($post->ID, '_el_event_date_time_end', true);
    $venue = get_post_meta($post->ID, '_el_event_venue', true);
    $url = get_post_meta($post->ID, '_el_event_url', true);
    $target_blank = get_post_meta($post->ID, '_el_event_target_blank', true);

    ?>
    <p>
        <label for="el_event_date_time"><strong>Start Date & Time:</strong></label><br />
        <input type="datetime-local" id="el_event_date_time" name="el_event_date_time" value="<?php echo esc_attr($start_datetime); ?>" class="widefat" />
    </p>
    <p>
        <label for="el_event_date_time_end"><strong>End Date & Time:</strong></label><br />
        <input type="datetime-local" id="el_event_date_time_end" name="el_event_date_time_end" value="<?php echo esc_attr($end_datetime); ?>" class="widefat" />
    </p>
    <p>
        <label for="el_event_venue"><strong>Venue Location:</strong></label><br />
        <input type="text" id="el_event_venue" name="el_event_venue" value="<?php echo esc_attr($venue); ?>" class="widefat" />
    </p>
    <p>
        <label for="el_event_url"><strong>Event Link (optional):</strong></label><br />
        <input type="url" id="el_event_url" name="el_event_url" value="<?php echo esc_url($url); ?>" class="widefat" />
    </p>
    <p>
        <label><input type="checkbox" name="el_event_target_blank" value="1" <?php checked($target_blank, '1'); ?> /> Open in new window</label>
    </p>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const startInput = document.getElementById('el_event_date_time');
            const endInput = document.getElementById('el_event_date_time_end');
            startInput.addEventListener('change', function () {
                if (!endInput.value) {
                    endInput.value = startInput.value;
                }
            });
        });
    </script>
    <?php
}

add_action('save_post', 'el_save_event_meta');
function el_save_event_meta($post_id) {
    if (!isset($_POST['el_event_meta_nonce']) || !wp_verify_nonce($_POST['el_event_meta_nonce'], 'el_save_event_meta')) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;

    $fields = [
        '_el_event_date_time' => sanitize_text_field($_POST['el_event_date_time'] ?? ''),
        '_el_event_date_time_end' => sanitize_text_field($_POST['el_event_date_time_end'] ?? ''),
        '_el_event_venue' => sanitize_text_field($_POST['el_event_venue'] ?? ''),
        '_el_event_url' => esc_url_raw($_POST['el_event_url'] ?? ''),
        '_el_event_target_blank' => isset($_POST['el_event_target_blank']) ? '1' : '0',
    ];

    foreach ($fields as $key => $value) {
        update_post_meta($post_id, $key, $value);
    }
}
