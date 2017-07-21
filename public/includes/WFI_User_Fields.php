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
 * The user fields
 */
class WFI_User_Fields {

	/**
	 * Initialize the plugin by setting localization and loading public scripts
	 * and styles.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		if ( is_admin() ) {
			return false;
		}
		$this->settings = get_wfi_settings();
		// WooCommerce Registration Form
		if ( !empty( $this->settings[ 'user_fields_reg' ] ) ) {
			add_action( 'woocommerce_register_form', array( $this, 'vatfc_fields' ) );
			add_action( 'woocommerce_register_post', array( $this, 'vatfc_validation' ), 9999, 3 );
		}
		// WooCommerce Edit Account Form
		add_action( 'woocommerce_edit_account_form', array( $this, 'vatfc_fields' ) );
		// WooCommerce Checkout Billing Information Form
		add_action( 'woocommerce_checkout_billing', array( $this, 'vatfc_fields' ), 9999 );
		// WooCommerce Customer Profile Fields
		add_filter( 'woocommerce_customer_meta_fields', array( $this, 'filter_vatcf_fields' ) );
		// WooCommerce validation of fields after submission
		add_action( 'woocommerce_save_account_details_errors', array( $this, 'vatfc_validation' ), 9999, 3 );
		add_action( 'woocommerce_checkout_process', array( $this, 'vatfc_validation_checkout' ) );
		// WooCommerce save fields after submission
		add_action( 'woocommerce_created_customer', array( $this, 'save_fields_profile' ) );
		add_action( 'woocommerce_save_account_details', array( $this, 'save_fields_profile' ) );
		// Force save on user information on WooCommerce checkout
		add_action( 'woocommerce_checkout_update_order_meta', array( $this, 'save_fields_checkout' ) );
	}

	/**
	 * Add vat and ssn field 
	 * 
	 * @return void
	 */
	public function vatfc_fields() {
		$required = true;
		if ( empty( $this->settings[ 'required_vat_fields' ] ) ) {
			$required = false;
			woocommerce_form_field( 'pvtazd', array(
				'type' => 'radio',
				'options' => array(
					'privato' => __( 'Private', WFI_TEXTDOMAIN ),
					'azienda' => __( 'Company', WFI_TEXTDOMAIN )
				),
				'class' => array( 'pvtazd form-row-wide' ),
				'required' => true,
				'label' => __( 'Are a you a company?', WFI_TEXTDOMAIN ),
					), get_user_pvtazd() );
		}
		woocommerce_form_field( 'piva', array(
			'type' => 'text',
			'class' => array( 'piva form-row-wide' ),
			'label' => __( 'VAT Number', WFI_TEXTDOMAIN ),
			'required' => $required,
				), get_user_vat() );
		woocommerce_form_field( 'cf', array(
			'type' => 'text',
			'class' => array( 'cf form-row-wide' ),
			'label' => __( 'SSN', WFI_TEXTDOMAIN ),
			'required' => $required,
				), get_user_fc() );
		?> 
		<script>
			function wfi_hide_fields() {
			  var fc = '<?php echo (!empty( $this->settings[ 'fc_only_italian' ] )); ?>';
			  var vat = '<?php echo (!empty( $this->settings[ 'vat_only_italian' ] )); ?>';
			  var mandatory = '<?php echo (!empty( $this->settings[ 'required_vat_fields' ] )); ?>';
			  var italy = (jQuery('.country_select :selected').val() === 'IT' || jQuery('.country_select').length === 0);
			  jQuery('#cf_field, #piva_field').hide();
			  if (vat !== '1' || vat === '1' && italy) {
				jQuery('#piva_field').show();
				if (jQuery('input[name=pvtazd]:visible').length !== 0 || mandatory === '1') {
				  if (jQuery('input[name=pvtazd]:checked').val() === 'privato') {
					jQuery('#piva_field').hide();
				  }
				}
			  }
			  if (fc !== '1' || fc === '1' && italy) {
				if (jQuery('input[name=pvtazd]:visible').length !== 0 || mandatory === '1') {
				  jQuery('#cf_field').show();
				}
			  }
			}
			jQuery(document).ready(function () {
			  jQuery('#cf_field, #piva_field').hide();
			  wfi_hide_fields();
			  jQuery('input[name=pvtazd], input[name=ask_invoice], #billing_country').change(function () {
				wfi_hide_fields();
			  });
			});
		</script>
		<?php
		if ( empty( $this->settings[ 'required_vat_fields' ] ) ) {
			?>
			<style>
				#pvtazd_field label, #pvtazd_field input {
					display: inline-block;
					vertical-align: middle;  
					margin-right:5px;
				}
			</style>
			<?php
		}
	}

	/**
	 * Add vat and ssn field in the profile backend
	 * 
	 * @param array $profile_fields Array with all the fields.
	 * 
	 * @return array
	 */
	public function filter_vatcf_fields( $profile_fields ) {
		if ( empty( $this->settings[ 'required_vat_fields' ] ) ) {
			$profile_fields[ 'fisco' ][ 'fields' ] = array(
				'pvtazd' => array(
					'type' => 'select',
					'label' => __( 'Are a you a company?', WFI_TEXTDOMAIN ),
					'options' => array(
						'privato' => __( 'Private', WFI_TEXTDOMAIN ),
						'azienda' => __( 'Company', WFI_TEXTDOMAIN )
					) ),
				'piva' => array( 'label' => __( 'VAT Number', WFI_TEXTDOMAIN ) ),
			);
		}

		if ( !empty( $this->settings[ 'fc_only_italian' ] ) && detect_customer_italian_by_billing() || empty( $this->settings[ 'fc_only_italian' ] ) ) {
			$profile_fields[ 'fisco' ][ 'fields' ][ 'cf' ] = array( 'label' => __( 'SSN', WFI_TEXTDOMAIN ) );
		}
		return $profile_fields;
	}

	/**
	 * Validation for the fields
	 * 
	 * @param string $username          The username.
	 * @param string $email             The email.
	 * @param object $validation_errors The error object.
	 * 
	 * @return boolean
	 */
	public function vatfc_validation( $username, $email, $validation_errors ) {
		if ( !empty( $this->settings[ 'fc_only_italian' ] ) && detect_customer_italian_by_billing() || empty( $this->settings[ 'fc_only_italian' ] ) ) {
			if ( isset( $_POST[ 'ask_invoice' ] ) ) {
				$ask_invoice = sanitize_text_field( $_POST[ 'ask_invoice' ] );
				if ( $ask_invoice === 'no' ) {
					return false;
				}
			}
			$cf = sanitize_text_field( $_POST[ 'cf' ] );
			if ( empty( $cf ) ) {
				$validation_errors->add( 'cf_error', __( 'SSN is empty.', WFI_TEXTDOMAIN ) );
				return false;
			} elseif ( preg_match( '/^[a-z]{6}[0-9]{2}[a-z][0-9]{2}[a-z][0-9]{3}[a-z]$/i', $cf ) === 0 ) {
				$validation_errors->add( 'cf_no_valid', __( 'SSN is not valid.', WFI_TEXTDOMAIN ) );
				return false;
			}
		}
		return true;
	}

	/**
	 * Validation in checkout
	 * 
	 * @return booleaan
	 */
	public function vatfc_validation_checkout() {
		$cf = sanitize_text_field( $_POST[ 'cf' ] );
		$piva = sanitize_text_field( $_POST[ 'piva' ] );
		if ( isset( $_POST[ 'ask_invoice' ] ) ) {
			$ask_invoice = sanitize_text_field( $_POST[ 'ask_invoice' ] );
			if ( $ask_invoice === 'no' ) {
				return false;
			}
		}
		if ( !empty( $this->settings[ 'fc_only_italian' ] ) && detect_customer_italian_by_billing() || empty( $this->settings[ 'fc_only_italian' ] ) ) {
			if ( empty( $cf ) ) {
				wc_add_notice( __( 'SSN is empty.', WFI_TEXTDOMAIN ), 'error' );
				return false;
			} elseif ( preg_match( '/^[a-z]{6}[0-9]{2}[a-z][0-9]{2}[a-z][0-9]{3}[a-z]$/i', $cf ) === 0 ) {
				wc_add_notice( __( 'SSN is not valid.', WFI_TEXTDOMAIN ), 'error' );
				return false;
			}
		}
		if ( get_user_pvtazd() === 'azienda' && $piva === '' ) {
			wc_add_notice( __( 'VAT is empty.', WFI_TEXTDOMAIN ), 'error' );
			return false;
		}
		return true;
	}

	/**
	 * Save fields in the profile
	 * 
	 * @param integer $user_id The user ID.
	 * 
	 * @return void
	 */
	public function save_fields_profile( $user_id ) {
		$cf = sanitize_text_field( $_POST[ 'cf' ] );
		$piva = sanitize_text_field( $_POST[ 'piva' ] );
		if ( empty( $this->settings[ 'required_vat_fields' ] ) ) {
			$pvtazd = sanitize_text_field( $_POST[ 'pvtazd' ] );
			update_user_meta( $user_id, 'pvtazd', $pvtazd );
			if ( !empty( $piva ) ) {
				update_user_meta( $user_id, 'piva', str_replace( array( ' ', '-', '_', '.' ), '', $piva ) );
			}
		}
		if ( !empty( $this->settings[ 'fc_only_italian' ] ) && detect_customer_italian_by_billing() || empty( $this->settings[ 'fc_only_italian' ] ) ) {
			update_user_meta( $user_id, 'cf', $cf );
		}
	}

	/**
	 * Save fields in the order
	 * 
	 * @param integer $order_id The Order ID.
	 * 
	 * @return void
	 */
	public function save_fields_checkout( $order_id ) {
		if ( is_user_logged_in() ) {
			$user_id = get_current_user_id();
			$this->save_fields_profile( $user_id );
		}
		$cf = sanitize_text_field( $_POST[ 'cf' ] );
		$piva = sanitize_text_field( $_POST[ 'piva' ] );
		if ( empty( $this->settings[ 'required_vat_fields' ] ) ) {
			$pvtazd = sanitize_text_field( $_POST[ 'pvtazd' ] );
			update_post_meta( $order_id, 'pvtazd', $pvtazd );
			if ( !empty( $piva ) ) {
				update_post_meta( $order_id, 'piva', str_replace( array( ' ', '-', '_', '.' ), '', $piva ) );
			}
		}
		if ( !empty( $this->settings[ 'fc_only_italian' ] ) && detect_customer_italian_by_billing() || empty( $this->settings[ 'fc_only_italian' ] ) ) {
			update_post_meta( $order_id, 'cf', $cf );
		}
	}

}

new WFI_User_Fields();
