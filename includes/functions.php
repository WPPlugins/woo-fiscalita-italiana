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
 * Check if the shop is located in Italy
 * 
 * @return boolean
 */
function is_italian_base_shop() {
	if ( function_exists( 'wc_get_base_location' ) ) {
		$location = wc_get_base_location();
		if ( $location[ 'country' ] === 'IT' ) {
			return true;
		}
	} else {
		// On PDF generation the function not exist -.-'
		$location = apply_filters( 'woocommerce_get_base_location', get_option( 'woocommerce_default_country' ) );
		$location = explode( ':', $location );
		if ( $location[ 0 ] === 'IT' ) {
			return true;
		}
	}
	return false;
}

/**
 * Get the array of information of WFI
 * 
 * @return array
 */
function get_wfi_info() {
	return apply_filters( 'wfi_info', get_option( 'woo-fiscalita-italiana', null ) );
}

/**
 * Get the array of settings of WFI
 * 
 * @return array
 */
function get_wfi_settings() {
	return apply_filters( 'wfi_settings', get_option( 'woo-fiscalita-italiana-settings', null ) );
}

/**
 * Get VAT of the shop
 * 
 * @return string
 */
function get_site_vat() {
	$value = get_wfi_info();
	if ( isset( $value[ 'piva' ] ) ) {
		return $value[ 'piva' ];
	}
	return;
}

/**
 * Print the VAT
 * 
 * @return void
 */
function the_site_vat() {
	echo get_site_vat();
}

/**
 * Get fiscal code of the shop
 * 
 * @return string
 */
function get_site_fc() {
	$value = get_wfi_info();
	return $value[ 'cf' ];
}

/**
 * Print the fiscal code
 * 
 * @return void
 */
function the_site_fc() {
	echo get_site_fc();
}

/**
 * Get address of the shop
 * 
 * @return string
 */
function get_site_address() {
	$value = get_wfi_info();
	return $value[ 'indirizzo' ];
}

/**
 * Print the address
 * 
 * @return void
 */
function the_site_address() {
	echo get_site_address();
}

/**
 * Get postalcode of the shop
 * 
 * @return string
 */
function get_site_postalcode() {
	$value = get_wfi_info();
	return $value[ 'cap' ];
}

/**
 * Print the postalcode
 * 
 * @return void
 */
function the_site_postalcode() {
	echo get_site_postalcode();
}

/**
 * Get the phone of the shop
 * 
 * @return string
 */
function get_site_phone() {
	$value = get_wfi_info();
	return $value[ 'phone' ];
}

/**
 * Print the phone
 * 
 * @return void
 */
function the_site_phone() {
	echo get_site_phone();
}

/**
 * Get REA of the shop
 * 
 * @return string
 */
function get_site_rea() {
	$value = get_wfi_info();
	return $value[ 'rea' ];
}

/**
 * Print the REA
 * 
 * @return void
 */
function the_site_rea() {
	echo get_site_rea();
}

/**
 * Get the Camera di Commercio code
 * 
 * @return string
 */
function get_site_camcom() {
	$value = get_wfi_info();
	return $value[ 'camcom' ];
}

/**
 * Print Camera di Commercio code
 * 
 * @return void
 */
function the_site_camcom() {
	echo get_site_camcom();
}

/**
 * Get ODR of the site
 * 
 * @return string
 */
function get_site_odr() {
	$value = get_wfi_info();
	if ( !isset( $value[ 'odr' ] ) ) {
		$value[ 'odr' ] = '';
	}
	if ( empty( $value[ 'odr' ] ) ) {
		$value[ 'odr' ] = '<a href="http://ec.europa.eu/consumers/odr/" target="_blank">http://ec.europa.eu/consumers/odr/</a>';
	}
	$text = wpautop( $value[ 'odr' ] );
	return $text;
}

/**
 * Print ODR
 * 
 * @return void
 */
function the_site_odr() {
	echo get_site_odr();
}

/**
 * Get type of company of the user
 * 
 * @param integer $user_id User ID.
 * 
 * @return string
 */
function get_user_pvtazd( $user_id = '' ) {
	if ( empty( $user_id ) ) {
		$user_id = get_current_user_id();
	}
	return get_user_meta( $user_id, 'pvtazd', true );
}

/**
 * Get VAT of the user
 * 
 * @param integer $user_id User ID.
 * 
 * @return string
 */
function get_user_vat( $user_id = '' ) {
	if ( empty( $user_id ) ) {
		$user_id = get_current_user_id();
	}
	return get_user_meta( $user_id, 'piva', true );
}

