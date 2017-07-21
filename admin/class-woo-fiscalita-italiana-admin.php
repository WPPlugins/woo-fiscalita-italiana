<?php

/**
 * WooCommerce Fiscalitá Italiana
 * 
 * @package   Woo_Fiscalita_Italiana_Admin
 * @author    Codeat <support@codeat.co>
 * @copyright 2016 
 * @license   GPL-2.0+
 * @link      http://codeat.co
 */
/**
 * Plugin class. This class should ideally be used to work with the
 * administrative side of the WordPress site.
 *
 * If you're interested in introducing public-facing
 * functionality, then refer to `class-woo-fiscalita-italiana.php`
 */
class Woo_Fiscalita_Italiana_Admin
{
    /**
     * Instance of this class.
     *
     * @var object
     */
    protected static  $instance = null ;
    /**
     * Slug of the plugin screen.
     *
     * @var string
     */
    protected  $plugin_admin_view = null ;
    /**
     * Initialize the plugin by loading admin scripts & styles and adding a
     * settings page and menu.
     *
     * @since 1.0.0
     */
    private function __construct()
    {
        // Load admin style sheet and JavaScript.
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
        // Add the options page and menu item.
        add_action( 'admin_menu', array( $this, 'add_plugin_admin_menu' ) );
        // Add an action link pointing to the options page.
        $plugin_basename = plugin_basename( plugin_dir_path( realpath( dirname( __FILE__ ) ) ) . WFI_TEXTDOMAIN . '.php' );
        add_filter( 'plugin_action_links_' . $plugin_basename, array( $this, 'add_action_links' ) );
        /*
         * Load Wp_Admin_Notice for the notices in the backend
         * 
         * First parameter the HTML, the second is the css class
         */
        if ( !class_exists( 'WP_Admin_Notice' ) ) {
            require_once plugin_dir_path( __FILE__ ) . 'includes/WP-Admin-Notice/WP_Admin_Notice.php';
        }
        /*
         * Load CMB
         */
        require_once plugin_dir_path( __FILE__ ) . 'includes/WFI_CMB.php';
        require_once plugin_dir_path( __FILE__ ) . 'includes/WFI_PP.php';
        /*
         * Import Export settings
         */
        require_once plugin_dir_path( __FILE__ ) . 'includes/WFI_ImpExp.php';
        require_once plugin_dir_path( __FILE__ ) . 'includes/WFI_Status_Init.php';
        if ( empty(get_wfi_info()) && !strpos( $_SERVER['REQUEST_URI'], '/network/' ) ) {
            new WP_Admin_Notice( sprintf( __( 'Check the %sWooCommerce Fiscalit&agrave; Italiana detection system%s for solve the missing issues!', WFI_TEXTDOMAIN ), '<a href="' . get_admin_url() . 'admin.php?page=woo-fiscalita-italiana#tabs-check" id="check-system-alert">', '</a>' ), 'error' );
        }
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
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Register and enqueue admin-specific style sheet.
     *
     * @since 1.0.0
     *
     * @return void Return early if no settings page is registered.
     */
    public function enqueue_admin_styles()
    {
        if ( !isset( $this->plugin_admin_view ) ) {
            return;
        }
        $screen = get_current_screen();
        
        if ( $this->plugin_admin_view === $screen->id || strpos( $_SERVER['REQUEST_URI'], 'index.php' ) || strpos( $_SERVER['REQUEST_URI'], get_bloginfo( 'wpurl' ) . '/wp-admin/' ) ) {
            wp_enqueue_style(
                WFI_TEXTDOMAIN . '-admin-styles',
                plugins_url( 'assets/css/admin.css', __FILE__ ),
                array( 'dashicons' ),
                WFI_VERSION
            );
            wp_enqueue_style(
                'woocommerce_admin_styles',
                WC()->plugin_url() . '/assets/css/admin.css',
                array( WFI_TEXTDOMAIN . '-admin-styles' ),
                WC_VERSION
            );
        }
    
    }
    
    /**
     * Register and enqueue admin-specific JavaScript.
     *
     * @since 1.0.0
     *
     * @return void Return early if no settings page is registered.
     */
    public function enqueue_admin_scripts()
    {
        if ( !isset( $this->plugin_admin_view ) ) {
            return;
        }
        $screen = get_current_screen();
        
        if ( $this->plugin_admin_view === $screen->id ) {
            wp_register_script(
                'jquery-tiptip',
                WC()->plugin_url() . '/assets/js/jquery-tiptip/jquery.tipTip.min.js',
                array( 'jquery' ),
                WC_VERSION,
                true
            );
            wp_enqueue_script(
                WFI_TEXTDOMAIN . '-admin-script',
                plugins_url( 'assets/js/admin.js', __FILE__ ),
                array( 'jquery', 'jquery-tiptip', 'jquery-ui-tabs' ),
                WFI_VERSION
            );
        }
    
    }
    
    /**
     * Register the administration menu for this plugin into the WordPress Dashboard menu.
     *
     * @since 1.0.0
     * 
     * @return void
     */
    public function add_plugin_admin_menu()
    {
        $this->plugin_admin_view = add_submenu_page(
            'woocommerce',
            __( 'Italian Fisco', WFI_TEXTDOMAIN ),
            __( 'Italian Fisco', WFI_TEXTDOMAIN ),
            'manage_woocommerce',
            WFI_TEXTDOMAIN,
            array( $this, 'display_plugin_admin_page' )
        );
    }
    
    /**
     * Render the settings page for this plugin.
     *
     * @since 1.0.0
     * 
     * @return void
     */
    public function display_plugin_admin_page()
    {
        require_once plugin_dir_path( __FILE__ ) . '/includes/WFI_System_Check.php';
        include_once 'views/admin.php';
    }
    
    /**
     * Add settings action link to the plugins page.
     * 
     * @param array $links Array of links.
     *
     * @since 1.0.0
     * 
     * @return array
     */
    public function add_action_links( $links )
    {
        return array_merge( array(
            'settings' => '<a href="' . admin_url( 'options-general.php?page=' . WFI_TEXTDOMAIN ) . '">' . __( 'Settings' ) . '</a>',
        ), $links );
    }

}