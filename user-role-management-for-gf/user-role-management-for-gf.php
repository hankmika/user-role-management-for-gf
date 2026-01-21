<?php
/**
 * Plugin Name: User role management for Gravity Forms
 * Description: Let administrators assign granular Gravity Forms capabilities to existing roles that can access wp-admin.
 * Version: 1.0.0
 * Author: Hank Mika
 * Text Domain: gfrm
 * Domain Path: /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'GFRM_PLUGIN_FILE', __FILE__ );
define( 'GFRM_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'GFRM_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
define( 'GFRM_MANAGE_CAP', 'gfrm_manage_caps' );

add_action(
    'plugins_loaded',
    function() {
        load_plugin_textdomain(
            'gfrm',
            false,
            dirname( GFRM_PLUGIN_BASENAME ) . '/languages'
        );
    }
);

// Core helpers + admin wiring.
require_once GFRM_PLUGIN_DIR . 'includes/helpers.php';
require_once GFRM_PLUGIN_DIR . 'includes/admin-page.php';

/**
 * Set up defaults on plugin activation.
 */
function gfrm_activate() {
    gfrm_grant_manage_cap_to_admins();
    gfrm_apply_saved_caps();
}
register_activation_hook( GFRM_PLUGIN_FILE, 'gfrm_activate' );
