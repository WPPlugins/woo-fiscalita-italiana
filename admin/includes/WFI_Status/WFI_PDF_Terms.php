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
 * Generate PDF terms
 */
class WFI_PDF_Terms {

  /**
   * Initialize the plugin
   *
   * @since 1.0.0
   */
  public function __construct() {
    // DOMPDF initialization
    require_once( plugin_dir_path( __FILE__ ) . '../../../includes/vendor/autoload.php' );
    define( 'DOMPDF_ENABLE_AUTOLOAD', false );
    require plugin_dir_path( __FILE__ ) . '../../../includes/vendor/dompdf/dompdf/dompdf_config.inc.php';
    require_once(plugin_dir_path( __FILE__ ) . '../../../includes/vendor/dompdf/dompdf/include/autoload.inc.php');
    // Create the folder for the file of the plugins
    $this->path = get_wfi_folder();
    wp_mkdir_p( $this->path );
  }

  /**
   * Generate the term page
   * 
   * @return void
   */
  public function generated_terms() {
    // Extract the text of terms & conditions
    $terms_id = wc_get_page_id( 'terms' );
    if ( !empty( $terms_id ) ) {
      if ( function_exists( 'icl_get_languages' ) ) {
        $languages = icl_get_languages( 'skip_missing=1' );
        if ( 1 < count( $languages ) ) {
          foreach ( $languages as $l ) {
            $post = get_post( icl_object_id( $terms_id, 'page', false, $l[ 'language_code' ] ) );
            $this->generate_term( $post, $l[ 'language_code' ] );
          }
        }
      } else {
        $post = get_post( $terms_id );
        $this->generate_term( $post );
      }
      new WP_Admin_Notice( __( 'Terms and Conditions pdf successfully updated!', WFI_TEXTDOMAIN ), 'updated' );
    } else {
      new WP_Admin_Notice( __( 'The page Terms and Conditions is not set in WooCommerce!', WFI_TEXTDOMAIN ), 'error' );
    }
  }

  /**
   * Generate the html content
   * 
   * @param object $post Post object.
   * @param string $lng  Language.
   * 
   * @return void
   */
  public function generate_term( $post, $lng = '' ) {
    $content = '<h1>' . $post->post_title . '</h1>';
    $content .=  apply_filters( 'the_content', $post->post_content );
    $content .=  '<br>';
    if ( !empty( get_site_vat() ) ) {
      $content .=  __( 'Tax VAT', WFI_TEXTDOMAIN ) . get_site_vat() . ' - ';
    }
    if ( !empty( get_site_fc() ) ) {
      $content .=  __( 'SSN', WFI_TEXTDOMAIN ) . get_site_fc() . ' - ';
    }
    $content .=  get_bloginfo( 'name' ) . get_site_address(). get_site_phone();
    $content .=  ' - ' . get_option( 'admin_email' ) . ' - '. get_site_url();
    // Generate the pdf
    $dompdf = new DOMPDF();
    $dompdf->load_html( $content );
    $dompdf->set_paper( 'A4', 'potrait' );
    $dompdf->render();
    if ( empty( $lng ) ) {
      $lng = strtolower( get_locale() );
    }
    file_put_contents( $this->path . '/terms-' . $lng . '.pdf', $dompdf->output() );
  }

}

new WFI_PDF_Terms();
