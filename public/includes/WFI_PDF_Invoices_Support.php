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
 * Add information on PDF Invoices
 *
 * @package Woo_Fiscalita_Italiana
 * @author  Codeat <support@codeat.co>
 */
class WFI_PDF_Invoices_Support {

  /**
   * Initialize the class
   *
   * @since     1.0.0
   */
  public function __construct() {
    if ( class_exists( 'WooCommerce_PDF_Invoices' ) ) {
	if ( is_italian_base_shop() ) {
	  add_action( 'wpo_wcpdf_before_order_data', array( $this, 'vat_fc_pdf_invoices_packaging' ), 10, 2 );
	}
	add_action( 'wpo_wcpdf_after_order_details', array( $this, 'site_vat_fc_pdf_invoices_packaging' ), 10, 2 );
    }

    if ( function_exists( 'yith_ywpi_init' ) ) {
	if ( is_italian_base_shop() ) {
	  add_action( 'yith_ywpi_invoice_template_customer_data', array( $this, 'vat_fc_yith_invoices' ), 9999 );
	}
	add_action( 'yith_ywpi_invoice_template_footer', array( $this, 'site_vat_fc_yith_invoices' ) );
	add_filter( 'yith_ywpi_new_invoice_number', array( $this, 'yith_new_invoice_number' ), 9999, 2 );
	add_filter( 'yith_ywpi_get_formatted_invoice_number', array( $this, 'yith_invoice_number' ), 9999, 2 );
    }
  }

  /**
   * Print VAT and Fiscal code
   * 
   * @param string $export
   * @param string $order
   */
  public function vat_fc_pdf_invoices_packaging( $export, $order ) {
    ?>
    <tr class="piva">
        <th><?php _e( 'Tax VAT', WFI_TEXTDOMAIN ); ?>:</th>
        <td><?php the_user_vat( $order->user_id ); ?></td>
    </tr>
    <tr class="cf">
        <th><?php _e( 'SSN', WFI_TEXTDOMAIN ); ?>:</th>
        <td><?php the_user_fc( $order->user_id ); ?></td>
    </tr>
    <?php
  }

  /**
   * Print VAT and Fiscal code
   * 
   * @global object $ywpi_document
   */
  public function vat_fc_yith_invoices() {
    global $ywpi_document;
    WFI_Order_User_Info::dpr633_72( $ywpi_document->order );
    ?>
    <div>
	  <?php _e( 'Tax VAT', WFI_TEXTDOMAIN ); ?>:
	  <?php the_user_vat( $ywpi_document->user_id ); ?><br>
	  <?php _e( 'SSN', WFI_TEXTDOMAIN ); ?>:
	  <?php the_user_fc( $ywpi_document->user_id ); ?>
    </div><br>
    <?php
  }

  /**
   * Print VAT and Fiscal code
   * 
   * @param string $export
   * @param number $order
   */
  public function site_vat_fc_pdf_invoices_packaging( $export, $order ) {
    WFI_Order_User_Info::dpr633_72( $order );
    ?>
    <div><?php _e( 'Tax VAT', WFI_TEXTDOMAIN ); ?> <?php the_site_vat(); ?> - 
	  <?php _e( 'SSN', WFI_TEXTDOMAIN ); ?> <?php the_site_fc(); ?> -
	  <?php echo get_bloginfo( 'name' ); ?> <?php the_site_address(); ?> 
	  <?php the_site_phone(); ?> - <?php echo get_option( 'admin_email' ) ?>
    </div>
    <?php
  }

  /**
   * Print VAT and Fiscal code
   */
  public function site_vat_fc_yith_invoices() {
    ?>
    <div><?php _e( 'Tax VAT', WFI_TEXTDOMAIN ); ?> <?php the_site_vat(); ?> - 
	  <?php _e( 'SSN', WFI_TEXTDOMAIN ); ?> <?php the_site_fc(); ?> -
	  <?php echo get_bloginfo( 'name' ); ?> <?php the_site_address(); ?> 
	  <?php the_site_phone(); ?> - <?php echo get_option( 'admin_email' ) ?>
    </div>
    <?php
  }

  public function yith_invoice_number( $number, $order ) {
    return $order->get_order_number();
  }

  public function yith_new_invoice_number( $number, $order ) {
    return str_replace( '/', '-', $this->yith_invoice_number( $number, $order ) );
  }

}

new WFI_PDF_Invoices_Support();
