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
 * Class for create the pages for payment and shipping methods
 *
 * @package Woo_Fiscalita_Italiana
 * @author  Codeat <support@codeat.co>
 */
class WFI_Pages {

  /**
   * Initialize the plugin
   *
   * @since     1.0.0
   */
  public function __construct() {
  }

  /**
   * Create woocommerce pages
   */
  public function create_woo_pages() {
    wc_create_page( __( 'shipping-methods', WFI_VERSION ), 'woocommerce_shipping_methods_page_id', __( 'Shipping Methods', WFI_VERSION ), '[wfi_shipping_methods]' );
    wc_create_page( __( 'payment-methods', WFI_VERSION ), 'woocommerce_payment_methods_page_id', __( 'Payment Methods', WFI_VERSION ), '[wfi_payment_methods]' );
    new WP_Admin_Notice( __( 'Payment and Shipping methods page successfull created!', WFI_VERSION ), 'updated' );
  }
}