/**
 * Print the VAT of the user
 * 
 * @return void
 */
function the_user_vat() {
	echo get_user_vat();
}

/**
 * Get the Fiscal code of the user
 * 
 * @param integer $user_id User ID.
 * 
 * @return string
 */
function get_user_fc( $user_id = '' ) {
	if ( empty( $user_id ) ) {
		$user_id = get_current_user_id();
	}
	return get_user_meta( $user_id, 'cf', true );
}

/**
 * Print the fiscal code of the user
 * 
 * @return void
 */
function the_user_fc() {
	echo get_user_fc();
}

/**
 * Get the path of the custom folder of WFI
 * 
 * @return string
 */
function get_wfi_folder() {
	$path = wp_upload_dir();
	return $path[ 'basedir' ] . '/wfi';
}

/**
 * Detect if the postal code is in islands
 * 
 * @global object $woocommerce
 * @return boolean
 */
function is_shipping_for_islands() {
	global $woocommerce;
	$pc_islands = array(
		0 => "07",
		1 => "08",
		2 => "09",
		3 => "92",
		4 => "93",
		5 => "95",
		6 => "94",
		7 => "98",
		8 => "90",
		9 => "96",
		10 => "97",
		11 => "91",
		12 => "86",
		13 => "87",
		14 => "88"
	);

	$postalcode = apply_filters( 'wfi_customer_shipping_postcode', $woocommerce->customer->get_shipping_postcode(), $woocommerce->customer );

	foreach ( $pc_islands as $pc_island ) {
		if ( $pc_island === substr( $postalcode, 0, 2 ) ) {
			return true;
		}
	}
	return false;
}

/**
 * Check if the user postalcode is the same of the shop
 * 
 * @global object $woocommerce
 * @return boolean
 */
function is_same_postalcode() {
	global $woocommerce;
	$pc_user = apply_filters( 'wfi_customer_shipping_postcode', $woocommerce->customer->get_shipping_postcode(), $woocommerce->customer );
	$pc = get_site_postalcode();
	if ( $pc_user === $pc ) {
		return true;
	}
	return false;
}

/**
 * Detect if the customer is italian by billing address
 * 
 * @return boolean
 */
function detect_customer_italian_by_billing() {
	if ( method_exists( WC()->customer, 'get_billing_country' ) ) {
		$country = WC()->customer->get_billing_country();
	} else {
		$country = WC()->customer->get_country();
	}
	return ( int ) (apply_filters( 'wfi_customer_country', $country ) === 'IT');
}

/**
 * Get an array of the european countries
 * 
 * @return array
 */
function european_countries__premium_only() {
	$countries = array( 'AT', 'BE', 'BG', 'CY', 'CZ', 'DE', 'DK', 'EE', 'ES', 'FI', 'FR', 'GB', 'GR', 'HU', 'HR', 'IE', 'IT', 'LT', 'LU', 'LV', 'MT', 'NL', 'PL', 'PT', 'RO', 'SE', 'SI', 'SK' );
	return apply_filters( 'european_countries', $countries );
}

/**
 * Detect if the customer is european by billing address
 * 
 * @return boolean
 */
function detect_customer_european_by_billing__premium_only() {
	if ( method_exists( WC()->customer, 'get_billing_country' ) ) {
		$country = WC()->customer->get_billing_country();
	} else {
		$country = WC()->customer->get_country();
	}
	$country = apply_filters( 'wfi_detection_country_by_customer', $country );
	if ( in_array( $country, european_countries__premium_only(), true ) ) {
		return true;
	}
	return false;
}

/**
 * Detect if the customer is european by ip address
 * 
 * @return boolean
 */
function detect_customer_european_by_ip__premium_only() {
	$geoip = WC_Geolocation::geolocate_ip();
	// Is empty on local system
	if ( empty( $geoip[ 'country' ] ) && WP_DEBUG === true ) {
		return true;
	}
	if ( in_array( $geoip[ 'country' ], european_countries__premium_only(), true ) ) {
		return true;
	}
	return false;
}

/**
 * Detect if the user is european by ip/billing address
 * 
 * @return boolean
 */
function is_customer_european__premium_only() {
	if ( detect_customer_european_by_ip__premium_only() && detect_customer_european_by_billing__premium_only() ) {
		return true;
	}
	return false;
}

/**
 * Check if the VAT is a valid VIES
 * 
 * @param string $vat     VAT code.
 * @param string $country Country.
 * 
 * @return \WP_Error|boolean
 */
