<?php
/**
 * Class WC_Hipay_Gateway_Pro_Request file.
 *
 * @package WooCommerce\Gateways
 */

if(!defined('ABSPATH')){ exit; }

class WC_Hipay_Gateway_Pro_Request {
    
    /**
    * md5 key to get callback from Hipay Wallet
    */
    protected $key;
    
    /**
    * Pointer to gateway making the request.
    *
    * @var WC_Hipay_Gateway_Pro
    */
    protected $gateway;
    
    /**
    * Endpoint for requests from Hipay Wallet.
    *
    * @var string
    */
    protected $notify_url;
    
    /**
    * Constructor.
    *
    * @param WC_Hipay_Gateway_Pro $gateway Hipay Wallet gateway object.
    */
    public function __construct($gateway){
                        
        $wc = WC();
        
        $this->gateway = $gateway;
        
        $this->notify_url = $wc->api_request_url('WC_Hipay_Gateway_Pro');
        
        $this->key = md5(uniqid(rand(), true));
        
    }
    
    /**
     * Get the Hipay Wallet request URL for an order.
     *
     * @param  WC_Order $order Order object
     */
    public function get_request_url($order, $order_id){
                
        global $wpdb;
            
        global $woocommerce;
        
        $control_order = 0;
        
        update_post_meta($order_id, 'woohipaypro_key', $this->key);

        $woohipaypro_settings = get_option('woocommerce_woohipaypro_settings', true);
        
        $order_hash  = $this->key;
            
        $hipay_sub   = array();

        $hipay_products = array();

        $hipay_order_label = '';
        
        /**
         * Get product(s) data
         */
        foreach($order->get_items() as $item_key => $item){
                        
            $item_id    = $item->get_id();
            $item_name  = $item->get_name();
            $item_data  = $item->get_data();

            $_product   = wc_get_product($item_data['product_id']);

            $_prices    = array(
                $_product->get_regular_price(),
                $_product->get_sale_price(),
                $_product->get_price()
            );
            
            /**
             * Get customer data
             */
            // $cur_user = (array) $order->get_user();
            
            // unset($cur_user['allcaps']);
            
            $customer_data = array(
                'customer_id'           => $order->get_customer_id(),
                'user_id'               => $order->get_user_id(),
                'user'                  => '',
                'ip'                    => $order->get_customer_ip_address(),
                'custom_note'           => $order->get_customer_note(),
                'billing_firstname'     => $order->get_billing_first_name(),
                'billing_lastname'      => $order->get_billing_last_name(),
                'billing_company'       => $order->get_billing_company(),
                'billing_address_1'     => $order->get_billing_address_1(),
                'billing_address_2'     => $order->get_billing_address_2(),
                'billing_city'          => $order->get_billing_city(),
                'billing_state'         => $order->get_billing_state(),
                'billing_postcode'      => $order->get_billing_postcode(),
                'billing_country'       => $order->get_billing_country(),
                'email'                 => $order->get_billing_email(),
                'phone'                 => $order->get_billing_phone(),
                'transaction_id'        => $order->get_transaction_id()
            );

            /**
             * Get product Hipay Wallet data
             */
            $post_id = wc_get_order_item_meta($item_id, '_product_id');

            $allow_hipaysubscription = get_post_meta($post_id, 'allow_hipaysubscription');
            $hipay_is_subscription   = $allow_hipaysubscription[0] == 'yes' ? 1 : 0;

            $hipaysubscription_data  = get_post_meta($post_id, 'hipaysubscription_data');
            
            $hipay_sub_data          = array($item_name, $hipaysubscription_data);

            if($hipay_is_subscription == 1){
                
                array_push($hipay_sub, $hipay_sub_data);
                
            }
            
            $product_data = array(
                'order_id'              => $order_id,
                'order_hash'            => $order_hash,
                'post_id'               => $post_id,
                'product_id'            => $item_data['product_id'],
                'name'                  => $item_data['name'],
                'variation_id'          => $item_data['variation_id'],
                'quantity'              => $item_data['quantity'],
                'tax_class'             => $item_data['tax_class'],
                'line_subtotal'         => $item_data['subtotal'],
                'line_subtotal_tax'     => $item_data['subtotal_tax'],
                'line_total'            => $item_data['total'],
                'line_total_tax'        => $item_data['total_tax'],
                'subscription'          => $hipay_is_subscription,
                'subscription_data'     => $hipaysubscription_data,
                'prices'                => $_prices,
                'notes'                 => array(
                    'name'              => esc_html(__('Renouvellement','woohipaypro').' '.$item_data['name']),
                    'description'       => esc_html(__('Renouvellement du produit/service','woohipaypro').' '.$item_data['name']),
                    'bname'             => esc_html(__('Validation','woohipaypro').' '.$item_data['name']),
                    'bdescription'      => esc_html(__('Souscription validée au produit/service','woohipaypro').' '.$item_data['name'])
                )
            );
            
            if($hipay_is_subscription == 1 && intval($item_data['quantity']) > 1){
                
                $control_order++;
                
            }

            array_push($hipay_products, $product_data);
            
        }
        
        if(count($hipay_sub) == 1){
                
            $hipay_order_label = $hipay_products[0]['name'].' - '.get_bloginfo();

        }else if(count($hipay_sub) == 0 && count($hipay_products) == 1){

            $hipay_order_label = $hipay_products[0]['name'].' - '.get_bloginfo();

        }else if(count($hipay_sub) == 0 && count($hipay_products) > 1){

            $hipay_order_label = count($hipay_products).' '.__('article(s)','woohipaypro').' - '.get_bloginfo();

        }
                
        if(count($order->get_items()) > 1 && count($hipay_sub) > 0){
            
            wc_add_notice(esc_html__("Votre panier compte au moins 1 produit (ou service) sous forme d'abonnement. Pour procéder au paiement votre panier ne doit contenir qu'un seul produit (ou service) sous forme d'abonnement, ou uniquement des produits à paiement unique.",'woohipaypro'), 'error');
                        
            return $woocommerce->cart->get_cart_url();
            
        }else if($control_order > 0){
            
            wc_add_notice(esc_html__("Votre panier compte au moins 1 produit (ou service) sous forme d'abonnement dont la quantité est supérieure à 1. Pour procéder au paiement votre panier ne doit contenir qu'un seul produit (ou service) sous forme d'abonnement en quantité 1, ou uniquement des produits à paiement unique.",'woohipaypro'), 'error');
            
            return $woocommerce->cart->get_cart_url();
            
        }else{

            $hipay_pay  = $this->build_request_url($order->get_total(), $woohipaypro_settings, $hipay_order_label, $hipay_products, $customer_data, $order_id);
            
            if($hipay_pay['status'] == 0){

                wc_add_notice(esc_html__('Code erreur :','woohipaypro').' '.$hipay_pay['error']['code'].'<br/>'.__('Descriptif de l\'erreur :','woohipaypro').' '.$hipay_pay['error']['message'].'<br/>'.__('Référence marchand :','woohipaypro').' '.$hipay_pay['error']['merchantReference'],'error');
                
                return $woocommerce->cart->get_cart_url();
            
            }else if($hipay_pay['status'] == 1){

                if(isset($hipay_pay['form']) && $hipay_pay['form'] != ''){

                    return esc_url_raw('https://bozprod.eu/api/woohipaywalletpro/form.php?form='.$hipay_pay['form']);

                }else{

                    return esc_url_raw($hipay_pay['url']);

                }

            }

        }
        
    }
    
