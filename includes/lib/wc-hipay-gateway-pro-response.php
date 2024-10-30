<?php
/**
 * Class WC_Hipay_Gateway_Pro_Response file.
 *
 * @package WooCommerce\Gateways
 */

if(!defined('ABSPATH')){ exit; }

class WC_Hipay_Gateway_Pro_Response {
        
    public function __construct(){
        
        add_action('woocommerce_api_'.strtolower('WC_Hipay_Gateway_Pro'), array($this, 'woohipaypro_payment_callback'));

        add_action('woocommerce_api_woohipaypro_cancel_payment', array($this, 'woohipaypro_cancel_payment'));

        add_action('woocommerce_api_woohipaypro_payment_ko', array($this, 'woohipaypro_payment_ko'));
        
        add_action('woocommerce_api_woohipaypro_payment_treatment', array($this, 'woohipaypro_payment_treatment'));
        
    }
    
    /**
    * get callback from Hipay Wallet and check payment data
    */
    public function woohipaypro_payment_callback(){
        
        global $wp;

        global $wpdb;
                    
        global $woocommerce;
        
        $key        = isset($_GET['key']) && !empty($_GET['key']) ? sanitize_text_field($_GET['key']) : NULL;
        
        $order_id   = isset($_GET['order']) && !empty($_GET['order']) ? intval($_GET['order']) : NULL;
        
        $operation  = isset($_GET['operation']) && !empty($_GET['operation']) ? sanitize_text_field($_GET['operation']) : NULL;
        
        $status     = isset($_GET['status']) && !empty($_GET['status']) ? sanitize_text_field($_GET['status']) : NULL;
        
        $transid    = isset($_GET['transid']) && !empty($_GET['transid']) ? sanitize_text_field($_GET['transid']) : NULL;
        
        $subid      = isset($_GET['subid']) && !empty($_GET['subid']) ? sanitize_text_field($_GET['subid']) : 'oneshot';
        
        if($key != NULL && $order_id != NULL && $operation != NULL & $status != NULL && $transid != NULL  && $subid != NULL){
            
            update_post_meta($order_id, 'woohipaypro_operation', $operation);
            
            update_post_meta($order_id, 'woohipaypro_status', $status);
            
            $order = wc_get_order($order_id);
            
            if($operation == "authorization" && $status == "ok"){
                
                // First step OK

            }else if($operation == "capture" && $status == "ok"){

                // Capture OK ==> Payment is accepted by Hipay
                                                
                if($subid != 'oneshot'){
                    
                    $type = 'subscription';
                    
                    $payments_num = get_post_meta($order_id,'woohipaypro_payments',true);
                    
                    foreach($order->get_items() as $item_key => $item){
                            
                        $item_id    = $item->get_id();

                        $item_data  = $item->get_data();

                        $product_id = $item_data['product_id'];

                        $post_id    = wc_get_order_item_meta($item_id, '_product_id');

                        $hipaysubscription_data  = get_post_meta($post_id, 'hipaysubscription_data');

                        break;

                    }
                    
                    if(!$payments_num){
                        
                        update_post_meta($order_id, 'woohipaypro_transid', $transid);
                    
                        update_post_meta($order_id, 'woohipaypro_subid', $subid);
                        
                        update_post_meta($order_id, 'woohipaypro_payments', 1);
                        
                        update_post_meta($order_id, 'woohipaypro_subscription_status', 'active');
                        
                        $rebill_tokens          = $hipaysubscription_data[0]['cu_tokens_hipaysubscription'];
                        
                        $rebill_tokens_meta     = $hipaysubscription_data[0]['cycle_hipaysubscription'];
                        
                        $rebill_tokens_label    = $hipaysubscription_data[0]['credits_label_hipaysubscription'];
                        
                        $user_subscriptions = get_user_meta($order->get_user_id(),'woohipaypro_subscriptions',false);
                        
                        if($user_subscriptions){
                            
                            $subscriptions = $user_subscriptions[0];
                            
                            $subscriptions[$subid] = $order_id;
                            
                        }else{
                            
                            $subscriptions = array($subid => $order_id);
                            
                        }
                        
                        update_user_meta($order->get_user_id(),'woohipaypro_subscriptions',$subscriptions);
                        
                        $order->payment_complete();
                
                        $woocommerce->cart->empty_cart();

                        $order->add_order_note(esc_html(__('Commande d\'abonnement sur Hipay Wallet effectuée avec succès :','woohipaypro').' '.$transid), 0);
                        
                        $this->woohipaypro_credit_user($order_id, $type, $rebill_tokens, $rebill_tokens_meta, $rebill_tokens_label);
                        
                    }else{
                        
                        $pay_number = intval($payments_num);
                        
                        $pay_number++;
                        
                        update_post_meta($order_id,'woohipaypro_payments',$pay_number);
                                                
                        $customer_address = array(
                            'first_name' => $order->get_billing_first_name(),
                            'last_name'  => $order->get_billing_last_name(),
                            'company'    => $order->get_billing_company(),
                            'email'      => $order->get_billing_email(),
                            'phone'      => $order->get_billing_phone(),
                            'address_1'  => $order->get_billing_address_1(),
                            'address_2'  => $order->get_billing_address_2(),
                            'city'       => $order->get_billing_city(),
                            'state'      => $order->get_billing_state(),
                            'postcode'   => $order->get_billing_postcode(),
                            'country'    => $order->get_billing_country()
                        );
                        
                        $rebill_price           = $hipaysubscription_data[0]['cu_price_hipaysubscription'];
                        
                        $rebill_tokens          = $hipaysubscription_data[0]['cl_tokens_hipaysubscription'];
                        
                        $rebill_tokens_meta     = $hipaysubscription_data[0]['cycle_hipaysubscription'];
                        
                        $rebill_tokens_label    = $hipaysubscription_data[0]['credits_label_hipaysubscription'];
                        
                        $rebill_product = wc_get_product($product_id);

                        $rebill_product->set_price($rebill_price);
                        
                        $rebill_order = wc_create_order(array('customer_id' => $order->get_user_id()));
                        
                        $rebill_order->add_product($rebill_product, 1);
                        
                        $rebill_order->set_address($customer_address, 'billing');
                        
                        $rebill_order->calculate_totals();
                        
                        $rebill_order->save();
                        
                        $rebill_order_id = $rebill_order->get_id();
                        
                        update_post_meta($rebill_order_id, 'woohipaypro_original_order', $order_id);
                        
                        update_post_meta($rebill_order_id, 'woohipaypro_transid', $transid);
                    
                        update_post_meta($rebill_order_id, 'woohipaypro_subid', $subid);
                                                
                        $rebill_order->payment_complete();
                        
                        $woocommerce->cart->empty_cart();
                        
                        $rebill_order->add_order_note(esc_html(__('Paiement récurrent N°','woohipaypro').' '.$pay_number.' '.__('pour l\'abonnement de la commande','woohipaypro').' '.$order_id.', '.__('ID de l\'abonnement :','woohipaypro').' '.$transid), 0);
                        
                        $order->add_order_note(esc_html(__('Paiement récurrent N°','woohipaypro').' '.$pay_number.' '.__('pour l\'abonnement, commande N°','woohipaypro').' '.$rebill_order_id.', '.__('ID de la transaction :','woohipaypro').' '.$transid), 0);
                        
                        $this->woohipaypro_credit_user($order_id, $type, $rebill_tokens, $rebill_tokens_meta, $rebill_tokens_label);
                        
                    }
                    
                }else{
                    
                    $type = 'oneshot';
                                        
                    update_post_meta($order_id, 'woohipaypro_transid', $transid);
                    
                    $order->payment_complete();
                
                    $woocommerce->cart->empty_cart();

                    $order->add_order_note(esc_html(__('Commande sur Hipay Wallet effectuée avec succès, ID de transaction :','woohipaypro').' '.$transid), 0);
                    
                    $this->woohipaypro_credit_user($order_id, $type, '', '', '');
                    
                }
                
            }else if($operation == "authorization" && $status == "nok"){

                // Authorization failed ==> Payment failed
                $order->add_order_note(esc_html(__('Autorisation de paiement refusée pour la transaction suivante :','woohipaypro').' '.$transid), 0);

            }else if($operation == "capture" && $status == "nok"){

                // Capture failed ==> Payment failed
                $order->add_order_note(esc_html(__('Autorisation de paiement acceptée mais paiement refusé pour la transaction suivante :','woohipaypro').' '.$transid), 0);

            }else{

                // Unknown status ==> write a note with details
                $order->add_order_note(esc_html(__('Un problème indéterminé est survenu, la paiement n\'a pas pu être effectué pour la transaction suivante :','woohipaypro').' '.$transid), 0);

            }
            
        }else{
            
            // ERROR
            
        }
                        
    }
    
