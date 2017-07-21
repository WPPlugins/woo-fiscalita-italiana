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
 * Add info of the user in the order backend
 */
class WFI_Order_User_Info {

	/**
	 * Initialize the class
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		// Add in WooCommerce Order the user infos
		add_action( 'woocommerce_admin_order_data_after_billing_address', array( $this, 'vatfc' ), 10, 1 );
		add_action( 'woocommerce_order_details_after_order_table', array( $this, 'vatfc' ) );
		add_action( 'woocommerce_email_customer_details_fields', array( $this, 'vatfc_email' ), 9999, 3 );
		add_action( 'woocommerce_admin_order_data_after_billing_address', array( $this, 'dpr633_72' ) );
		add_action( 'woocommerce_email_after_order_table', array( $this, 'dpr633_72' ) );
	}

	/**
	 * Show VAT and SSN to order in bemail
	 * 
	 * @param array $fields        Fields for user area..
	 * @param array $sent_to_admin Is admin?..
	 * @param array $order         Order object.
	 * 
	 * @return array
	 */
	public function vatfc_email( $fields, $sent_to_admin, $order ) {
		$args = wfi_get_order_fields( $order, true );
		if ( $args[ 'pvtazd' ] === 'azienda' ) {
			if ( empty( $args[ 'vat' ] ) ) {
				$args[ 'vat' ] = __( 'Missing Data', WFI_TEXTDOMAIN );
			}
			$fields[] = array( 'label' => __( 'Tax VAT', WFI_TEXTDOMAIN ), 'value' => $args[ 'vat' ] );
		}
		if ( empty( $args[ 'ssn' ] ) ) {
			$args[ 'ssn' ] = __( 'Missing Data', WFI_TEXTDOMAIN );
		}
		$fields[] = array( 'label' => __( 'SSN', WFI_TEXTDOMAIN ), 'value' => $args[ 'ssn' ] );
		return $fields;
	}

	/**
	 * Show VAT and SSN to order in backend
	 * 
	 * @param integer $order Order.
	 * 
	 * @return void
	 */
	public function vatfc( $order ) {
		$args = wfi_get_order_fields( $order );
		$html = '';
		if ( isset( $args[ 'pvtazd' ] ) && $args[ 'pvtazd' ] === 'azienda' ) {
			if ( empty( $args[ 'vat' ] ) ) {
				$args[ 'vat' ] = __( 'Missing Data', WFI_TEXTDOMAIN );
			}
			$html .= '<p><strong>' . __( 'Tax VAT', WFI_TEXTDOMAIN ) . ':</strong> ' . $args[ 'vat' ] . '</p>';
		}
		if ( empty( $args[ 'ssn' ] ) ) {
			$args[ 'ssn' ] = __( 'Missing Data', WFI_TEXTDOMAIN );
		}
		$html .= '<p><strong>' . __( 'SSN', WFI_TEXTDOMAIN ) . ':</strong> ' . $args[ 'ssn' ] . '</p>';
		echo $html;
	}

	/**
	 * Print the italian law quote to not use taxation for country where is not configured a tax.
	 * 
	 * @param integer $order_id Order ID.
	 * 
	 * @return boolean
	 */
	public static function dpr633_72( $order_id ) {
		$settings = get_wfi_settings();
		if ( empty( $settings[ 'dpr633-72' ] ) ) {
			return true;
		}
		$order = new WC_Order( $order_id );
		if ( count( $order->get_tax_totals() ) === 0 ) {
			echo '<br>' . __( 'Operazione fuori campo iva ai sensi dell\'art. 7 dpr 633/72', WFI_TEXTDOMAIN );
			return true;
		}
		return false;
	}

}

new WFI_Order_User_Info();