    /**
     * Build the request to Boz Prod API to return Hipay Wallet request URL for the current order.
     */
    public function build_request_url($o,$h,$l,$p,$c,$i){
    
        global $wp;

        global $wpdb;
                    
        global $woocommerce;
                
        $type = count($p) == 1 && $p[0]['subscription'] == 1 ? 'subscription' : 'oneshot';
                
        update_post_meta($i, 'woohipaypro_type', $type);

        $root = array(
            get_bloginfo(),
            site_url(),
            get_permalink(get_option('woocommerce_checkout_page_id')),
            get_locale(),
            $_SERVER['REMOTE_ADDR'],
            time(),
            get_woocommerce_currency(),
            get_current_user_id(),
            md5(uniqid(rand(), true)),
            $this->notify_url,
            $c,
            $p,
            $type
        );

        $data   = array($o,$h,$l,$p,$root);

        $str    = json_encode($data);

        $url    = 'https://bozprod.eu/api/woohipaywalletpro/data.php';
        
        $args = array(
            'method'      => 'POST',
            'timeout'     => 45,
            'redirection' => 5,
            'httpversion' => '1.0',
            'blocking'    => true,
            'headers'     => array(),
            'body'        => $data,
            'cookies'     => array()
        );
        
        $response = wp_remote_post(esc_url_raw($url), $args);
        
        if(!is_wp_error($response)){
            
            $api_response = json_decode(wp_remote_retrieve_body($response), true);
                        
        }else{
            
            $api_response = NULL;
                        
        }
        
        return $api_response;

    }
            
}