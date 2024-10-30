<?php
/**
 * Hipay Wallet Payment Gateway
 *
 * Provides a Hipay Wallet Payment Gateway.
 *
 * @class       WC_Hipay_Gateway_Pro
 * @extends     WC_Payment_Gateway
 * @version     1.0.0
 * @package     WooCommerce/Classes/Payment
 */

if(!defined('ABSPATH')){ exit; }

class WC_Hipay_Gateway_Pro extends WC_Payment_Gateway {
    
    /**
    * Constructor for the gateway.
    */
    public function __construct(){
        
        $this->id                   = 'woohipaypro';
        $this->icon                 = '';
        $this->has_fields           = false;
        $this->method_title         = 'HiPay Wallet payment gateway pro';
        $this->method_description   = esc_html__('Autoriser à partir de votre boutique WooCommerce les paiements simples et les paiements récurrents via la plateforme de paiement en ligne HiPay Wallet. HiPay Wallet est l\'une des solutions de paiement en ligne les plus sécurisées.','woohipaypro');

        $this->supports = array(
            'products',
            'subscriptions',
            'subscription_cancellation'
            /*
            'subscription_suspension',
            'subscription_reactivation',
            'subscription_amount_changes',
            'subscription_date_changes',
            'subscription_payment_method_change',
            'subscription_payment_method_change_customer',
            'subscription_payment_method_change_admin',
            'multiple_subscriptions'
             */
        );
        
        $this->init_form_fields();
            
        $this->init_settings();
        $this->title            = $this->get_option('title');
        $this->description      = $this->get_option('description');
        $this->enabled          = $this->get_option('enabled');
        $this->testmode         = 'yes' === $this->get_option('testmode');
        $this->private_key      = $this->testmode ? $this->get_option('test_private_key') : $this->get_option('private_key');
        $this->publishable_key  = $this->testmode ? $this->get_option('test_publishable_key') : $this->get_option('publishable_key');
        
        $this->woohipaypro      = isset($_GET['woohipaywalletpro']) && !empty($_GET['woohipaywalletpro']) ? sanitize_text_field($_GET['woohipaywalletpro']) : NULL;

        $this->woohipayorder    = isset($_GET['order']) && !empty($_GET['order']) && is_numeric($_GET['order']) ? intval($_GET['order']) : NULL;
                
        add_action('wp_enqueue_scripts', array($this, 'payment_scripts'));
        
        if(is_admin()){

            add_action('woocommerce_update_options_payment_gateways_'.$this->id, array($this, 'process_admin_options'));

        }
        
        include_once WOOHIPAY_ROOT_PATH.'/includes/lib/wc-hipay-gateway-pro-response.php';
        
        new WC_Hipay_Gateway_Pro_Response($this);
        
        if(is_checkout() && $this->woohipaypro == 'cancelled-payment'){
                
            wc_clear_notices();

            wc_add_notice(esc_html__('Vous avez annulé la transaction de votre commande. S\'il s\'agit d\'une erreur vous pouvez à nouveau procéder au paiement de votre commande.','woohipaypro'),'error');

            return;

        }else if(is_checkout() && $this->woohipaypro == 'payment-ko'){

            wc_clear_notices();

            wc_add_notice(esc_html__('Votre paiement n\'a pas été effectué et votre commande n\'a pas aboutie, car un problème est survenu lors du paiement sur la plateforme HiPay Wallet : il peut s\'agir d\'un manque de provisions sur votre compte bancaire ou d\'une restriction liée à votre compte bancaire.','woohipaypro'),'error');

            return;

        }else if(is_checkout() && $this->woohipaypro == 'payment-treatment'){

            wc_clear_notices();
            
            if($this->woohipayorder != NULL){
                
                add_action('woocommerce_before_checkout_form', array($this, 'woohipaypro_order_status_loader'));
                                
                add_action('wp_footer', array($this, 'woohipaypro_treat_payment_data'));
                
            }else{
                
                wc_add_notice(esc_html__('une erreur est survenue : l\'ID de vore commande n\'a pu être récupéré et le statut de votre commande est inconnu...','woohipaypro'),'error');
                
                return;
                
            }

        }
                
    }
    
