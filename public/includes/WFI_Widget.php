<?php

/**
 * WooCommerce Fiscalita Italiana
 *
 * @package   Woo_Fiscalita_Italiana
 * @author    Codeat <support@codeat.co>
 * @copyright 2016 
 * @license   GPL-2.0+
 * @link      http://codeat.co
 */

/**
 * Widget ODR
 */
class WFI_Widget_ODR extends WP_Widget {

	/**
	 * Sets up the widgets name etc
	 */
	public function __construct() {
		$widget_ops = array( 
			'classname' => 'wfi_widget_ODR',
			'description' => 'Print the ODR code',
		);
		parent::__construct( 'wfi_widget_ODR', 'Italian Fisco ODR', $widget_ops );
	}

	/**
	 * Outputs the content of the widget
	 *
	 * @param array $args     Arguments.
	 * @param array $instance Instance.
	 * 
	 * @return void
	 */
	public function widget( $args, $instance ) {
		echo do_shortcode('[wfi_site_odr]');
	}

}

add_action('widgets_init',
	create_function('', 'return register_widget("WFI_Widget_ODR");')
);