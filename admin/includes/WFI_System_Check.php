<?php

/**
 * WooCommerce Fiscalitá Italiana
 *
 * @package   Woo_Fiscalita_Italiana
 * @author    Codeat <support@codeat.co>
 * @copyright 2016 GPL 2.0+
 * @license   GPL-2.0+
 * @link      http://codeat.it
 */
/**
 * Class for the panel system
 */
class WFI_System_Check
{
    /**
     * Initialize the class
     *
     * @since 1.0.0
     * 
     * @return void
     */
    public function __construct()
    {
        $this->settings = get_wfi_settings();
    }
    
    /**
     * Generate the box with the status
     * 
     * @return void
     */
    public function generate_check_system()
    {
        
        if ( !wfi_fs()->is_plan( 'professional' ) ) {
            ?>
			<div class="postbox message-alert"><?php 
            _e( 'Using the free version of the plugin do not cover every aspect of Italian fiscality.<br> Get the pro version for a full control and get notified about new normatives.', WFI_TEXTDOMAIN );
            ?>
</div>
		<?php 
        }
        
        ?>
		<h1><?php 
        _e( 'Mandatory for Italian Fisco', WFI_TEXTDOMAIN );
        ?>
</h1>
		<?php 
        $mandatory = $this->required_check();
        foreach ( $mandatory as $key => $value ) {
            $this->generate_line( $key, $value['name'], $value['tooltip'] );
        }
        ?>
		<h1><?php 
        _e( 'Non mandatory', WFI_TEXTDOMAIN );
        ?>
</h1>
		<?php 
        $optional = $this->optional_check();
        foreach ( $optional as $key => $value ) {
            $this->generate_line( $key, $value['name'], $value['tooltip'] );
        }
    }
    
    /**
     * Detect the function and print the status for the panel
     * 
     * @param number $id      ID.
     * @param string $name    Text.
     * @param string $tooltip The tooltip text.
     * 
     * @return void
     */
    public function generate_line( $id, $name, $tooltip )
    {
        $valid = 'error';
        switch ( $id ) {
            case 'info':
                if ( (!empty(get_site_vat()) || !empty(get_site_fc())) && !empty(get_site_address()) && !empty(get_site_postalcode()) ) {
                    $valid = 'success';
                }
                break;
            case 'tax_enabled':
                $valid = 'error';
                if ( get_option( 'woocommerce_calc_taxes' ) !== 'no' ) {
                    $valid = 'success';
                }
                break;
            case 'fields_checkout':
                $valid = 'success';
                break;
            case 'fields_order':
                $valid = 'success';
                break;
            case 'italian_tax':
                if ( WC_Tax::find_rates( array(
                    'country' => 'IT',
                ) ) ) {
                    $valid = 'success';
                }
                break;
            case 'site_footer':
                $valid = 'invalid';
                if ( !empty(get_site_address()) && !empty(get_site_postalcode()) && !empty($this->settings['info_done']) ) {
                    $valid = 'success';
                }
                break;
            case 'odr':
                $valid = 'invalid';
                if ( !empty(get_site_odr()) && !empty($this->settings['odr_done']) ) {
                    $valid = 'success';
                }
                break;
            case 'terms':
                if ( !empty(wc_get_page_id( 'terms' )) ) {
                    $valid = 'success';
                }
                break;
            case 'pdf_invoice':
                
                if ( class_exists( 'WooCommerce_PDF_Invoices' ) ) {
                    $valid = 'success';
                    $name .= ' WooCommerce PDF Invoices & Packing Slips';
                } else {
                    
                    if ( function_exists( 'yith_ywpi_init' ) ) {
                        $valid = 'success';
                        $name .= ' YITH WooCommerce PDF Invoice and Shipping List';
                    } else {
                        $name .= __( 'Nothing', WFI_TEXTDOMAIN );
                    }
                
                }
                
                break;
            case 'pages':
                
                if ( !empty(get_option( 'woocommerce_shipping_methods_page_id' )) && !empty(get_option( 'woocommerce_payment_methods_page_id' )) ) {
                    $valid = 'success';
                } else {
                    
                    if ( empty(get_option( 'woocommerce_shipping_methods_page_id' )) ) {
                        $valid = 'invalid';
                    } elseif ( empty(get_option( 'woocommerce_payment_methods_page_id' )) ) {
                        $valid = 'invalid';
                    }
                
                }
                
                break;
            case 'extract_tax':
                $valid = 'success';
                if ( get_option( 'woocommerce_tax_display_cart' ) === 'incl' ) {
                    $valid = 'invalid';
                }
                break;
        }
        $this->print_line( apply_filters( 'wfi_check_detection', array(
            'id'      => $id,
            'name'    => $name,
            'tooltip' => $tooltip,
            'valid'   => $valid,
        ) ) );
    }
    
