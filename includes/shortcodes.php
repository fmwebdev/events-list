<?php
defined('ABSPATH') || exit;

// [events_list_full category="category-slug"]
add_shortcode('events_list_full', function($atts) {
    $atts = shortcode_atts(array(
        'category' => '',
    ), $atts);

    ob_start();

    $paged = max(1, get_query_var('paged', 1));
    $args = array(
        'post_type'      => 'event_item',
        'posts_per_page' => get_option('el_full_list_count', 20),
        'paged'          => $paged,
        'meta_key'       => '_el_event_date_time',
        'orderby'        => 'meta_value',
        'order'          => 'ASC',
        'meta_query'     => array(array(
            'key'     => '_el_event_expired',
            'value'   => '0',
            'compare' => '='
        ))
    );

    if (!empty($atts['category'])) {
        $args['tax_query'] = array(array(
            'taxonomy' => 'event_category',
            'field'    => 'slug',
            'terms'    => sanitize_title($atts['category']),
        ));
    }

    $query = new WP_Query($args);

    echo '<div class="events-grid">';
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();

            $url = get_post_meta(get_the_ID(), '_el_event_url', true);
            $target = get_post_meta(get_the_ID(), '_el_event_target_blank', true) === '1' ? ' target="_blank"' : '';
            $datetime = get_post_meta(get_the_ID(), '_el_event_date_time', true);
            $end = get_post_meta(get_the_ID(), '_el_event_date_time_end', true);
            $venue = get_post_meta(get_the_ID(), '_el_event_venue', true);
            $timestamp = strtotime($datetime);
            $start_time = date_i18n('g:i a', $timestamp);
            $end_time = $end ? date_i18n('g:i a', strtotime($end)) : '';
            $month = date_i18n('M', $timestamp);
            $day = date_i18n('d', $timestamp);

            echo '<div class="event-card">';
            if (has_post_thumbnail()) {
                echo '<a href="' . esc_url($url) . '"' . $target . '>';
                the_post_thumbnail('large');
                echo '</a>';
            }

            echo '<div class="event-block-top">';
            echo '<div class="event-mini-month-day">';
            echo '<div class="month">' . esc_html($month) . '</div>';
            echo '<div class="day">' . esc_html($day) . '</div>';
            echo '</div>';

            echo '<div class="event-mini-details">';
            echo '<div class="time">' . esc_html($start_time);
            if ($end_time) echo ' – ' . esc_html($end_time);
            echo '</div>';
            echo '<div class="title"><a href="' . esc_url($url) . '"' . $target . '>' . get_the_title() . '</a></div>';
            echo '<div class="venue">' . esc_html($venue) . '</div>';
            echo '</div>';
            echo '</div>';
            echo '</div>';
        }
        echo '</div>';

        echo paginate_links(array(
            'total'   => $query->max_num_pages,
            'current' => $paged
        ));
    } else {
        echo '<p>No upcoming events found.</p>';
    }

    wp_reset_postdata();
    return ob_get_clean();
});

// [events_list_mini count="5" category="slug"]
add_shortcode('events_list_mini', function($atts) {
    $atts = shortcode_atts(array(
        'count' => get_option('el_mini_list_count', 5),
        'category' => '',
    ), $atts);

    $args = array(
        'post_type'      => 'event_item',
        'posts_per_page' => intval($atts['count']),
        'meta_key'       => '_el_event_date_time',
        'orderby'        => 'meta_value',
        'order'          => 'ASC',
        'meta_query'     => array(array(
            'key'     => '_el_event_expired',
            'value'   => '0',
            'compare' => '='
        ))
    );

    if (!empty($atts['category'])) {
        $args['tax_query'] = array(array(
            'taxonomy' => 'event_category',
            'field'    => 'slug',
            'terms'    => sanitize_title($atts['category']),
        ));
    }

    $query = new WP_Query($args);

    ob_start();
    echo '<ul class="event-mini-list">';

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();

            $url = get_post_meta(get_the_ID(), '_el_event_url', true);
            $target = get_post_meta(get_the_ID(), '_el_event_target_blank', true) === '1' ? ' target="_blank"' : '';
            $datetime = get_post_meta(get_the_ID(), '_el_event_date_time', true);
            $end = get_post_meta(get_the_ID(), '_el_event_date_time_end', true);
            $venue = get_post_meta(get_the_ID(), '_el_event_venue', true);
            $timestamp = strtotime($datetime);
            $start_time = date_i18n('g:i a', $timestamp);
            $end_time = $end ? date_i18n('g:i a', strtotime($end)) : '';
            $month = date_i18n('M', $timestamp);
            $day = date_i18n('d', $timestamp);

            echo '<li class="event-mini-item">';
            if (has_post_thumbnail()) {
                echo '<a href="' . esc_url($url) . '"' . $target . '>';
                the_post_thumbnail('thumbnail');
                echo '</a>';
            }

            echo '<div class="event-mini-month-day">';
            echo '<div class="month">' . esc_html($month) . '</div>';
            echo '<div class="day">' . esc_html($day) . '</div>';
            echo '</div>';

            echo '<div class="event-mini-details">';
            echo '<div class="time">' . esc_html($start_time);
            if ($end_time) echo ' – ' . esc_html($end_time);
            echo '</div>';
            echo '<div class="title"><a href="' . esc_url($url) . '"' . $target . '>' . get_the_title() . '</a></div>';
            echo '<div class="venue">' . esc_html($venue) . '</div>';
            echo '</div>';
            echo '</li>';
        }
    } else {
        echo '<li>No upcoming events found.</li>';
    }

    echo '</ul>';
    wp_reset_postdata();
    return ob_get_clean();
});
