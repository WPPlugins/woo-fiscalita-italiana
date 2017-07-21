<?php

/**
 * WooCommerce Fiscalitá Italiana
 *
 * @package   Woo_Fiscalita_Italiana
 * @author    Codeat <support@codeat.co>
 * @license   GPL-2.0+
 * @link      http://codeat.it
 * @copyright 2016 GPL 2.0+
 */
/**
 * Init the methods to generate something by url
 *
 * @package Woo_Fiscalita_Italiana
 * @author  Codeat <support@codeat.co>
 */
class WFI_Status_Init
{
    /**
     * Initialize the plugin by setting localization and loading public scripts
     * and styles.
     *
     * @since     1.0.0
     */
    public function __construct()
    {
        add_action( 'admin_init', array( $this, 'create_pages' ) );
    }
    
    /**
     * Create wfi pages
     */
    public function create_pages()
    {
        
        if ( !empty($_GET['create_wfi_pages']) && current_user_can( 'manage_options' ) ) {
            require_once plugin_dir_path( __FILE__ ) . 'WFI_Status/WFI_Pages.php';
            $pages = new WFI_Pages();
            $pages->create_woo_pages();
        }
    
    }
    
    /**
     * Generate pdf terms
     */
    public function generate_terms()
    {
    }

}
new WFI_Status_Init();