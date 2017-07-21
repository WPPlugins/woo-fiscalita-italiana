<?php

/**
 * WooCommerce Fiscalita Italiana
 *
 * @package   Woo_Fiscalita_Italiana
 * @author    Codeat <support@codeat.co>
 * @copyright 2016 GPL 2.0+
 * @license   GPL-2.0+
 * @link      http://codeat.it
 */

/**
 * Email stuff
 */
class WFI_Email {

  /**
   * Initialize the class
   *
   * @since 1.0.0
   */
  public function __construct() {
    $this->settings = get_wfi_settings();
    // add_filter( 'woocommerce_email_footer_text', array( $this, 'add_terms' ), 10, 9999 );
    add_filter( 'woocommerce_email_attachments', array( $this, 'attach_terms' ), 10, 3 );
  }

  /**
   * Add the link
   * 
   * @param string $text The footer.
   * 
   * @return string
   */
  public function add_terms( $text ) {
    return $text . '<br><a href="' . esc_url( get_permalink( wc_get_page_id( 'terms' ) ) ) . '" target="_blank">' . __( 'Terms & Conditions', 'woocommerce' ) . '</a>';
  }

  /**
   * Attach the terms pdf in all the emails
   * 
   * @param array   $attachments Attachments.
   * @param integer $id          ID.
   * @param array   $object      Post object.
   * 
   * @return array
   */
  public function attach_terms( $attachments, $id, $object ) {
    $path = wp_upload_dir();
    $path = $path[ 'basedir' ] . '/wfi';
    $locale = strtolower( get_locale() );
    if ( defined( 'ICL_LANGUAGE_CODE' ) ) {
      $locale = ICL_LANGUAGE_CODE;
    }
    $attachments[] = $path . '/terms-' . $locale . '.pdf';
    return $attachments;
  }

}

new WFI_Email();