    /**
    * Init form fields
    */
    public function init_form_fields(){
            
        $this->form_fields = array(
            'enabled' => array(
                'title'       => esc_html__('Activer/désactiver','woohipaypro'),
                'label'       => esc_html__('Activer HiPay Wallet Payment Gateway Pro','woohipaypro'),
                'type'        => 'checkbox',
                'description' => '',
                'default'     => 'no'
            ),
            'title' => array(
                'title'       => esc_html__('Titre','woohipaypro'),
                'type'        => 'text',
                'description' => esc_html__('Ce champ gère le titre que l\'utilisateur verra durant le processus de paiement. ','woohipaypro'),
                'default'     => esc_html__('Carte de paiement via Hipay Wallet','woohipaypro'),
                'desc_tip'    => true,
                'placeholder' => esc_html__('Carte de paiement','woohipaypro')
            ),
            'description' => array(
                'title'       => esc_html__('Description','woohipaypro'),
                'type'        => 'textarea',
                'description' => esc_html__('Ce champ gère la description que l\'utilisateur verra durant le processus de paiement. ','woohipaypro'),
                'desc_tip'    => true,
                'default'     => '<img src="'.WOOHIPAY_ROOT_URL.'assets/img/woocommerce-hipaywallet-pro-payment-gateway.jpg" alt="HiPay Wallet Payment Gateway"/><br/><br/>'.__('Payer en toute sécurité avec','woohipaypro').' <a href="https://www.hipaywallet.com" target="_blank" title="HiPay Wallet Payment Gateway">HiPay Wallet</a>.',
                'placeholder' => esc_html__('Description que le client verra...','woohipaypro')
            ),
            'testmode' => array(
                'title'       => esc_html__('Mode test','woohipaypro'),
                'label'       => esc_html__('Activer le mode test','woohipaypro'),
                'type'        => 'checkbox',
                'description' => esc_html__('Cochez pour activer le mode test, sinon le mode production est activé.','woohipaypro'),
                'default'     => 'yes',
                'desc_tip'    => true,
            ),
            'test_publishable_key' => array(
                'title'       => esc_html__('Login API - mode test','woohipaypro'),
                'type'        => 'text',
                'description' => esc_html__('Votre login API pour l\'environnement de TEST.','woohipaypro'),
                'desc_tip'    => true
            ),
            'test_private_key' => array(
                'title'       => esc_html__('Mot de passe API - mode test','woohipaypro'),
                'type'        => 'password',
                'description' => esc_html__('Votre mot de passe API pour l\'environnement de TEST.','woohipaypro'),
                'desc_tip'    => true
            ),
            'test_private_rsa_key' => array(
                'title'       => esc_html__('Clé RSA privée - mode test','woohipaypro'),
                'type'        => 'textarea',
                'description' => esc_html__('Votre clé RSA privée pour l\'environnement de TEST.','woohipaypro'),
                'desc_tip'    => true
            ),
            'publishable_key' => array(
                'title'       => esc_html__('Login API - mode production','woohipaypro'),
                'type'        => 'text',
                'description' => esc_html__('Votre login API pour l\'environnement de PRODUCTION.','woohipaypro'),
                'desc_tip'    => true
            ),
            'private_key' => array(
                'title'       => esc_html__('Mot de passe API - mode production','woohipaypro'),
                'type'        => 'password',
                'description' => esc_html__('Votre mot de passe API pour l\'environnement de PRODUCTION.','woohipaypro'),
                'desc_tip'    => true
            ),
            'private_rsa_key' => array(
                'title'       => esc_html__('Clé RSA privée - mode production','woohipaypro'),
                'type'        => 'textarea',
                'description' => esc_html__('Votre clé RSA privée pour l\'environnement de PRODUCTION.','woohipaypro'),
                'desc_tip'    => true
            ),
            'logo' => array(
                'title'       => esc_html__('Logo du formulaire HiPay Wallet','woohipaypro'),
                'type'        => 'text',
                'description' => esc_html__('Votre logo sur le formulaire de paiement HiPay Wallet (512x512 pixels), formats .jpg, .jpeg, .gif ou .png.', 'woohipaypro'),
                'default'     => esc_html__('','woohipaypro'),
                'desc_tip'    => true,
                'placeholder' => esc_url('https://www.domain.tld/logo.jpg')
            ),
            'age' => array(
                'title'       => esc_html__('Age minimum des clients','woohipaypro'),
                'type'        => 'text',
                'description' => esc_html__('Définissez l\'âge minimum des clients : 12, 16, 18, ALL.', 'woohipaypro'),
                'default'     => esc_html__('','woohipaypro'),
                'desc_tip'    => true,
                'placeholder' => esc_html__('18','woohipaypro')
            ),
            'websiteid' => array(
                'title'       => esc_html__('ID du site Web','woohipaypro'),
                'type'        => 'number',
                'description' => esc_html__('ID du site Web sur votre compte HiPay Wallet.', 'woohipaypro'),
                'desc_tip'    => true,
            ),
            'categoryid' => array(
                'title'       => esc_html__('ID de la catégorie','woohipaypro'),
                'type'        => 'number',
                'description' => esc_html__('ID de la catégorie du site Web sur votre compte HiPay Wallet.', 'woohipaypro'),
                'desc_tip'    => true,
            ),
            'mainaccount' => array(
                'title'       => esc_html__('ID du compte principal','woohipaypro'),
                'type'        => 'number',
                'description' => esc_html__('ID du compte principal, compte HiPay Wallet sur lequel est configuré le site Internet.', 'woohipaypro'),
                'desc_tip'    => true,
            ),
            'affiliate_one' => array(
                'title'       => esc_html__('ID du compte affilié N°1','woohipaypro'),
                'type'        => 'number',
                'description' => esc_html__('ID du compte affilié N°1, pour la perception des commissions sur les ventes.', 'woohipaypro'),
                'desc_tip'    => true
            ),
            'affiliate_two' => array(
                'title'       => esc_html__('ID du compte affilié N°2','woohipaypro'),
                'type'        => 'number',
                'description' => esc_html__('ID du compte affilié N°2, pour la perception des commissions sur les ventes.', 'woohipaypro'),
                'desc_tip'    => true
            ),
            'affiliate_three' => array(
                'title'       => esc_html__('ID du compte affilié N°3','woohipaypro'),
                'type'        => 'number',
                'description' => esc_html__('ID du compte affilié N°3, pour la perception des commissions sur les ventes.', 'woohipaypro'),
                'desc_tip'    => true
            ),
            'affiliate_four' => array(
                'title'       => esc_html__('ID du compte affilié N°4','woohipaypro'),
                'type'        => 'number',
                'description' => esc_html__('ID du compte affilié N°4, pour la perception des commissions sur les ventes.', 'woohipaypro'),
                'desc_tip'    => true
            ),
            'unsubscribe' => array(
                'title'       => esc_html__('Utilisateurs et désabonnement','woohipaypro'),
                'label'       => esc_html__('Permettre aux utilisateurs de se désabonner','woohipaypro'),
                'type'        => 'checkbox',
                'description' => esc_html__('Cocher la case pour permettre aux utiisateurs qui souscrivent un abonnement de se désabonner eux-mêmes.','woohipaypro'),
                'desc_tip'    => true,
                'default'     => 'yes'
            )
        );

    }
    
