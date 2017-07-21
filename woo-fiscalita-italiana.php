<?php

/**
 * WFI plugin
 * 
 * @package   Woo_Fiscalita_Italiana
 * @author    Codeat <support@codeat.co>
 * @copyright 2016 
 * @license   GPL-2.0+
 * @link      http://codeat.co
 */
/*
 * Plugin Name:       Woo Fiscalita Italiana
 * Plugin URI:        https://wordpress.org/plugins/woo-fiscalita-italiana/
 * Description:       Easy Italian Fiscality for WooCommerce: checkout fields, invoices, digital goods and everything you need for a store based in Italy 
 * Version:           1.1.16
 * Author:            Codeat
 * Author URI:        http://codeat.co
 * Text Domain:       woo-fiscalita-italiana
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 * WordPress-Plugin-Boilerplate-Powered: v2.0.0
 * 
 * @fs_premium_only admin/includes/WFI_Order.php, admin/includes/WFI_Seq_Order_Admin.php, admin/includes/WFI_Bulk_Order_Export.php, public/includes/WFI_Shipping_Islands.php, public/includes/WFI_Shipping_SupCOD.php, public/includes/WFI_Custom_Taxes.php, public/includes/WFI_VAT_Validation.php, includes/WFI_Seq_Order.php, includes/WFI_PDF_Invoices.php, includes/WFI_Invoices.php, includes/vendor/, languages/, includes/load_textdomain.php, includes/composer.json, includes/composer.lock
 */
// If this file is called directly, abort.
if ( !defined( 'WPINC' ) ) {
    die;
}
if ( !function_exists( 'woo_is_plugin_active' ) ) {
    /**
     * Check if WooCommerce is active
     * 
     * @return boolean
     */
    function woo_is_plugin_active()
    {
        return in_array( 'woocommerce/woocommerce.php', (array) get_option( 'active_plugins', array() ), true ) || function_exists( 'is_plugin_active_for_network' ) && is_plugin_active_for_network( 'woocommerce/woocommerce.php' ) || array_key_exists( 'woocommerce/woocommerce.php', get_site_option( 'active_sitewide_plugins' ) || array() );
    }

}

if ( woo_is_plugin_active() ) {
    define( 'WFI_VERSION', '1.1.16' );
    define( 'WFI_TEXTDOMAIN', 'woo-fiscalita-italiana' );
    /**
     * Create a helper function for easy SDK access.
     * 
     * @global object $wfi_fs
     * @return object
     */
    function wfi_fs()
    {
        global  $wfi_fs ;
        
        if ( !isset( $wfi_fs ) ) {
            // Include Freemius SDK.
            require_once dirname( __FILE__ ) . '/includes/freemius/start.php';
            $wfi_fs = fs_dynamic_init( array(
                'id'             => '323',
                'slug'           => 'woo-fiscalita-italiana',
                'public_key'     => 'pk_b8c01da60ee2cb8916918a0fd7160',
                'is_premium'     => false,
                'has_addons'     => true,
                'has_paid_plans' => true,
                'menu'           => array(
                'slug'   => 'woo-fiscalita-italiana',
                'parent' => array(
                'slug' => 'woocommerce',
            ),
            ),
                'is_live'        => true,
            ) );
        }
        
        return $wfi_fs;
    }
    
    // Init Freemius.
    wfi_fs();
    require_once plugin_dir_path( __FILE__ ) . 'includes/functions.php';
    require_once plugin_dir_path( __FILE__ ) . 'includes/shortcode.php';
    require_once plugin_dir_path( __FILE__ ) . 'public/class-woo-fiscalita-italiana.php';
    require_once plugin_dir_path( __FILE__ ) . 'public/includes/WFI_ActDeact.php';
    /*
     * Register hooks that are fired when the plugin is activated or deactivated.
     * When the plugin is deleted, the uninstall.php file is loaded.
     */
    register_activation_hook( __FILE__, array( 'WFI_ActDeact', 'activate' ) );
    register_deactivation_hook( __FILE__, array( 'WFI_ActDeact', 'deactivate' ) );
    add_action( 'plugins_loaded', array( 'Woo_Fiscalita_Italiana', 'get_instance' ), 9999 );
    /*
     * -----------------------------------------------------------------------------
     * Dashboard and Administrative Functionality
     * -----------------------------------------------------------------------------
     */
    
    if ( is_admin() && (!defined( 'DOING_AJAX' ) || !DOING_AJAX) ) {
        require_once plugin_dir_path( __FILE__ ) . 'admin/class-woo-fiscalita-italiana-admin.php';
        add_action( 'plugins_loaded', array( 'Woo_Fiscalita_Italiana_Admin', 'get_instance' ) );
    }
    
    /**
     * Clean the system at uninstall of WFI
     * 
     * @global object $wpdb
     * @return void
     */
    function wfi_fs_uninstall()
    {
        global  $wpdb ;
        
        if ( is_multisite() ) {
            $blogs = $wpdb->get_results( "SELECT blog_id FROM {$wpdb->blogs}", ARRAY_A );
            if ( $blogs ) {
                foreach ( $blogs as $blog ) {
                    switch_to_blog( $blog['blog_id'] );
                    delete_option( 'woo-fiscalita-italiana' );
                    delete_option( 'woo-fiscalita-italiana-settings' );
                    restore_current_blog();
                }
            }
        }
        
        delete_option( 'woo-fiscalita-italiana' );
        delete_option( 'woo-fiscalita-italiana-settings' );
    }
    
    wfi_fs()->add_action( 'after_uninstall', 'wfi_fs_uninstall' );
}