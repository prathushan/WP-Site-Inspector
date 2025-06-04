<?php
/**
 * Plugin Name: Site Inspector
 * Description: Inspect active themes, post types, shortcodes, APIs, CDNs, templates, and more—visually.
 * Version: 1.0
 * Author: Prathusha, Prem Kumar, Vinay
 */


if (!defined('ABSPATH')) exit;

// Load core classes
require_once plugin_dir_path(__FILE__) . 'admin/class-admin-ui.php';
require_once plugin_dir_path(__FILE__) . 'admin/class-analyzer.php';
require_once plugin_dir_path(__FILE__) . 'includes/logger.php';
require_once plugin_dir_path(__FILE__) . 'includes/ajax-handlers.php';
require_once plugin_dir_path(__FILE__) . 'admin/class-settings.php';




// Instantiate Admin UI
new WP_Site_Inspector_Admin_UI();
new WP_Site_Inspector_Settings();