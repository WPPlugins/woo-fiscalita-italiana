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
 * Free Shipping for same postal code
 */
class WFI_Shipping_SamePC {

  /**
   * Initialize the plugin by setting localization and loading public scripts
   * and styles.
   *
   * @since 1.0.0
   */
  public function __construct() {
    add_action( 'woocommerce_proceed_to_checkout', array( $this, 'alert_for_samecap' ) );
    add_filter( 'woocommerce_update_order_review_fragments', array( $this, 'filter_alert_for_samecap' ) );
    add_action( 'woocommerce_cart_calculate_fees', array( $this, 'same_cap_free_retirement' ) );
    // AJAX reload
    add_action( 'woocommerce_checkout_order_review', array( $this, 'alert_for_samecap' ) );
  }

  /**
   * Set free
   * 
   * @global object $woocommerce
   * 
   * @return boolean
   */
  public function same_cap_free_retirement() {
    if ( is_same_postalcode() ) {
      global $woocommerce;
      $woocommerce->cart->add_fee( __( 'Retirement Headquarters', WFI_TEXTDOMAIN ), 0, true, '' );
      $woocommerce->cart->shipping_total = 0;
      $woocommerce->shipping->reset_shipping();
	  return true;
    }
	return false;
  }

  /**
   * Add alert
   * 
   * @param array $fragments List of array.
   * 
   * @return string
   */
  public function filter_alert_for_samecap( $fragments = array() ) {
    $fragments[ '.woocommerce-checkout-alert-samecap' ] = '<div class="woocommerce-checkout-alert-samecap">';
    if ( is_same_postalcode() ) {
      $fragments[ '.woocommerce-checkout-alert-samecap' ] .= '<small>';
      $fragments[ '.woocommerce-checkout-alert-samecap' ] .= __( '<b>Note:</b> If your ZIP code is the same of the store you have to retire the package directly at our Headquarter.', WFI_TEXTDOMAIN );
      $fragments[ '.woocommerce-checkout-alert-samecap' ] .= '</small><div class="clearfix"></div>';
    }
    $fragments[ '.woocommerce-checkout-alert-samecap' ].= '</div>';
    return $fragments;
  }

  /**
   * Force the message 
   * 
   * @return void
   */
  public function alert_for_samecap() {
    $fragments = $this->filter_alert_for_samecap();
    echo $fragments[ '.woocommerce-checkout-alert-samecap' ];
  }

}

new WFI_Shipping_SamePC();
