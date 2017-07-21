<?php

/**
 * All the PointerPlus related code.
 *
 * @package   Woo_Fiscalita_Italiana
 * @author  Mte90 <mte90net@gmail.com>
 * @license   GPL-2.0+
 * @copyright 2014-2015
 * @since    1.0.0
 */
class WFI_PP
{
    /**
     * Initialize CMB2.
     *
     * @since     1.0.0
     */
    public function __construct()
    {
        require_once plugin_dir_path( __FILE__ ) . '/PointerPlus/class-pointerplus.php';
        new PointerPlus( array(
            'prefix' => WFI_TEXTDOMAIN,
        ) );
        //$pointerplus->reset_pointer();
        add_filter(
            WFI_TEXTDOMAIN . '-pointerplus_list',
            array( $this, 'pointers' ),
            10,
            2
        );
    }
    
    /**
     * Add pointers.
     *
     * @param $pointers
     * @param $prefix for your pointers
     *
     * @return mixed
     */
    public function pointers( $pointers, $prefix )
    {
        $pointers = array_merge( $pointers, array(
            $prefix . '_tab_info'  => array(
            'selector'   => '#tab-info-pointer',
            'title'      => __( 'Store information', WFI_TEXTDOMAIN ),
            'text'       => __( 'Fill all the company fields in order to complete the store. Those datas will be used in invoices and for the shortcodes.', WFI_TEXTDOMAIN ),
            'width'      => 260,
            'align'      => 'left',
            'edge'       => 'top',
            'icon_class' => 'dashicons-admin-settings',
            'next'       => $prefix . '_tab_check',
        ),
            $prefix . '_tab_check' => array(
            'selector'   => '#tab-check-pointer',
            'title'      => __( 'Check your system', WFI_TEXTDOMAIN ),
            'text'       => __( 'Use the tab to check the status of your WooCommerce. All the mandatory fields must be marked as green to be sure that your store is Italian fiscally compliant.', WFI_TEXTDOMAIN ),
            'width'      => 260,
            'align'      => 'left',
            'edge'       => 'top',
            'icon_class' => 'dashicons-admin-settings',
        ),
        ) );
        return $pointers;
    }

}
new WFI_PP();