function is_valid_vat__premium_only( $vat, $country = '' ) {
	if ( $country === '' ) {
		if ( method_exists( WC()->customer, 'get_billing_country' ) ) {
			$country = WC()->customer->get_billing_country();
		} else {
			$country = WC()->customer->get_country();
		}
		$country = apply_filters( 'wfi_detection_country_by_customer', $country );
	}
	if ( !preg_match( '/^[0-9]{11}$/', $vat ) ) {
		return false;
	}
	if ( $country === 'IT' ) {
		return true;
	}

	$transient_cached = 'vat_validation_' . $country . '_' . $vat;
	$cached_request = get_transient( $transient_cached );

	if ( !$cached_request ) {
		$response = wp_remote_post( 'http://ec.europa.eu/taxation_customs/vies/vatResponse.html', array( 'body' => array(
				'action' => 'check',
				'check' => 'Verify',
				'memberStateCode' => $country,
				'number' => $vat
			) ) );

		if ( is_wp_error( $response ) ) {
			return new WP_Error( 'api', sprintf( 'VAT Error: %s', $response->get_error_message() ) );
		} else {
			$check = strpos( $response[ 'body' ], 'Yes, valid VAT number' );
			if ( !$check ) {
				return false;
			}
			set_transient( $transient_cached, 1, WEEK_IN_SECONDS );
			return true;
		}
	} elseif ( $cached_request ) {
		return true;
	}
}

/**
 * Get the fields wrapper for WooCommerce 2/3
 * 
 * @param WC_Order $order Order object.
 * 
 * @return array
 */
function wfi_get_order_fields( $order ) {
	$attr = array();
	if ( gettype( $order ) !== 'object' ) {
		$attr[ 'order_id' ] = $order;
		$order = new WC_Order( $order );
	}
	if ( version_compare( WOOCOMMERCE_VERSION, '2.7', '<' ) ) {
		$vat_order = get_post_meta( $attr[ 'order_id' ], 'piva', true );
		$pvtazd_order = get_post_meta( $attr[ 'order_id' ], 'pvtazd', true );
		$ssn_order = get_post_meta( $attr[ 'order_id' ], 'cf', true );
		$attr[ 'user_id' ] = $order->user_id;
		$attr[ 'order_date' ] = $order->order_date;
		$attr[ 'order_completed_date' ] = $order->completed_date;
		$attr[ 'order_paid_date' ] = $order->paid_date;
		$attr[ 'order_status' ] = $order->post_status;
		$attr[ 'order_note' ] = $order->post->post_excerpt;
		$attr[ 'user_email' ] = $order->customer_email;
		$attr[ 'user_registered' ] = get_user_by( 'email', $order->billing_email );
		$attr[ 'transaction' ] = $order->transaction_id;
	} else {
		$attr[ 'order_id' ] = $order->get_id();
		$vat_order = $order->get_meta( 'piva', true );
		$pvtazd_order = $order->get_meta( 'pvtazd', true );
		$ssn_order = $order->get_meta( 'cf', true );
		$attr[ 'order_date' ] = $order->get_date_created()->date_i18n( 'd/m/Y' );
		$attr[ 'order_completed_date' ] = $order->get_date_completed();
			$attr[ 'order_paid_date' ] = '';
		if ( !empty( $order->get_date_paid() ) ) {
			$attr[ 'order_paid_date' ] = $order->get_date_paid()->date_i18n( 'd/m/Y' );
		}
		$attr[ 'order_status' ] = $order->get_status();
		$attr[ 'user_id' ] = $order->get_user_id();
		$attr[ 'user_email' ] = $order->get_billing_email();
		$attr[ 'order_note' ] = implode( ",", $order->get_customer_order_notes() );
		$attr[ 'user_registered' ] = get_user_by( 'email', $order->get_billing_email() );
		$attr[ 'transaction' ] = $order->get_transaction_id();
	}
	// Check if user is guest not load the data from the user id that is the admin
	if ( isset( $attr[ 'user_registered' ]->ID ) ) {
		$attr[ 'vat' ] = get_user_vat( $attr[ 'user_id' ] );
		$attr[ 'ssn' ] = get_user_fc( $attr[ 'user_id' ] );
		$attr[ 'pvtazd' ] = get_user_pvtazd( $attr[ 'user_id' ] );
	}
	if ( !empty( $ssn_order ) ) {
		$attr[ 'vat' ] = $vat_order;
		$attr[ 'ssn' ] = $ssn_order;
		$attr[ 'pvtazd' ] = $pvtazd_order;
	}
	return $attr;
}
