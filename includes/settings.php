<?php
defined('ABSPATH') || exit;

add_action('admin_menu', function() {
    add_submenu_page('edit.php?post_type=event_item', 'Events Settings', 'Settings', 'manage_options', 'events-list-settings', 'el_render_settings_page');
});

add_action('admin_init', function() {
    register_setting('el_settings_group', 'el_full_list_count', ['type' => 'integer', 'sanitize_callback' => 'absint']);
    register_setting('el_settings_group', 'el_mini_list_count', ['type' => 'integer', 'sanitize_callback' => 'absint']);
});

function el_render_settings_page() {
    ?>
    <div class="wrap">
        <h1>Events List Settings</h1>
        <form method="post" action="options.php">
            <?php settings_fields('el_settings_group'); ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Full Page List - Events Per Page</th>
                    <td><input type="number" name="el_full_list_count" value="<?php echo esc_attr(get_option('el_full_list_count', 20)); ?>" min="1" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Mini List Default Count</th>
                    <td><input type="number" name="el_mini_list_count" value="<?php echo esc_attr(get_option('el_mini_list_count', 5)); ?>" min="1" /></td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>

        <hr>
        <h2>Shortcode Instructions</h2>
        <p>Use the following shortcodes to display your events on the front-end:</p>
        <ul>
            <li><code>[events_list_full]</code> – Displays a responsive grid layout with pagination.</li>
            <li><code>[events_list_mini count="5"]</code> – Displays a compact vertical list of upcoming events.</li>
            <li><strong>Optional:</strong> Add a <code>category</code> parameter to either shortcode to filter events by category. Example:<br>
                <code>[events_list_full category="workshops"]</code><br>
                <code>[events_list_mini count="3" category="webinars"]</code>
            </li>
        </ul>
        <p><strong>Note:</strong> Categories are managed under the “Event Categories” menu within the Events List section.</p>
    </div>
    <?php
}
