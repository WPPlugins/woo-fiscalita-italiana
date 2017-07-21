<?php

/**
 * This class contain the activate and deactive method and relates.
 *
 * @package   Woo_Fiscalita_Italiana
 * @author    Codeat <support@codeat.co>
 * @license   GPL-2.0+
 * @link      http://codeat.co
 * @copyright 2016 
 */
class WFI_ActDeact {

  /**
   * Initialize the Act/Deact
   */
  function __construct() {
    // Activate plugin when new blog is added
    add_action( 'wpmu_new_blog', array( $this, 'activate_new_site' ) );
  }

  /**
   * Fired when the plugin is activated.
   *
   * @since    1.0.0
   *
   * @param boolean $network_wide True if active in a multiste, false if classic site.
   * 
   * @return void
   */
  public static function activate( $network_wide ) {
    if ( function_exists( 'is_multisite' ) && is_multisite() ) {
      if ( $network_wide ) {
        // Get all blog ids
        $blog_ids = self::get_blog_ids();
        foreach ( $blog_ids as $blog_id ) {
          switch_to_blog( $blog_id );
          self::single_activate();
          restore_current_blog();
        }
      } else {
        self::single_activate();
      }
    } else {
      self::single_activate();
    }
  }

  /**
   * Fired when the plugin is deactivated.
   *
   * @since    1.0.0
   *
   * @param boolean $network_wide True if WPMU superadmin uses
   *                              "Network Deactivate" action, false if
   *                              WPMU is disabled or plugin is
   *                              deactivated on an individual blog.
   * 
   * @return void
   */
  public static function deactivate( $network_wide ) {
    if ( function_exists( 'is_multisite' ) && is_multisite() ) {
      if ( $network_wide ) {
        // Get all blog ids
        $blog_ids = self::get_blog_ids();
        foreach ( $blog_ids as $blog_id ) {
          switch_to_blog( $blog_id );
          self::single_deactivate();
          restore_current_blog();
        }
      } else {
        self::single_deactivate();
      }
    } else {
      self::single_deactivate();
    }
  }

  /**
   * Fired when a new site is activated with a WPMU environment.
   *
   * @since    1.0.0
   *
   * @param integer $blog_id ID of the new blog.
   * 
   * @return void
   */
  public function activate_new_site( $blog_id ) {
    if ( 1 !== did_action( 'wpmu_new_blog' ) ) {
      return;
    }

    switch_to_blog( $blog_id );
    self::single_activate();
    restore_current_blog();
  }

  /**
   * Get all blog ids of blogs in the current network that are:
   * - not archived
   * - not spam
   * - not deleted
   *
   * @since    1.0.0
   *
   * @return array|false The blog ids, false if no matches.
   */
  private static function get_blog_ids() {
    global $wpdb;

    // Get an array of blog ids
    $sql = 'SELECT blog_id FROM ' . $wpdb->blogs .
            " WHERE archived = '0' AND spam = '0'
			AND deleted = '0'";

    return $wpdb->get_col( $sql );
  }

  /**
   * Fired for each blog when the plugin is activated.
   *
   * @since    1.0.0
   * @return void
   */
  private static function single_activate() {
    if ( !WC_Tax::find_rates( array( 'country' => 'IT' ) ) ) {
      $tax_rate = array(
          'tax_rate_country' => 'IT',
          'tax_rate_state' => '',
          'tax_rate' => '22',
          'tax_rate_name' => __( 'Tax', WFI_TEXTDOMAIN ),
          'tax_rate_priority' => 1,
          'tax_rate_compound' => 0,
          'tax_rate_shipping' => 1,
          'tax_rate_order' => 0,
          'tax_rate_class' => ''
      );

      WC_Tax::_insert_tax_rate( $tax_rate );
    }    
  }

  /**
   * Fired for each blog when the plugin is deactivated.
   *
   * @since    1.0.0
   * @return void
   */
  private static function single_deactivate() {
    wp_clear_scheduled_hook( 'wfi_eu_vat_sync' );
  }

}

new WFI_ActDeact();
