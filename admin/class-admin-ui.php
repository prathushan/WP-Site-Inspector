<?php
if (!defined('ABSPATH')) exit;

class WP_Site_Inspector_Admin_UI {

    public function __construct() {
        add_action('admin_menu', [$this, 'register_menu']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_assets']);
        add_action('admin_init', [$this, 'register_settings']);
    }

    /**
     * Register admin menu
     */
    public function register_menu() {
        add_menu_page(
            'WP Site Inspector',
            'Site Inspector',
            'manage_options',
            'wp-site-inspector',
            [$this, 'render_dashboard'],
            'dashicons-chart-area',
            81
        );
         add_submenu_page(
        'wp-site-inspector',             
        'Code AI Assistant',            
        'Code AI <span style="background: red; color: white; padding: 2px 4px; font-size: 10px; border-radius: 3px; margin-left: 5px;">New</span>',                       
        'manage_options',                
        'wpsi-code-ai',                  
        [$this, 'render_code_ai_page']   
       );
    }

    /**
     * Enqueue assets only for the plugin page
     */
    public function enqueue_assets($hook) {
        if ($hook !== 'toplevel_page_wp-site-inspector') return;

        wp_enqueue_style('wpsi-style', plugin_dir_url(__FILE__) . 'assets/style.css');
        wp_enqueue_script('wpsi-chart', plugin_dir_url(__FILE__) . 'assets/libs/Chart.min.js', [], null, true);
        wp_enqueue_script('wpsi-main', plugin_dir_url(__FILE__) . 'assets/script.js', ['jquery'], null, true);
    }

    /**
     * Register toggle setting for enabling WP debug log
     */
    public function register_settings() {
        register_setting('wpsi_settings_group', 'wpsi_enable_debug_log');
    }

    /**
     * Load dashboard view
     */
    public function render_dashboard() {
        include_once plugin_dir_path(__FILE__) . 'views/dashboard.php';
    }
    
    /**
     * Load Code-Ai
     */
    public function render_code_ai_page() {
    include_once plugin_dir_path(__FILE__) . 'views/code-ai.php';
   }

}
