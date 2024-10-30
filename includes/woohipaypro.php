<?php

/* 
 * WooCommerce HiPay Wallet Gateway Pro
 */

add_filter('woocommerce_payment_gateways', 'woohipaypro_add_gateway_class');

function woohipaypro_add_gateway_class($gateways){
    
    $gateways[] = 'WC_Hipay_Gateway_Pro';
    
    return $gateways;
    
}

add_action('plugins_loaded', 'woohipaypro_init_gateway_class');

/**
 * Init the class
 */
function woohipaypro_init_gateway_class(){
    
    if(!class_exists('WC_Payment_Gateway')){ return; }
    
    include_once WOOHIPAY_ROOT_PATH.'/includes/lib/wc-hipay-gateway-pro.php';
    
    function add_wc_hipay_pro_gateway($methods){
    
        $methods[] = 'WC_Hipay_Gateway_Pro';

        return $methods;

    }
    
}