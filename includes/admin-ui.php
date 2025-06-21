<?php
defined('ABSPATH') || exit;

add_filter('manage_event_item_posts_columns', function($columns) {
    $columns['event_start'] = 'Start Date';
    $columns['event_end'] = 'End Date';
    $columns['event_status'] = 'Status';
    return $columns;
});

add_action('manage_event_item_posts_custom_column', function($column, $post_id) {
    if ($column === 'event_start') {
        echo esc_html(get_post_meta($post_id, '_el_event_date_time', true));
    }
    if ($column === 'event_end') {
        echo esc_html(get_post_meta($post_id, '_el_event_date_time_end', true));
    }
    if ($column === 'event_status') {
        $expired = get_post_meta($post_id, '_el_event_expired', true);
        echo $expired === '1' ? '<span style="color:red;"><strong>Expired</strong></span>' : '<span style="color:green;">Active</span>';
    }
}, 10, 2);
