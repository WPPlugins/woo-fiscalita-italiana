<?php

add_shortcode( 'wfi_payment_methods', 'payments_methods_shortcode' );
add_shortcode( 'wfi_site_odr', 'get_site_odr' );
function payments_methods_shortcode()
{
    $gateways = WC()->payment_gateways->get_available_payment_gateways();
    
    if ( !empty($gateways) ) {
        $html = '<ul class="payment_methods">';
        foreach ( $gateways as $gateway ) {
            $html .= '<li class="payment_method_' . $gateway->id . '">
                    <label for="payment_method_' . $gateway->id . '">' . $gateway->get_title() . ' ' . $gateway->get_icon() . '</label>';
            if ( $gateway->has_fields() || $gateway->get_description() ) {
                $html .= '<div class="payment_desc payment_method_' . $gateway->id . '">
                    <p>' . $gateway->get_description() . '</p>
                </div>';
            }
            $html .= '</li>';
        }
        $html .= '</ul>';
        return $html;
    }

}

add_shortcode( 'wfi_shipping_methods', 'shipping_methods_shortcode' );
function shipping_methods_shortcode()
{
    $methods = WC()->shipping->load_shipping_methods();
    
    if ( !empty($methods) ) {
        $html = '<ul class="payment_methods">';
        foreach ( $methods as $method ) {
            
            if ( $method->enabled === 'yes' ) {
                
                if ( !empty($method->cost) ) {
                    $cost = $method->cost;
                } else {
                    $cost = $method->fee;
                }
                
                $html .= '<li class="shipping_method_' . $method->id . '">
                    <label for="shipping_method_' . $method->id . '">' . $method->get_title() . ' - ' . $cost . ' ' . get_woocommerce_currency_symbol() . '</label>';
                if ( $method->method_description ) {
                    $html .= '<div class="shipping_desc shipping_method_' . $method->id . '">
                    <p>' . $method->method_description . '</p>
                </div>';
                }
                $html .= '</li>';
            }
        
        }
        $html .= '</ul>';
        return $html;
    }

}