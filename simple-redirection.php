<?php
/**
 * Plugin Name: Simple Redirection
 * Plugin URI: https://github.com/luozongbao/simple-redirection/
 * Description: A simple plugin to create custom slugs with redirection functionality, supporting WordPress and WordPress Multisite.
 * Version: 1.0.0
 * Author: Atipat Lorwongam and Claude Sonnet 4
 * Author URI: https://github.com/luozongbao/simple-redirection/
 * License: GPL v2 or later
 * Network: true
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('WSR_VERSION', '1.0.0');
define('WSR_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('WSR_PLUGIN_URL', plugin_dir_url(__FILE__));

class WordPressSimpleRedirect {
    
    private $table_name;
    
    public function __construct() {
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'simple_redirects';
        
        // Hook into WordPress
        add_action('init', array($this, 'init'));
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('wp_ajax_wsr_create_slug', array($this, 'ajax_create_slug'));
        add_action('wp_ajax_wsr_update_slug', array($this, 'ajax_update_slug'));
        add_action('wp_ajax_wsr_delete_slug', array($this, 'ajax_delete_slug'));
        add_action('wp_ajax_wsr_toggle_status', array($this, 'ajax_toggle_status'));
        add_action('wp_ajax_wsr_test_redirect', array($this, 'ajax_test_redirect'));
        
        // Handle redirects - use earlier hook with higher priority
        add_action('parse_request', array($this, 'handle_redirect'), 1);
        
        // Activation and deactivation hooks
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
        register_uninstall_hook(__FILE__, array('WordPressSimpleRedirect', 'uninstall'));
    }
    
    public function init() {
        // Enqueue scripts and styles for admin
        if (is_admin()) {
            add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
        }
        
        // Add rewrite rule to catch all requests
        add_rewrite_rule('^(.+)/?$', 'index.php?wsr_slug=$matches[1]', 'top');
        
        // Add query var
        add_filter('query_vars', array($this, 'add_query_vars'));
    }
    
    public function add_query_vars($vars) {
        $vars[] = 'wsr_slug';
        return $vars;
    }
    
    public function activate() {
        $this->create_table();
        
        // For multisite, create table for each site
        if (is_multisite()) {
            $sites = get_sites();
            foreach ($sites as $site) {
                switch_to_blog($site->blog_id);
                $this->create_table();
                restore_current_blog();
            }
        }
        
        // Flush rewrite rules
        flush_rewrite_rules();
    }
    
    public function deactivate() {
        // Flush rewrite rules
        flush_rewrite_rules();
    }
    
    public static function uninstall() {
        global $wpdb;
        
        if (is_multisite()) {
            $sites = get_sites();
            foreach ($sites as $site) {
                switch_to_blog($site->blog_id);
                $table_name = $wpdb->prefix . 'simple_redirects';
                $wpdb->query("DROP TABLE IF EXISTS $table_name");
                restore_current_blog();
            }
        } else {
            $table_name = $wpdb->prefix . 'simple_redirects';
            $wpdb->query("DROP TABLE IF EXISTS $table_name");
        }
        
        // Flush rewrite rules
        flush_rewrite_rules();
    }
    
    private function create_table() {
        global $wpdb;
        
        $charset_collate = $wpdb->get_charset_collate();
        
        $sql = "CREATE TABLE $this->table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            slug varchar(255) NOT NULL,
            target_url text NOT NULL,
            redirect_count int(11) DEFAULT 0,
            last_called datetime NULL,
            status enum('active','inactive') DEFAULT 'active',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY slug (slug)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
    
    public function add_admin_menu() {
        add_menu_page(
            'Simple Redirection',
            'Simple Redirect',
            'manage_options',
            'simple-redirection',
            array($this, 'admin_page'),
            'dashicons-admin-links',
            30
        );
    }
    
    public function enqueue_admin_scripts($hook) {
        if ($hook !== 'toplevel_page_simple-redirection') {
            return;
        }
        
        wp_enqueue_script('jquery');
        wp_enqueue_script('wsr-admin-js', WSR_PLUGIN_URL . 'assets/admin.js', array('jquery'), WSR_VERSION, true);
        wp_enqueue_style('wsr-admin-css', WSR_PLUGIN_URL . 'assets/admin.css', array(), WSR_VERSION);
        
        wp_localize_script('wsr-admin-js', 'wsr_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('wsr_nonce')
        ));
    }
    
    public function admin_page() {
        global $wpdb;
        
        // Get all slugs
        $slugs = $wpdb->get_results("SELECT * FROM $this->table_name ORDER BY created_at DESC");
        
        include WSR_PLUGIN_DIR . 'templates/admin-page.php';
    }
    
    public function ajax_create_slug() {
        check_ajax_referer('wsr_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }
        
        global $wpdb;
        
        $slug = sanitize_text_field($_POST['slug']);
        $target_url = esc_url_raw($_POST['target_url']);
        
        // Validate inputs
        if (empty($slug) || empty($target_url)) {
            wp_send_json_error('Slug and Target URL are required.');
        }
        
        // Remove leading/trailing slashes and clean slug
        $slug = trim($slug, '/');
        
        // Check if slug already exists
        $existing = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $this->table_name WHERE slug = %s", $slug));
        if ($existing > 0) {
            wp_send_json_error('Slug already exists.');
        }
        
        // Insert new slug
        $result = $wpdb->insert(
            $this->table_name,
            array(
                'slug' => $slug,
                'target_url' => $target_url
            ),
            array('%s', '%s')
        );
        
        if ($result === false) {
            wp_send_json_error('Failed to create slug.');
        }
        
        // Flush rewrite rules to ensure new slug works immediately
        flush_rewrite_rules();
        
        wp_send_json_success('Slug created successfully.');
    }
    
    public function ajax_update_slug() {
        check_ajax_referer('wsr_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }
        
        global $wpdb;
        
        $id = intval($_POST['id']);
        $slug = sanitize_text_field($_POST['slug']);
        $target_url = esc_url_raw($_POST['target_url']);
        
        // Validate inputs
        if (empty($slug) || empty($target_url)) {
            wp_send_json_error('Slug and Target URL are required.');
        }
        
        // Remove leading/trailing slashes and clean slug
        $slug = trim($slug, '/');
        
        // Check if slug already exists (excluding current record)
        $existing = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $this->table_name WHERE slug = %s AND id != %d", $slug, $id));
        if ($existing > 0) {
            wp_send_json_error('Slug already exists.');
        }
        
        // Update slug
        $result = $wpdb->update(
            $this->table_name,
            array(
                'slug' => $slug,
                'target_url' => $target_url
            ),
            array('id' => $id),
            array('%s', '%s'),
            array('%d')
        );
        
        if ($result === false) {
            wp_send_json_error('Failed to update slug.');
        }
        
        wp_send_json_success('Slug updated successfully.');
    }
    
    public function ajax_delete_slug() {
        check_ajax_referer('wsr_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }
        
        global $wpdb;
        
        $id = intval($_POST['id']);
        
        $result = $wpdb->delete($this->table_name, array('id' => $id), array('%d'));
        
        if ($result === false) {
            wp_send_json_error('Failed to delete slug.');
        }
        
        wp_send_json_success('Slug deleted successfully.');
    }
    
    public function ajax_toggle_status() {
        check_ajax_referer('wsr_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }
        
        global $wpdb;
        
        $id = intval($_POST['id']);
        $status = sanitize_text_field($_POST['status']);
        
        $result = $wpdb->update(
            $this->table_name,
            array('status' => $status),
            array('id' => $id),
            array('%s'),
            array('%d')
        );
        
        if ($result === false) {
            wp_send_json_error('Failed to update status.');
        }
        
        wp_send_json_success('Status updated successfully.');
    }
    
    public function ajax_test_redirect() {
        check_ajax_referer('wsr_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }
        
        global $wpdb;
        
        $id = intval($_POST['id']);
        
        $slug_data = $wpdb->get_row($wpdb->prepare("SELECT * FROM $this->table_name WHERE id = %d", $id));
        
        if (!$slug_data) {
            wp_send_json_error('Slug not found.');
        }
        
        wp_send_json_success(array('target_url' => $slug_data->target_url));
    }
    
    public function handle_redirect($wp) {
        global $wpdb;
        
        // Get the requested slug from query var
        $requested_slug = get_query_var('wsr_slug');
        
        // If no slug in query var, try to get from REQUEST_URI
        if (empty($requested_slug)) {
            $request_uri = $_SERVER['REQUEST_URI'];
            
            // Remove query string
            $request_uri = strtok($request_uri, '?');
            
            // Get the site's base path
            $site_url_parts = parse_url(home_url());
            $site_path = isset($site_url_parts['path']) ? $site_url_parts['path'] : '';
            
            // Remove site path from request URI
            if (!empty($site_path) && $site_path !== '/') {
                $request_uri = str_replace($site_path, '', $request_uri);
            }
            
            // Remove leading and trailing slashes
            $requested_slug = trim($request_uri, '/');
        }
        
        // If still empty, return
        if (empty($requested_slug)) {
            return;
        }
        
        // Don't redirect admin, wp-content, wp-includes, etc.
        if (preg_match('/^(wp-admin|wp-content|wp-includes|xmlrpc\.php|wp-login\.php)/', $requested_slug)) {
            return;
        }
        
        // Check if this matches any of our slugs
        $slug_data = $wpdb->get_row($wpdb->prepare("SELECT * FROM $this->table_name WHERE slug = %s AND status = 'active'", $requested_slug));
        
        if ($slug_data) {
            // Update redirect count and last called time
            $wpdb->update(
                $this->table_name,
                array(
                    'redirect_count' => $slug_data->redirect_count + 1,
                    'last_called' => current_time('mysql')
                ),
                array('id' => $slug_data->id),
                array('%d', '%s'),
                array('%d')
            );
            
            // Perform redirect
            wp_redirect($slug_data->target_url, 301);
            exit;
        }
    }
}

// Initialize the plugin
new WordPressSimpleRedirect();