<?php

/**
 * @package   Woo_Fiscalita_Italiana
 * @author  Mte90 <mte90net@gmail.com>
 * @license   GPL-2.0+
 * @copyright 2014-2015
 * @since    1.0.0
 */

/**
 * All the CMB related code.
 */
class WFI_CMB {

	/**
	 * Initialize CMB2.
	 *
	 * @since     1.0.0
	 */
	public function __construct() {
		require_once( plugin_dir_path( __FILE__ ) . '/CMB2/init.php' );

		/**
		 * CMB2 Conditionals
		 * https://github.com/jcchavezs/cmb2-conditionals/
		 */
		require_once( plugin_dir_path( __FILE__ ) . '/CMB2-Conditionals/cmb2-conditionals.php' );

		// CMB2 alert
		add_action( 'cmb2_save_options-page_fields', array( $this, 'option_saved' ), 4, 9999 );

		add_action( 'cmb2_admin_init', array( $this, 'user_profile_fields' ) );
	}

	/**
	 * Alert WFI updates
	 * 
	 * @param integer $object_id Object ID.
	 * @param integer $cmb_id    CMB metabox ID.
	 * @param object  $updated   Object.
	 * @param object  $object    Object.
	 * 
	 * @return void
	 */
	public function option_saved( $object_id, $cmb_id, $updated, $object ) {
		if ( $cmb_id === WFI_TEXTDOMAIN . '_options' ) {
			$notice = new WP_Admin_Notice( __( 'Italian Fisco information updated!', WFI_TEXTDOMAIN ), 'updated' );
			$notice->output();
		}
		if ( $cmb_id === WFI_TEXTDOMAIN . '_options-second' ) {
			$notice = new WP_Admin_Notice( __( 'Italian Fisco settings updated!', WFI_TEXTDOMAIN ), 'updated' );
			$notice->output();
		}
	}

	/**
	 * User fields in the backend profile
	 * @return void
	 */
	public function user_profile_fields() {
		$cmb = new_cmb2_box( array(
			'id' => WFI_TEXTDOMAIN . '_profile_metabox',
			'object_types' => array( 'user' ),
			'show_names' => true,
			'new_user_section' => 'add-new-user'
				) );

		$cmb->add_field( array(
			'name' => esc_html__( 'VAT', WFI_TEXTDOMAIN ),
			'id' => 'piva',
			'type' => 'text',
		) );

		$cmb->add_field( array(
			'name' => esc_html__( 'SSN', WFI_TEXTDOMAIN ),
			'id' => 'cf',
			'type' => 'text',
		) );
	}

}

new WFI_CMB();