    /**
     * Print the status
     * 
     * @param array $args The attribute.
     * 
     * @return void
     */
    public function print_line( $args )
    {
        echo  '<p>' . $args['name'] . '
        <span class="marker ' . $args['id'] . ' ' . $args['valid'] . '"></span>
        <span class="woocommerce-help-tip" data-tip="' . $args['tooltip'] . '"></span>
    </p>' ;
    }
    
    /**
     * List of status detection required
     * 
     * @return array
     */
    public function required_check()
    {
        $required = array(
            'info'            => array(
            'name'    => __( 'Info Data Completed', WFI_TEXTDOMAIN ),
            'tooltip' => __( 'You must complete the Info Tab Section', WFI_TEXTDOMAIN ),
        ),
            'tax_enabled'     => array(
            'name'    => __( 'WooCommerce Tax System Enabled', WFI_TEXTDOMAIN ),
            'tooltip' => __( 'Don\'t forget to activate the tax system in WooCommerce in the settings', WFI_TEXTDOMAIN ),
        ),
            'fields_checkout' => array(
            'name'    => __( 'Additional Checkout Fields', WFI_TEXTDOMAIN ),
            'tooltip' => __( 'VAT for Companies and SSN for Private must be in Checkout and in User profile', WFI_TEXTDOMAIN ),
        ),
            'fields_order'    => array(
            'name'    => __( 'Additional Invoices Fields', WFI_TEXTDOMAIN ),
            'tooltip' => __( 'VAT and SSN must be also in the invoices', WFI_TEXTDOMAIN ),
        ),
            'italian_tax'     => array(
            'name'    => __( 'Tax for Italy set at 22%', WFI_TEXTDOMAIN ),
            'tooltip' => __( 'Tax rate for Italy set to the current percentage', WFI_TEXTDOMAIN ),
        ),
            'site_footer'     => array(
            'name'    => __( 'Company Data in the footer', WFI_TEXTDOMAIN ),
            'tooltip' => __( 'You must put your company data in every website\'s page. Usually this kind of data are placed into the footer', WFI_TEXTDOMAIN ),
        ),
            'odr'             => array(
            'name'    => __( 'ODR', WFI_TEXTDOMAIN ),
            'tooltip' => __( 'You must place a link to Online Dispute Risolution, also if you use alternative platforms, according with 2013/11/EU Directive', WFI_TEXTDOMAIN ),
        ),
            'terms'           => array(
            'name'    => __( 'Terms and Conditions are set', WFI_TEXTDOMAIN ),
            'tooltip' => __( 'You have to set a page for Terms and Conditions', WFI_TEXTDOMAIN ),
        ),
        );
        return apply_filters( 'wfi_check_required', $required );
    }
    
    /**
     * List of status detection required
     * 
     * @return array
     */
    public function optional_check()
    {
        $optional = array(
            'pdf_invoice' => array(
            'name'    => __( 'PDF invoice plugin finded: ', WFI_TEXTDOMAIN ),
            'tooltip' => __( 'We support <strong>YITH WooCommerce PDF Invoice</strong> and <strong>PDF invoices & packing slips</strong>. Choose your favourite plugin and we will automatically personlaize it for fit the italian standards.', WFI_TEXTDOMAIN ),
        ),
            'pages'       => array(
            'name'    => __( 'Shipping and Payment Methods Pages', WFI_TEXTDOMAIN ),
            'tooltip' => __( 'Optional pages that helps your users to keep track of your shipping and payment methods. You can generate them pushing the specific button under the <strong>Settings Tab</strong>', WFI_TEXTDOMAIN ),
        ),
            'extract_tax' => array(
            'name'    => __( 'Extract of the Taxes', WFI_TEXTDOMAIN ),
            'tooltip' => __( 'Show the amount of Taxes in the Cart or Checkout add that amount also on the Orders and Invoices', WFI_TEXTDOMAIN ),
        ),
        );
        return apply_filters( 'wfi_check_optional', $optional );
    }
    
    /**
     * Print a button
     * 
     * @param number $id   ID.
     * @param string $name Text.
     * 
     * @return void
     */
    public function generate_button( $id, $name )
    {
        echo  '<a href="' . add_query_arg( $id, 'true', get_admin_url() . 'admin.php?page=woo-fiscalita-italiana' ) . '#tabs-settings" class="button">' . $name . '</a>' ;
    }
    
    /**
     * List of action button
     * 
     * @return array
     */
    public function actions()
    {
        $actions = array(
            'create_wfi_pages' => array(
            'name' => __( 'Create Pages', WFI_TEXTDOMAIN ),
        ),
        );
        return apply_filters( 'wfi_check_actions', $actions );
    }

}
new WFI_System_Check();