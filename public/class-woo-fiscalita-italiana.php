<?php

/**
 * WooCommerce Fiscalita Italiana
 *
 * @package   Woo_Fiscalita_Italiana
 * @author    Codeat <support@codeat.co>
 * @copyright 2016 
 * @license   GPL-2.0+
 * @link      http://codeat.co
 */
/**
 * Plugin class. This class should ideally be used to work with the
 * public-facing side of the WordPress site.
 *
 * If you're interested in introducing administrative or dashboard
 * functionality, then refer to `class-woo-fiscalita-italiana-admin.php`
 */
class Woo_Fiscalita_Italiana
{
    /**
     * Unique identifier for your plugin.
     *
     * @var string
     */
    protected static  $plugin_name = 'WooCommerce Fiscalita Italiana' ;
    /**
     * Instance of this class.
     *
     * @var object
     */
    protected static  $instance = null ;
    /**
     * Settings
     *
     * @var array
     */
    public  $settings = null ;
    /**
     * Initialize the plugin by setting localization and loading public scripts
     * and styles.
     *
     * @since 1.0.0
     */
    private function __construct()
    {
        require_once plugin_dir_path( __FILE__ ) . 'includes/WFI_Widget.php';
        require_once plugin_dir_path( __FILE__ ) . 'includes/WFI_User_Fields.php';
        require_once plugin_dir_path( __FILE__ ) . 'includes/WFI_Order_User_Info.php';
        require_once plugin_dir_path( __FILE__ ) . 'includes/WFI_Email.php';
        $this->settings = get_wfi_settings();
        if ( !empty($this->settings['shipping_samepc']) ) {
            require_once plugin_dir_path( __FILE__ ) . 'includes/WFI_Shipping_SamePC.php';
        }
    }
    
    /**
     * Return the version
     *
     * @since 1.0.0
     *
     * @return Version const.
     */
    public function get_settings()
    {
        return get_wfi_settings();
    }
    
    /**
     * Return an instance of this class.
     *
     * @since 1.0.0
     *
     * @return object A single instance of this class.
     */
    public static function get_instance()
    {
        // If the single instance hasn't been set, set it now.
        if ( null == self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

}