    /**
    * Redirect to checkout page with error notice if payment cancelled
    */
    public function woohipaypro_cancel_payment(){
                         
        wp_redirect(esc_url_raw(get_permalink(get_option('woocommerce_checkout_page_id')).'?woohipaywalletpro=cancelled-payment'));

    }

    /**
    * Redirect to checkout page with error notice if payment ko
    */
    public function woohipaypro_payment_ko(){

        wp_redirect(esc_url_raw(get_permalink(get_option('woocommerce_checkout_page_id')).'?woohipaywalletpro=payment-ko'));

    }

    /**
    * Redirect to checkout page with loader while the system analyze Hipay response
    */
    public function woohipaypro_payment_treatment(){
        
        global $woocommerce;
        
        $order_id = isset($_GET['order']) && !empty($_GET['order']) ? intval($_GET['order']) : NULL;
        
        if($order_id != NULL){
            
            wp_redirect(esc_url_raw(get_permalink(get_option('woocommerce_checkout_page_id')).'?woohipaywalletpro=payment-treatment&order='.$order_id));
            
        }else{
            
            wp_redirect(esc_url_raw(get_permalink(get_option('woocommerce_checkout_page_id')).'?woohipaywalletpro=payment-ko'));
            
        }

    }
    
    /**
    * Add tokens to user if payment ok and tokens must be added
    */
    public function woohipaypro_credit_user($order_id, $type, $credits, $meta, $label){
        
        global $wp;
        
        global $wpdb;
        
        global $woocommerce;
        
        $order      = wc_get_order($order_id);
        
        $items      = $order->get_items();
        
        $user_id    = $order->get_user_id();
        
        foreach($items as $item){
            
            $item_id    = $item->get_id();
            
            $post_id    = wc_get_order_item_meta($item_id, '_product_id');

            $allow_hipaysubscription = get_post_meta($post_id, 'allow_hipaysubscription');
            
            $hipay_is_subscription   = $allow_hipaysubscription[0] == 'yes' ? 1 : 0;

            $hipaysubscription_data  = get_post_meta($post_id, 'hipaysubscription_data');
                        
            if($type == 'oneshot'){
                
                                
                if($hipaysubscription_data[0]['cl_tokens_hipaysubscription'] != '' && intval($hipaysubscription_data[0]['cl_tokens_hipaysubscription']) > 0 && $hipaysubscription_data[0]['cycle_hipaysubscription'] != ''){

                    $tokens = get_user_meta($user_id, $hipaysubscription_data[0]['cycle_hipaysubscription'], true);
                    
                    if($tokens){
                        
                        update_user_meta($user_id, $hipaysubscription_data[0]['cycle_hipaysubscription'], intval($tokens) + intval($hipaysubscription_data[0]['cl_tokens_hipaysubscription']));
                        
                    }else{
                        
                        update_user_meta($user_id, $hipaysubscription_data[0]['cycle_hipaysubscription'], intval($hipaysubscription_data[0]['cl_tokens_hipaysubscription']));
                        
                    }
                    
                }
                
                woohipaypro_manage_options($hipaysubscription_data[0]['cycle_hipaysubscription'],$hipaysubscription_data[0]['credits_label_hipaysubscription']);
                
            }else if($type == 'subscription'){
                                
                if($credits != '' && intval($credits) > 0 && $meta != ''){
                    
                    $tokens = get_user_meta($user_id, $meta, true);

                    if($tokens){
                        
                        update_user_meta($user_id, $meta, intval($tokens) + intval($credits));
                        
                    }else{
                        
                        update_user_meta($user_id, $meta, intval($credits));
                        
                    }
                    
                }
                
                woohipaypro_manage_options($meta,$label);
                
            }
        
        }
        
    }
    
}