    /**
    * Load css and js scripts
    */
    public function payment_scripts(){
        
        if(!is_cart() && !is_checkout()){
            
            return;
            
        }else{
            
            wp_enqueue_style('woocommerce_woohipaypro_css',WOOHIPAY_ROOT_URL.'assets/css/woohipaypro.css',array(),WOOHIPAY_PLUGIN_VERSION,'all');
            
            wp_register_script('woocommerce_woohipaypro_js', WOOHIPAY_ROOT_URL.'assets/js/woohipaypro.js', array('jquery'), WOOHIPAY_PLUGIN_VERSION, true);
            
            wp_localize_script('woocommerce_woohipaypro_js','woohipayproajax', array(
                'ajax_url'      => admin_url('admin-ajax.php'),
                'site_url'      => site_url(),
                'ajax_nonce'    => wp_create_nonce('woohipayproajax')
            ));

            wp_enqueue_script('woocommerce_woohipaypro_js');
                        
        }
        
    }
    
    /**
    * Process the payment and return the result
    */
    public function process_payment($order_id){
            
        include_once WOOHIPAY_ROOT_PATH.'/includes/lib/wc-hipay-gateway-pro-request.php';
                                
        $order = wc_get_order($order_id);
                
        $hipay_request = new WC_Hipay_Gateway_Pro_Request($this);
                        
        return array(
            'result'   => 'success',
            'redirect' => $hipay_request->get_request_url($order, $order_id)
        );

    }
    
