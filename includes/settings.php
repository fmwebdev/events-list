<?php
defined('ABSPATH') || exit;

add_action('admin_menu', function () {
    add_submenu_page('edit.php?post_type=event_item', 'Events Settings', 'Settings', 'manage_options', 'events-list-settings', 'el_render_settings_page');
});

add_action('admin_init', function () {
    register_setting('el_settings_group', 'el_full_list_count', ['type' => 'integer', 'sanitize_callback' => 'absint']);
    register_setting('el_settings_group', 'el_mini_list_count', ['type' => 'integer', 'sanitize_callback' => 'absint']);
});

// Handle update check trigger
add_action('admin_post_el_check_updates_now', function () {
    if (current_user_can('manage_options')) {
        do_action('puc_check_now-events-list');
        wp_redirect(add_query_arg('el_update_checked', '1', admin_url('edit.php?post_type=event_item&page=events-list-settings')));
        exit;
    }
});

function el_render_settings_page() {
    ?>
    <div class="wrap">
        <h1>Events List Settings</h1>
        <?php if (isset($_GET['el_update_checked'])): ?>
            <div class="notice notice-success"><p>Update check triggered. If a new version is available, it will appear shortly.</p></div>
        <?php endif; ?>
        <form method="post" action="options.php">
            <?php settings_fields('el_settings_group'); ?>
            <table class="form-table">
                <tr><th>Full Page List - Events Per Page</th>
                <td><input type="number" name="el_full_list_count" value="<?php echo esc_attr(get_option('el_full_list_count', 20)); ?>" min="1" /></td></tr>
                <tr><th>Mini List Default Count</th>
                <td><input type="number" name="el_mini_list_count" value="<?php echo esc_attr(get_option('el_mini_list_count', 5)); ?>" min="1" /></td></tr>
            </table>
            <?php submit_button(); ?>
        </form>
        <form method="post" action="<?php echo admin_url('admin-post.php'); ?>" style="margin-top:20px;">
            <input type="hidden" name="action" value="el_check_updates_now" />
            <?php submit_button('Check for Updates Now', 'secondary'); ?>
        </form>
        <hr>
        <h2>Shortcode Instructions</h2>
        <p>Use the following shortcodes to display events:</p>
        <ul>
            <li><code>[events_list_full]</code> – Full page responsive view</li>
            <li><code>[events_list_mini count="5"]</code> – Compact stacked view</li>
            <li><code>[events_list_full category="workshops"]</code> – Filter by category</li>
        </ul>
    </div>
    <?php
}
