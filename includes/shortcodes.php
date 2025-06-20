<?php
defined('ABSPATH') || exit;

function el_render_event_card($post_id) {
    $start = get_post_meta($post_id, '_el_event_date_time', true);
    $end = get_post_meta($post_id, '_el_event_date_time_end', true);
    $venue = get_post_meta($post_id, '_el_event_venue', true);
    $url = get_post_meta($post_id, '_el_event_url', true);
    $target = get_post_meta($post_id, '_el_event_target_blank', true) === '1' ? '_blank' : '_self';
    $image = get_the_post_thumbnail_url($post_id, 'full');
    $title = get_the_title($post_id);
    $date = date('M d, Y', strtotime($start));
    $time = date('g:i A', strtotime($start));
    $link = $url ?: '#';
    return "<div class='event-card'>
        <a href='{$link}' target='{$target}'><img src='{$image}' alt='{$title}'></a>
        <div class='event-block-top'>
            <div class='event-mini-month-day'><div class='month'>" . date('M', strtotime($start)) . "</div><div class='day'>" . date('d', strtotime($start)) . "</div></div>
            <div class='event-mini-details'>
                <div class='time'>{$time}</div>
                <div class='title'><a href='{$link}' target='{$target}'>{$title}</a></div>
                <div class='venue'>{$venue}</div>
            </div>
        </div>
    </div>";
}

function el_shortcode_events_list_full($atts) {
    $atts = shortcode_atts(['category' => ''], $atts);
    $count = get_option('el_full_list_count', 20);
    $paged = max(1, get_query_var('paged') ?: get_query_var('page') ?: 1);

    $args = [
        'post_type' => 'event_item',
        'meta_query' => [[
            'key' => '_el_event_expired',
            'value' => '1',
            'compare' => '!='
        ]],
        'orderby' => 'meta_value',
        'meta_key' => '_el_event_date_time',
        'order' => 'ASC',
        'posts_per_page' => $count,
        'paged' => $paged,
    ];

    if (!empty($atts['category'])) {
        $args['tax_query'] = [[
            'taxonomy' => 'event_category',
            'field' => 'slug',
            'terms' => sanitize_text_field($atts['category'])
        ]];
    }

    $q = new WP_Query($args);
    if (!$q->have_posts()) return '<p>No upcoming events found.</p>';

    $output = '<div class="events-grid">';
    while ($q->have_posts()) {
        $q->the_post();
        $output .= el_render_event_card(get_the_ID());
    }
    $output .= '</div>';

    $output .= paginate_links(['total' => $q->max_num_pages]);
    wp_reset_postdata();
    return $output;
}
add_shortcode('events_list_full', 'el_shortcode_events_list_full');

function el_shortcode_events_list_mini($atts) {
    $atts = shortcode_atts(['count' => get_option('el_mini_list_count', 5), 'category' => ''], $atts);

    $args = [
        'post_type' => 'event_item',
        'meta_query' => [[
            'key' => '_el_event_expired',
            'value' => '1',
            'compare' => '!='
        ]],
        'orderby' => 'meta_value',
        'meta_key' => '_el_event_date_time',
        'order' => 'ASC',
        'posts_per_page' => absint($atts['count']),
    ];

    if (!empty($atts['category'])) {
        $args['tax_query'] = [[
            'taxonomy' => 'event_category',
            'field' => 'slug',
            'terms' => sanitize_text_field($atts['category'])
        ]];
    }

    $q = new WP_Query($args);
    if (!$q->have_posts()) return '<p>No upcoming events found.</p>';

    $output = '<ul class="event-mini-list">';
    while ($q->have_posts()) {
        $q->the_post();
        $id = get_the_ID();
        $start = get_post_meta($id, '_el_event_date_time', true);
        $venue = get_post_meta($id, '_el_event_venue', true);
        $url = get_post_meta($id, '_el_event_url', true);
        $target = get_post_meta($id, '_el_event_target_blank', true) === '1' ? '_blank' : '_self';
        $image = get_the_post_thumbnail_url($id, 'thumbnail') ?: '';
        $title = get_the_title($id);
        $time = date('g:i A', strtotime($start));
        $month = date('M', strtotime($start));
        $day = date('d', strtotime($start));
        $output .= "<li class='event-mini-item'>
            <img src='{$image}' alt='{$title}'>
            <div class='event-mini-month-day'><div class='month'>{$month}</div><div class='day'>{$day}</div></div>
            <div class='event-mini-details'>
                <div class='time'>{$time}</div>
                <div class='title'><a href='{$url}' target='{$target}'>{$title}</a></div>
                <div class='venue'>{$venue}</div>
            </div>
        </li>";
    }
    $output .= '</ul>';
    wp_reset_postdata();
    return $output;
}
add_shortcode('events_list_mini', 'el_shortcode_events_list_mini');