    /**
     * Add custom html to display loader on page when payment is waiting response
     */
    public function woohipaypro_order_status_loader(){
        
    ?>
<div id="woohipaypro-loader">
    <h3><?php esc_html_e('Traitement du paiement','woohipaypro'); ?></h3>
    <img src="<?php echo WOOHIPAY_ROOT_URL.'assets/img/cog.svg'; ?>" alt="cog" id="woohipaypro-cog"/>
    <p><?php esc_html_e('Analyse de la réception du paiement depuis Hipay Wallet...','woohipaypro'); ?></p>
</div>
    <?php
    
    }
        
    /**
    * Call the function to get the callback from database to check if payment capture is ok
    */
    public function woohipaypro_treat_payment_data(){
                
        $callback_status = $this->woohipaypro_get_payment_status();
                
        if($callback_status['url'] != ''){
            
            wp_redirect($callback_status['url']);
            
        }
        
    }
    
    /**
    * Get data from database to check if payment capture is ok
    */
    public function woohipaypro_get_payment_status(){
        
        global $wp;
        
        global $wpdb;
        
        global $woocommerce;
        
        $order      = wc_get_order($this->woohipayorder);
        
        $items      = $order->get_items();
        
        foreach($items as $item){
            
            $item_id    = $item->get_id();
            
            $post_id    = wc_get_order_item_meta($item_id, '_product_id');
            
            $operation  = get_post_meta($this->woohipayorder, 'woohipaypro_operation',true);
            
            $status     = get_post_meta($this->woohipayorder, 'woohipaypro_status',true);
            
            break;
            
        }
        
        if($operation == "authorization" && $status == "nok"){

            // Authorization failed ==> Payment failed
            $redirect_url = esc_url_raw(get_permalink(get_option('woocommerce_checkout_page_id')).'?woohipaywalletpro=payment-ko');
            
            $payment_status = array(
                'time'          => date('H:i:s'),
                'operation'     => $operation,
                'status'        => $status,
                'url'           => ''
            );
            
            return $payment_status;

        }else if($operation == "capture" && $status == "nok"){

            // Capture failed ==> Payment failed
            $redirect_url = esc_url_raw(get_permalink(get_option('woocommerce_checkout_page_id')).'?woohipaywalletpro=payment-ko');
            
            $payment_status = array(
                'time'          => date('H:i:s'),
                'operation'     => $operation,
                'status'        => $status,
                'url'           => ''
            );
            
            return $payment_status;

        }else if($operation == "authorization" && $status == "ok"){
            
            // First step OK, waiting for page reloading...
            $payment_status = array(
                'time'          => date('H:i:s'),
                'operation'     => $operation,
                'status'        => $status,
                'url'           => ''
            );
            
            return $payment_status;

        }else if($operation == "capture" && $status == "ok"){

            // Capture OK ==> Payment is accepted by Hipay
            $redirect_url = esc_url_raw($this->get_return_url($order));
            
            $payment_status = array(
                'time'          => date('H:i:s'),
                'operation'     => $operation,
                'status'        => $status,
                'url'           => $redirect_url
            );
            
            return $payment_status;

        }else{
            
            // Unknown status ==> write a note with details
            $redirect_url = esc_url_raw(get_permalink(get_option('woocommerce_checkout_page_id')).'?woohipaywalletpro=payment-ko');
            
            $payment_status = array(
                'time'          => date('H:i:s'),
                'operation'     => $operation,
                'status'        => $status,
                'url'           => $redirect_url
            );
            
            return $payment_status;
            
        }
                        
    }
    
}