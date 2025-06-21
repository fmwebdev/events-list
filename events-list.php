<?php
/**
 * Plugin Name: Events List
 * Description: Manage and display time-based events with expiration, admin UI, and shortcodes.
 * Version: 1.1.1
 * Author: FedMed Dev
 * Plugin URI: https://github.com/fmwebdev/events-list
 */

defined('ABSPATH') || exit;

// Include existing plugin files
require_once plugin_dir_path(__FILE__) . 'includes/cpt.php';
require_once plugin_dir_path(__FILE__) . 'includes/meta-boxes.php';
require_once plugin_dir_path(__FILE__) . 'includes/shortcodes.php';
require_once plugin_dir_path(__FILE__) . 'includes/settings.php';
require_once plugin_dir_path(__FILE__) . 'includes/cron.php';
require_once plugin_dir_path(__FILE__) . 'includes/admin-ui.php';

// GitHub Auto-Updater Class
if (!class_exists('WP_GitHub_Updater')) {
    class WP_GitHub_Updater {
        
        private $plugin_slug;
        private $plugin_basename;
        private $plugin_path;
        private $plugin_url;
        private $github_username;
        private $github_repo;
        private $github_token;
        private $version;
        
        public function __construct($plugin_file, $github_username, $github_repo, $github_token = '') {
            $this->plugin_path = $plugin_file;
            $this->plugin_basename = plugin_basename($plugin_file);
            $this->plugin_slug = dirname($this->plugin_basename);
            $this->plugin_url = plugin_dir_url($plugin_file);
            $this->github_username = $github_username;
            $this->github_repo = $github_repo;
            $this->github_token = $github_token;
            
            // Get current plugin version
            if (!function_exists('get_plugin_data')) {
                require_once(ABSPATH . 'wp-admin/includes/plugin.php');
            }
            $plugin_data = get_plugin_data($plugin_file);
            $this->version = $plugin_data['Version'];
            
            add_filter('pre_set_site_transient_update_plugins', array($this, 'check_for_update'));
            add_filter('plugins_api', array($this, 'plugin_popup'), 10, 3);
            add_filter('upgrader_post_install', array($this, 'after_install'), 10, 3);
        }
        
        /**
         * Check if there's an update available
         */
        public function check_for_update($transient) {
            if (empty($transient->checked)) {
                return $transient;
            }
            
            // Get remote version
            $remote_version = $this->get_remote_version();
            
            if (version_compare($this->version, $remote_version, '<')) {
                $transient->response[$this->plugin_basename] = (object) array(
                    'slug' => $this->plugin_slug,
                    'plugin' => $this->plugin_basename,
                    'new_version' => $remote_version,
                    'url' => $this->get_github_repo_url(),
                    'package' => $this->get_download_url()
                );
            }
            
            return $transient;
        }
        
        /**
         * Get the latest release version from GitHub
         */
        private function get_remote_version() {
            $api_url = "https://api.github.com/repos/{$this->github_username}/{$this->github_repo}/releases/latest";
            
            $args = array(
                'timeout' => 30,
                'headers' => array(
                    'User-Agent' => 'WordPress Plugin Updater'
                )
            );
            
            // Add authorization header if token is provided
            if (!empty($this->github_token)) {
                $args['headers']['Authorization'] = 'token ' . $this->github_token;
            }
            
            $response = wp_remote_get($api_url, $args);
            
            if (is_wp_error($response) || wp_remote_retrieve_response_code($response) !== 200) {
                return false;
            }
            
            $body = wp_remote_retrieve_body($response);
            $data = json_decode($body, true);
            
            if (isset($data['tag_name'])) {
                // Remove 'v' prefix if present (e.g., v1.2.3 -> 1.2.3)
                return ltrim($data['tag_name'], 'v');
            }
            
            return false;
        }
        
        /**
         * Get the download URL for the latest release
         */
        private function get_download_url() {
            $api_url = "https://api.github.com/repos/{$this->github_username}/{$this->github_repo}/releases/latest";
            
            $args = array(
                'timeout' => 30,
                'headers' => array(
                    'User-Agent' => 'WordPress Plugin Updater'
                )
            );
            
            if (!empty($this->github_token)) {
                $args['headers']['Authorization'] = 'token ' . $this->github_token;
            }
            
            $response = wp_remote_get($api_url, $args);
            
            if (is_wp_error($response) || wp_remote_retrieve_response_code($response) !== 200) {
                return false;
            }
            
            $body = wp_remote_retrieve_body($response);
            $data = json_decode($body, true);
            
            // Return the zipball URL
            if (isset($data['zipball_url'])) {
                return $data['zipball_url'];
            }
            
            return false;
        }
        
        /**
         * Show plugin information popup
         */
        public function plugin_popup($result, $action, $args) {
            if ($action !== 'plugin_information') {
                return false;
            }
            
            if (!isset($args->slug) || $args->slug !== $this->plugin_slug) {
                return false;
            }
            
            $api_url = "https://api.github.com/repos/{$this->github_username}/{$this->github_repo}/releases/latest";
            
            $request_args = array(
                'timeout' => 30,
                'headers' => array(
                    'User-Agent' => 'WordPress Plugin Updater'
                )
            );
            
            if (!empty($this->github_token)) {
                $request_args['headers']['Authorization'] = 'token ' . $this->github_token;
            }
            
            $response = wp_remote_get($api_url, $request_args);
            
            if (is_wp_error($response) || wp_remote_retrieve_response_code($response) !== 200) {
                return false;
            }
            
            $body = wp_remote_retrieve_body($response);
            $data = json_decode($body, true);
            
            if (!$data) {
                return false;
            }
            
            return (object) array(
                'name' => 'Events List',
                'slug' => $this->plugin_slug,
                'version' => ltrim($data['tag_name'], 'v'),
                'author' => 'FedMed Dev',
                'homepage' => $data['html_url'],
                'requires' => '4.0',
                'tested' => get_bloginfo('version'),
                'downloaded' => 0,
                'last_updated' => $data['published_at'],
                'sections' => array(
                    'description' => $data['body'] ?: 'Manage and display time-based events with expiration, admin UI, and shortcodes.',
                    'changelog' => $data['body'] ?: 'See GitHub repository for changelog.'
                ),
                'download_link' => $data['zipball_url']
            );
        }
        
        /**
         * Handle post-install cleanup
         */
        public function after_install($response, $hook_extra, $result) {
            global $wp_filesystem;
            
            $install_directory = plugin_dir_path($this->plugin_path);
            $wp_filesystem->move($result['destination'], $install_directory);
            $result['destination'] = $install_directory;
            
            if ($this->is_plugin_active()) {
                activate_plugin($this->plugin_basename);
            }
            
            return $result;
        }
        
        /**
         * Check if plugin is active
         */
        private function is_plugin_active() {
            return is_plugin_active($this->plugin_basename);
        }
        
        /**
         * Get GitHub repository URL
         */
        private function get_github_repo_url() {
            return "https://github.com/{$this->github_username}/{$this->github_repo}";
        }
    }
}

// Initialize GitHub updater (admin only for performance)
if (is_admin()) {
    new WP_GitHub_Updater(
        __FILE__,              // This plugin file
        'fmwebdev',            // Your GitHub username
        'events-list',         // Your repository name
        ''                     // GitHub token (empty for public repo)
    );
}

// Your existing plugin functionality
add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style('events-list-style', plugin_dir_url(__FILE__) . 'assets/frontend.css');
});

register_activation_hook(__FILE__, function () {
    if (!wp_next_scheduled('el_check_expired_events')) {
        wp_schedule_event(time(), 'daily', 'el_check_expired_events');
    }
});

register_deactivation_hook(__FILE__, function () {
    wp_clear_scheduled_hook('el_check_expired_events');
});
