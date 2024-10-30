<?php

/**
 * Array for locale data
 */
global $en;

$en = array('en_EN','en_US');

date_default_timezone_set(date_default_timezone_get());

/**
 * Enqueue Woohipaypro CSS & JS scripts in back end
 */
function woohipaypro_scripts($hook){
            
    $woohip_pages = array(
        'post.php',
        'post-new.php',
        'edit.php'
    );
    
    if(in_array($hook,$woohip_pages)){
        
        wp_enqueue_style('woohip_css',WOOHIPAY_ROOT_URL.'assets/css/woohipay.css',array(),WOOHIPAY_PLUGIN_VERSION,'all');
        
        wp_register_script('woohip_js',WOOHIPAY_ROOT_URL.'assets/js/woohipay.js',array('jquery'),WOOHIPAY_PLUGIN_VERSION,true);
        wp_enqueue_script('woohip_js');
        
    }
    
}

/**
 * Enqueue Woohipaypro CSS & JS scripts in back end
 */
add_action('admin_enqueue_scripts','woohipaypro_scripts');

/**
 * Enqueue Woohipaypro CSS & JS scripts in front end
 */
function woohipaypro_front_scripts($hook){
    
    wp_register_style('woohipaypro_glob_css',WOOHIPAY_ROOT_URL.'assets/css/woohipaypro-global.css',array(),WOOHIPAY_PLUGIN_VERSION,'all');
    wp_enqueue_style('woohipaypro_glob_css',WOOHIPAY_ROOT_URL.'assets/css/woohipaypro-global.css');

    wp_register_script('woohipaypro_glob_js',WOOHIPAY_ROOT_URL.'assets/js/woohipaypro-global.js',array('jquery'),WOOHIPAY_PLUGIN_VERSION,true);
    wp_enqueue_script('woohipaypro_glob_js');
    wp_localize_script('woohipaypro_glob_js','woohipapro_ajax', array('ajax_url' => admin_url('admin-ajax.php')));
    
}

add_action('wp_enqueue_scripts','woohipaypro_front_scripts');

/**
 * Manage tabs in product edition
 */
function woohipaypro_hipaysubscription_tab($tabs){
  
    $tabs['custom_tab'] = array(
        'label'  => __('HiPay abonnement', 'woohipaypro'),
        'target' => 'hipaysubscription_panel',
        'class'  => array(),
    );

    return $tabs;
  
}

/**
 * Manage tabs in product edition
 */
add_filter('woocommerce_product_data_tabs', 'woohipaypro_hipaysubscription_tab');

/**
 * Manage data in product edition
 */
function woohipaypro_hipaysubscription_panel(){
        
    global $post;
    
    $woohipaypro_settings = get_option('woocommerce_woohipaypro_settings', true);
        
    $allow_hipaysubscription = get_post_meta($post->ID, 'allow_hipaysubscription');
    
    $hipaysubscription_data  = get_post_meta($post->ID, 'hipaysubscription_data');
    
?>
    <div id="hipaysubscription_panel" class="panel woocommerce_options_panel">
        <div class="options_group">
            <?php  
            
            $allow_hipaysubscription_value          = $allow_hipaysubscription[0] == 'yes' ? '' : 'false';
            
            $hipaysubscription_data_type            = $hipaysubscription_data[0]['type_hipaysubscription'];
            
            $hipaysubscription_data_cu_int          = $hipaysubscription_data[0]['cu_freq_hipaysubscription'];
            
            $hipaysubscription_data_cu_tokens       = $hipaysubscription_data[0]['cu_tokens_hipaysubscription'];
            
            $hipaysubscription_data_cu_label        = $hipaysubscription_data[0]['cu_label_hipaysubscription'];
            
            $hipaysubscription_data_cu_price        = $hipaysubscription_data[0]['cu_price_hipaysubscription'];
            
            $hipaysubscription_data_cl_freq         = $hipaysubscription_data[0]['cl_freq_hipaysubscription'];
            
            $hipaysubscription_data_cl_tokens       = $hipaysubscription_data[0]['cl_tokens_hipaysubscription'];
            
            $hipaysubscription_data_cl_label        = $hipaysubscription_data[0]['cl_label_hipaysubscription'];
                        
            $hipaysubscription_data_cycle           = $hipaysubscription_data[0]['cycle_hipaysubscription'];
            
            $hipaysubscription_data_credits_label   = $hipaysubscription_data[0]['credits_label_hipaysubscription'];
            
            $hipaysubscription_data_affiliates      = $hipaysubscription_data[0]['affiliates_hipaysubscription'];
            
            $hipaysubscription_data_aff_one         = $hipaysubscription_data[0]['aff_one_hipaysubscription'];
            
            $hipaysubscription_data_aff_two         = $hipaysubscription_data[0]['aff_two_hipaysubscription'];
            
            $hipaysubscription_data_aff_three       = $hipaysubscription_data[0]['aff_three_hipaysubscription'];
            
            $hipaysubscription_data_aff_four        = $hipaysubscription_data[0]['aff_four_hipaysubscription'];
                        
            $args = array(
                'label' => esc_html__('Paiements récurrents ?', 'woohipaypro'),
                'class' => '',
                'style' => '',
                'wrapper_class' => '',
                'value' => esc_html($allow_hipaysubscription_value),
                'id' => 'allow_hipaysubscription',
                'name' => 'allow_hipaysubscription',
                'cbvalue' => '',
                'desc_tip' => false,
                'custom_attributes' => '',
                'description' => esc_html__('Activer les paiements récurrents pour ce produit/service.', 'woohipaypro')
            );
            
            woocommerce_wp_checkbox($args);
            
            $args = array(
                'label' => esc_html__('Type d\'abonnement ?', 'woohipaypro'),
                'class' => 'select short',
                'style' => '',
                'wrapper_class' => '',
                'value' => esc_html($hipaysubscription_data_type),
                'id' => 'type_hipaysubscription',
                'name' => 'type_hipaysubscription',
                'options' => [esc_html__('Abonnement régulier', 'woohipaypro'),esc_html__('Abonnement personnalisé', 'woohipaypro')],
                'desc_tip' => false,
                'custom_attributes' => '',
                'description' => esc_html__('Choisir le type d\'abonnement pour ce produit/service.', 'woohipaypro')
            );
            
            woocommerce_wp_select($args);
            
            $args = array(
                'label' => esc_html__('1er Intervalle', 'woohipaypro'),
                'placeholder' => '',
                'class' => 'short woohipayhidden',
                'style' => '',
                'wrapper_class' => '',
                'value' => esc_html($hipaysubscription_data_cu_int),
                'id' => 'cu_freq_hipaysubscription',
                'name' => 'cu_freq_hipaysubscription',
                'type' => '',
                'desc_tip' => false,
                'data_type' => '',
                'custom_attributes' => '',
                'description' => __('Intervalle entre le 1er et le 2e paiement (nombre de jour(s)).', 'woohipaypro')
            );
            
            woocommerce_wp_text_input($args);
            
            $args = array(
                'label' => esc_html__('Nombre de jetons', 'woohipaypro'),
                'placeholder' => '',
                'class' => 'short woohipayhidden',
                'style' => '',
                'wrapper_class' => '',
                'value' => esc_html($hipaysubscription_data_cu_tokens),
                'id' => 'cu_tokens_hipaysubscription',
                'name' => 'cu_tokens_hipaysubscription',
                'type' => '',
                'desc_tip' => false,
                'data_type' => '',
                'custom_attributes' => '',
                'description' => esc_html__('Nombre de jetons crédités au 1er paiement (si le produit/service nécessite des jetons).', 'woohipaypro')
            );
            
            woocommerce_wp_text_input($args);
            
            $args = array(
                'label' => esc_html__('Libellé du produit/service', 'woohipaypro'),
                'placeholder' => esc_html__('Ex.: "Offre découverte du service : 100 jetons"', 'woohipaypro'),
                'class' => 'short woohipayhidden',
                'style' => '',
                'wrapper_class' => '',
                'value' => esc_html($hipaysubscription_data_cu_label),
                'id' => 'cu_label_hipaysubscription',
                'name' => 'cu_label_hipaysubscription',
                'type' => '',
                'desc_tip' => false,
                'data_type' => '',
                'custom_attributes' => '',
                'description' => esc_html__('Libellé du produit/service pour le 1er paiement.', 'woohipaypro')
            );
            
            woocommerce_wp_textarea_input($args);
            
            $args = array(
                'label' => esc_html__('Montant dès 2e paiement', 'woohipaypro'),
                'placeholder' => '',
                'class' => 'short woohipayhidden',
                'style' => '',
                'wrapper_class' => '',
                'value' => esc_html($hipaysubscription_data_cu_price),
                'id' => 'cu_price_hipaysubscription',
                'name' => 'cu_price_hipaysubscription',
                'type' => '',
                'desc_tip' => false,
                'data_type' => '',
                'custom_attributes' => '',
                'description' => esc_html__('Montant à payer dès le 2e paiement (montant du 1er paiement : onglet "Général").', 'woohipaypro')
            );
            
            woocommerce_wp_text_input($args);
            
            $args = array(
                'label' => esc_html__('Intervalle des paiements', 'woohipaypro'),
                'placeholder' => '',
                'class' => 'short',
                'style' => '',
                'wrapper_class' => '',
                'value' => esc_html($hipaysubscription_data_cl_freq),
                'id' => 'cl_freq_hipaysubscription',
                'name' => 'cl_freq_hipaysubscription',
                'type' => '',
                'desc_tip' => false,
                'data_type' => '',
                'custom_attributes' => '',
                'description' => esc_html__('Intervalle entre les paiements (nombre de jour(s)), dès le 2e paiement.', 'woohipaypro')
            );
            
            woocommerce_wp_text_input($args);
            
            $args = array(
                'label' => esc_html__('Nombre de jetons', 'woohipaypro'),
                'placeholder' => '',
                'class' => 'short',
                'style' => '',
                'wrapper_class' => '',
                'value' => esc_html($hipaysubscription_data_cl_tokens),
                'id' => 'cl_tokens_hipaysubscription',
                'name' => 'cl_tokens_hipaysubscription',
                'type' => '',
                'desc_tip' => false,
                'data_type' => '',
                'custom_attributes' => '',
                'description' => esc_html__('Nombre de jetons crédités à chaque paiement (si le produit/service nécessite des jetons), cette variable, si renseignée, crédite aussi l\'utilisateur lors des paiements uniques.', 'woohipaypro')
            );
            
            woocommerce_wp_text_input($args);
            
            $args = array(
                'label' => esc_html__('Libellé du produit/service', 'woohipaypro'),
                'placeholder' => esc_html__('Ex.: "1000 jetons pour utiliser le service"', 'woohipaypro'),
                'class' => 'short woohipayhidden',
                'style' => '',
                'wrapper_class' => '',
                'value' => esc_html($hipaysubscription_data_cl_label),
                'id' => 'cl_label_hipaysubscription',
                'name' => 'cl_label_hipaysubscription',
                'type' => '',
                'desc_tip' => false,
                'data_type' => '',
                'custom_attributes' => '',
                'description' => esc_html__('Libellé du produit/service à partir du 2e paiement.', 'woohipaypro')
            );
            
            woocommerce_wp_textarea_input($args);
            
            $args = array(
                'label' => esc_html__('Variable database', 'woohipaypro'),
                'placeholdeesc_htmlr' => 'ex.: "my_custom_product"',
                'class' => 'short',
                'style' => '',
                'wrapper_class' => '',
                'value' => esc_html($hipaysubscription_data_cycle),
                'id' => 'cycle_hipaysubscription',
                'name' => 'cycle_hipaysubscription',
                'type' => '',
                'desc_tip' => false,
                'data_type' => '',
                'custom_attributes' => '',
                'description' => esc_html__('Variable utilisée pour stocker les jetons de l\'utilisateur en base de données.', 'woohipaypro')
            );
            
            woocommerce_wp_text_input($args);
            
            $args = array(
                'label' => esc_html__('Libellé des jetons', 'woohipaypro'),
                'placeholder' => 'ex.: "token|tokens"',
                'class' => 'short',
                'style' => '',
                'wrapper_class' => '',
                'value' => esc_html($hipaysubscription_data_credits_label),
                'id' => 'credits_label_hipaysubscription',
                'name' => 'credits_label_hipaysubscription',
                'type' => '',
                'desc_tip' => false,
                'data_type' => '',
                'custom_attributes' => '',
                'description' => esc_html__('Variable utilisée pour déterminer le libellé des jetons/crédits en front-office : valeur au singulier | valeur au pluriel.', 'woohipaypro')
            );
            
            woocommerce_wp_text_input($args);
            
            $args = array(
                'label' => esc_html__('Commission aux affiliés ?', 'woohipaypro'),
                'class' => 'select short',
                'style' => '',
                'wrapper_class' => '',
                'value' => esc_html($hipaysubscription_data_affiliates),
                'id' => 'affiliates_hipaysubscription',
                'name' => 'affiliates_hipaysubscription',
                'options' => [esc_html__('Pas de commission', 'woohipaypro'),esc_html__('Commission aux affiliés', 'woohipaypro')],
                'desc_tip' => false,
                'custom_attributes' => '',
                'description' => esc_html__('Choisir si une commission doit être versée aux affiliés pour ce produit/service.', 'woohipaypro')
            );
            
            woocommerce_wp_select($args);
                        
            $args = array(
                'label' => esc_html(__('ID :', 'woohipaypro').' '.$woohipaypro_settings['affiliate_one']),
                'placeholder' => '',
                'class' => 'short woohipayhidden_aff',
                'style' => '',
                'wrapper_class' => '',
                'value' => esc_html($hipaysubscription_data_aff_one),
                'id' => 'aff_one_hipaysubscription',
                'name' => 'aff_one_hipaysubscription',
                'type' => '',
                'desc_tip' => false,
                'data_type' => '',
                'custom_attributes' => '',
                'description' => esc_html__('Pourcentage (%) du montant du produit/service qui sera reversé à l\'affilié N°1.', 'woohipaypro')
            );
            
            woocommerce_wp_text_input($args);
            
            $args = array(
                'label' => esc_html(__('ID :', 'woohipaypro').' '.$woohipaypro_settings['affiliate_two']),
                'placeholder' => '',
                'class' => 'short woohipayhidden_aff',
                'style' => '',
                'wrapper_class' => '',
                'value' => esc_html($hipaysubscription_data_aff_two),
                'id' => 'aff_two_hipaysubscription',
                'name' => 'aff_two_hipaysubscription',
                'type' => '',
                'desc_tip' => false,
                'data_type' => '',
                'custom_attributes' => '',
                'description' => esc_html__('Pourcentage (%) du montant du produit/service qui sera reversé à l\'affilié N°2.', 'woohipaypro')
            );
            
            woocommerce_wp_text_input($args);
            
            $args = array(
                'label' => esc_html(__('ID :', 'woohipaypro').' '.$woohipaypro_settings['affiliate_three']),
                'placeholder' => '',
                'class' => 'short woohipayhidden_aff',
                'style' => '',
                'wrapper_class' => '',
                'value' => esc_html($hipaysubscription_data_aff_three),
                'id' => 'aff_three_hipaysubscription',
                'name' => 'aff_three_hipaysubscription',
                'type' => '',
                'desc_tip' => false,
                'data_type' => '',
                'custom_attributes' => '',
                'description' => esc_html__('Pourcentage (%) du montant du produit/service qui sera reversé à l\'affilié N°3.', 'woohipaypro')
            );
            
            woocommerce_wp_text_input($args);
            
            $args = array(
                'label' => esc_html(__('ID :', 'woohipaypro').' '.$woohipaypro_settings['affiliate_four']),
                'placeholder' => '',
                'class' => 'short woohipayhidden_aff',
                'style' => '',
                'wrapper_class' => '',
                'value' => esc_html($hipaysubscription_data_aff_four),
                'id' => 'aff_four_hipaysubscription',
                'name' => 'aff_four_hipaysubscription',
                'type' => '',
                'desc_tip' => false,
                'data_type' => '',
                'custom_attributes' => '',
                'description' => esc_html__('Pourcentage (%) du montant du produit/service qui sera reversé à l\'affilié N°4.', 'woohipaypro')
            );
            
            woocommerce_wp_text_input($args);
            
            ?>
        </div>
    </div>
<?php
}

/**
 * Manage data in product edition
 */
add_action('woocommerce_product_data_panels', 'woohipaypro_hipaysubscription_panel');

/**
 * Save Woohipaypro data product
 */
function woohipaypro_save_hipaysubscription_fields($post_id){
        
    $allow_hipaysubscription = isset($_POST['allow_hipaysubscription']) ? 'yes' : 'no';
    update_post_meta($post_id, 'allow_hipaysubscription', $allow_hipaysubscription);
    
    $type_hipaysubscription             = isset($_POST['type_hipaysubscription']) ? intval($_POST['type_hipaysubscription']) : '';
    $cu_freq_hipaysubscription          = isset($_POST['cu_freq_hipaysubscription']) ? intval($_POST['cu_freq_hipaysubscription']) : '';
    $cu_tokens_hipaysubscription        = isset($_POST['cu_tokens_hipaysubscription']) ? intval($_POST['cu_tokens_hipaysubscription']) : '';
    $cu_label_hipaysubscription         = isset($_POST['cu_label_hipaysubscription']) ? sanitize_text_field($_POST['cu_label_hipaysubscription']) : '';
    $cu_price_hipaysubscription         = isset($_POST['cu_price_hipaysubscription']) ? floatval($_POST['cu_price_hipaysubscription']) : '';
    $cl_freq_hipaysubscription          = isset($_POST['cl_freq_hipaysubscription']) ? intval($_POST['cl_freq_hipaysubscription']) : '';
    $cl_tokens_hipaysubscription        = isset($_POST['cl_tokens_hipaysubscription']) ? intval($_POST['cl_tokens_hipaysubscription']) : '';
    $cl_label_hipaysubscription         = isset($_POST['cl_label_hipaysubscription']) ? sanitize_text_field($_POST['cl_label_hipaysubscription']) : '';
    $cycle_hipaysubscription            = isset($_POST['cycle_hipaysubscription']) ? sanitize_text_field($_POST['cycle_hipaysubscription']) : '';
    $credits_label_hipaysubscription    = isset($_POST['credits_label_hipaysubscription']) ? sanitize_text_field($_POST['credits_label_hipaysubscription']) : '';
    $affiliates_hipaysubscription       = isset($_POST['affiliates_hipaysubscription']) ? intval($_POST['affiliates_hipaysubscription']) : '';
    $aff_one_hipaysubscription          = isset($_POST['aff_one_hipaysubscription']) ? floatval($_POST['aff_one_hipaysubscription']) : '';
    $aff_two_hipaysubscription          = isset($_POST['aff_two_hipaysubscription']) ? floatval($_POST['aff_two_hipaysubscription']) : '';
    $aff_three_hipaysubscription        = isset($_POST['aff_three_hipaysubscription']) ? floatval($_POST['aff_three_hipaysubscription']) : '';
    $aff_four_hipaysubscription         = isset($_POST['aff_four_hipaysubscription']) ? floatval($_POST['aff_four_hipaysubscription']) : '';
    
    $hipaysubscription_data = array(
        'type_hipaysubscription'            => $type_hipaysubscription,
        'cu_freq_hipaysubscription'         => $cu_freq_hipaysubscription,
        'cu_tokens_hipaysubscription'       => $cu_tokens_hipaysubscription,
        'cu_label_hipaysubscription'        => $cu_label_hipaysubscription,
        'cu_price_hipaysubscription'        => $cu_price_hipaysubscription,
        'cl_freq_hipaysubscription'         => $cl_freq_hipaysubscription,
        'cl_tokens_hipaysubscription'       => $cl_tokens_hipaysubscription,
        'cl_label_hipaysubscription'        => $cl_label_hipaysubscription,
        'cycle_hipaysubscription'           => $cycle_hipaysubscription,
        'credits_label_hipaysubscription'   => $credits_label_hipaysubscription,
        'affiliates_hipaysubscription'      => $affiliates_hipaysubscription,
        'aff_one_hipaysubscription'         => $aff_one_hipaysubscription,
        'aff_two_hipaysubscription'         => $aff_two_hipaysubscription,
        'aff_three_hipaysubscription'       => $aff_three_hipaysubscription,
        'aff_four_hipaysubscription'        => $aff_four_hipaysubscription
    );
    
    update_post_meta($post_id, 'hipaysubscription_data', $hipaysubscription_data);
    
    woohipaypro_manage_options($cycle_hipaysubscription,$credits_label_hipaysubscription);
  
}

/**
 * Save Woohipaypro data product
 */
add_action('woocommerce_process_product_meta', 'woohipaypro_save_hipaysubscription_fields');

/**
 * Get Woohipaypro options: custom data to retrieve tokens label of products
 */
function woohipaypro_manage_options($opt,$val){
    
    global $wp;
    
    global $wpdb;
    
    if($opt != '' && $val != ''){
        
        $values = explode("|",$val);
    
        $options = get_option('woohipaypro_opts');

        if($options){

            $options[$opt] = array($values[0],$values[1]);
                
            update_option('woohipaypro_opts', $options);

        }else{

            $opts = array( $opt    => array($values[0],$values[1]) );

            add_option('woohipaypro_opts', $opts, '', 'yes');

        }
        
    }
        
}

/**
 * Add tokens link to woocommerce account page
 */
function woohipaypro_account_tokens_link($menu_links){
 
    $menu_links = array_slice($menu_links, 0, 4, true) 
    + array('woohipaypro-tokens' => esc_html__('Crédits/jetons', 'woohipaypro'))
    + array_slice($menu_links, 4, NULL, true);

    return $menu_links;
 
}

add_filter('woocommerce_account_menu_items', 'woohipaypro_account_tokens_link', 40);

/**
 * Add endpoint for tokens link on woocommerce account page
 */
function woohipaypro_account_tokens_link_endpoint(){
 
    add_rewrite_endpoint('woohipaypro-tokens', EP_PAGES);
 
}

add_action('init', 'woohipaypro_account_tokens_link_endpoint');

/**
 * Add content in tokens tab on woocommerce account page
 */
function woohipaypro_account_tokens_content(){
        
    global $wpdb;
    
    $options = get_option('woohipaypro_opts');
        
    $user_id = get_current_user_id();
    
    $total = 0;
    
    $tokens = '<table class="woocommerce-orders-table woocommerce-MyAccount-orders shop_table shop_table_responsive my_account_orders account-orders-table">
<thead>
    <tr>
        <th class="woocommerce-orders-table__header woocommerce-orders-table__header-tokens-label"><span class="nobr">'.esc_html__('Libellé des crédits/jetons', 'woohipaypro').'</span></th>
        <th class="woocommerce-orders-table__header woocommerce-orders-table__header-tokens"><span class="nobr">'.esc_html__('Crédits/jetons disponibles', 'woohipaypro').'</span></th>
    </tr>
</thead>
<tbody>
';
    
    foreach($options as $k => $v){
        
        $data = get_user_meta($user_id, $k, true);
        
        if($data){
            
            $total += intval($data);
            
            $tok = intval($data) > 1 ? woohipaypro_number_format($data).' <small><em>'.$v[1].'</em><small>' : $data.' <small><em>'.$v[0].'</em><small>';
            
            $tokens .= '<tr class="woocommerce-orders-table__row woocommerce-orders-table__row--status-completed order">
    <td class="woocommerce-orders-table__cell woocommerce-orders-table__cell-order-number" data-title="Woohipaypro tokens label">
        <div class="woohipaypro-token icon"></div> '.ucfirst($v[1]).'
    </td>
    <td class="woocommerce-orders-table__cell woocommerce-orders-table__cell-order-date" data-title="Woohipaypro tokens">
        '.$tok.'
    </td>
</tr>';
            
        }
        
    }
    
    $tokens .= '</tbody>
</table>';
    
    $notokens = '<div><p>'.esc_html__('Vous ne possédez actuellement aucun crédit/jeton...', 'woohipaypro').'</p></div>';
    
    echo '<h3>'.esc_html__('Crédits/jetons :', 'woohipaypro').'</h3>';
    
    if($total > 0){
        
        echo $tokens;
        
    }else{
        
        echo $notokens;
        
    }
     
}

add_action('woocommerce_account_woohipaypro-tokens_endpoint', 'woohipaypro_account_tokens_content');

/**
 * Add subscriptions link to woocommerce account page
 */
function woohipaypro_account_subscriptions_link($menu_links){
 
    $menu_links = array_slice($menu_links, 0, 4, true) 
    + array('woohipaypro-subscriptions' => esc_html__('Abonnements', 'woohipaypro'))
    + array_slice($menu_links, 4, NULL, true);

    return $menu_links;
 
}

add_filter('woocommerce_account_menu_items', 'woohipaypro_account_subscriptions_link', 40);

/**
 * Add endpoint for subscritions link on woocommerce account page
 */
function woohipaypro_account_subscriptions_link_endpoint(){
 
    add_rewrite_endpoint('woohipaypro-subscriptions', EP_PAGES);
 
}

add_action('init', 'woohipaypro_account_subscriptions_link_endpoint');

/**
 * Add content in subscriptions tab on woocommerce account page
 */
function woohipaypro_account_subscriptions_content(){
 
    global $wpdb;
    
    global $woocommerce;
    
    $woohipaypro_opts = get_option('woocommerce_woohipaypro_settings');
        
    $user_id = get_current_user_id();
    
    $subscriptions = get_user_meta($user_id,'woohipaypro_subscriptions',false);
    
    if($subscriptions){
        
        $sub = $subscriptions[0];
    
        $subs = '<table id="woohipaypro-user-subscriptions-orders" class="woocommerce-orders-table woocommerce-MyAccount-orders shop_table shop_table_responsive my_account_orders account-orders-table">
<thead>
    <tr>
        <th class="woocommerce-orders-table__header woocommerce-orders-table__header-subs-id"><span class="nobr">'.esc_html__('ID de l\'abonnement', 'woohipaypro').'</span></th>
        <th class="woocommerce-orders-table__header woocommerce-orders-table__header-subs-order"><span class="nobr">'.esc_html__('N° de commande', 'woohipaypro').'</span></th>
        <th class="woocommerce-orders-table__header woocommerce-orders-table__header-subs-stop"><span class="nobr">'.esc_html__('Statut de l\'abonnement', 'woohipaypro').'</span></th>
    </tr>
</thead>
<tbody>
';

        foreach($sub as $k => $v){

            $results     = $wpdb->get_results("select * from $wpdb->postmeta where meta_value = '".$k."' ORDER BY meta_id ASC", ARRAY_A);
            
            $unsubscribe = $woohipaypro_opts['unsubscribe'] == 'yes' ? ' / <small><a href="javascript:void(0);" id="stop_'.$k.'" class="woohipaypro-stop-formula" data-label="'.esc_html__('Vous êtes sur le point de stopper votre abonnement ID :', 'woohipaypro').'" data-warning="'.esc_html__('Cette action est irréversible. Pour stopper définitivement votre abonnement, cliquez sur le bouton suivant :', 'woohipaypro').'" data-confirm="'.esc_html__('Votre abonnement a bien été stoppé.', 'woohipaypro').'" data-error="'.esc_html__('Une erreur est survenue : votre abonnement n\'a pu être stoppé. Pour stopper votre abonnement, veuillez contacter l\'administrateur du site.', 'woohipaypro').'" data-cancel="'.esc_html__('Annuler','woohipaypro').'" data-stop="'.esc_html__('STOPPER','woohipaypro').'" data-cog="'.WOOHIPAY_ROOT_URL.'assets/img/cog.svg" data-res="'.esc_html__('Résiliation en cours : ne fermez pas la fenêtre.','woohipaypro').'">'.esc_html__('Stopper', 'woohipaypro').'</a></small>' : '';
            
            $post_data   = $wpdb->get_results("select * from $wpdb->postmeta where meta_key = 'woohipaypro_subid' AND meta_value = '".$k."'", ARRAY_A);
            
            $status      = $wpdb->get_results("select * from $wpdb->postmeta where post_id = '".$post_data[0]['post_id']."' AND meta_key = 'woohipaypro_subscription_status'", ARRAY_A);
            
            $sub_status  = $status[0]['meta_value'] == 'stopped' ? esc_html__('Stoppé', 'woohipaypro') : esc_html__('Actif', 'woohipaypro').$unsubscribe ;
            
            $subs .= '<tr class="woocommerce-orders-table__row woocommerce-orders-table__row--status-completed order">
    <td class="woocommerce-orders-table__cell woocommerce-orders-table__cell-order-number" data-title="Woohipaypro subscriptions ID">
        <a href="javascript:void(0);" id="sub_'.$k.'" class="woohipaypro-subscription-id">'.$k.'</a>
    </td>
    <td class="woocommerce-orders-table__cell woocommerce-orders-table__cell-order-date" data-title="Woohipaypro subscriptions order">
        <a href="'.esc_url(get_permalink(get_option('woocommerce_myaccount_page_id'))).'view-order/'.$v.'/" target="_blank">'.esc_html__('n°', 'woohipaypro').' '.$v.'</a>
    </td>
    <td id="formula_'.$k.'" class="woocommerce-orders-table__cell woocommerce-orders-table__cell-order-date woohipaypro-k-cell" data-title="Woohipaypro subscriptions stop" data-subid="'.$k.'" data-inactive="'.esc_html__('Stoppé','woohipaypro').'">
        '.$sub_status.'
    </td>
</tr>';
            
             if(count($results) > 0){
                 
                 $inc = 2;
                 
                 for($i=0; $i<count($results); $i++){
                     
                     if($results[$i]['post_id'] != $v){
                         
                         $subs .= '<tr class="woocommerce-orders-table__row woocommerce-orders-table__row--status-completed order woohipaypro-hidden-tr" data-sub="'.$k.'">
    <td class="woocommerce-orders-table__cell woocommerce-orders-table__cell-order-number" data-title="Woohipaypro subscriptions ID">
        '.$k.'
    </td>
    <td class="woocommerce-orders-table__cell woocommerce-orders-table__cell-order-date" data-title="Woohipaypro subscriptions order">
        <a href="'.esc_url(get_permalink(get_option('woocommerce_myaccount_page_id'))).'view-order/'.$results[$i]['post_id'].'/" target="_blank">'.esc_html__('n°', 'woohipaypro').' '.$results[$i]['post_id'].'</a>
    </td>
    <td class="woocommerce-orders-table__cell woocommerce-orders-table__cell-order-date" data-title="Woohipaypro subscriptions stop">
        <small><em>'.esc_html__('Paiement n°', 'woohipaypro').' '.$inc.'</em></small>
    </td>
</tr>';
                         $inc++;
                         
                     }
                     
                 }
                 
             }

        }

        $subs .= '</tbody>
</table>';
        
    }
        
    $nosubs = '<div><p>'.esc_html__('Vous n\'avez actuellement aucun abonnement...', 'woohipaypro').'</p></div>';
    
    echo '<h3>'.esc_html__('Abonnements :', 'woohipaypro').'</h3>';
    
    if($subscriptions){
        
        echo $subs;
        
    }else{
        
        echo $nosubs;
        
    }
        
}

add_action('woocommerce_account_woohipaypro-subscriptions_endpoint', 'woohipaypro_account_subscriptions_content');

/**
 * Get locale to number format correctly
 */
function woohipaypro_number_format($n){
    
    $locale = get_locale();
    
    global $en;
    
    if(in_array($locale,$en)){
        
        $num = number_format($n);
        
    }else{
        
        $num = number_format($n, 0, ',', ' ');
        
    }
    
    return $num;
    
}

/**
 * Add custom field option to custom fields dropdown to enable restriction access with tokens
 */
function woohipaypro_custom_custom_fields(){
    
    if(! isset($GLOBALS['post'])){ return; }

    $post_type = get_post_type($GLOBALS['post']);

    if(!post_type_supports( $post_type, 'custom-fields')){ return; }
    
    ?>
<script>
    if(jQuery('[value="woohipaypro_restrict_field"]').length < 1){
        
        jQuery('#metakeyselect').append('<option value="woohipaypro_restrict_field">woohipaypro_restrict_field</option>');
        
    }
    if(jQuery('[value="woohipaypro_restrict_tokens"]').length < 1){
        
        jQuery('#metakeyselect').append('<option value="woohipaypro_restrict_tokens">woohipaypro_restrict_tokens</option>');
        
    }
</script>
    <?php
}

add_action('admin_footer-post-new.php', 'woohipaypro_custom_custom_fields');

add_action('admin_footer-post.php', 'woohipaypro_custom_custom_fields');

/**
 * Hook to check if loading a page or a post, and check if access is restricted
 * Content will be hidden if user is not connected or if user have no tokens
 */
function woohipaypro_restrict_content(){
    
    global $wp;
    
    global $wpdb;
    
    if(is_single() or is_page()){
        
        global $post;
        
        global $en;
        
        $options        = get_option('woohipaypro_opts');
        
        $post_id        = get_the_ID();
        
        $tokens_meta    = get_post_meta($post_id,'woohipaypro_restrict_field',true);
        
        $tokens_data    = get_post_meta($post_id,'woohipaypro_restrict_tokens',true);
                
        $tokens_data    = explode('|',$tokens_data);
        
        $tokens_spent   = $tokens_data[0];
        
        $cost           = intval($tokens_spent) > 1 ? $tokens_spent.' '.$options[$tokens_meta][1] : $tokens_spent.' '.$options[$tokens_meta][0];
        
        $cost_extract   = intval($tokens_spent) > 1 ? $tokens_spent.' '.$options[$tokens_meta][1].' '.esc_html__('débités', 'woohipaypro') : $tokens_spent.' '.$options[$tokens_meta][0].' '.esc_html__('débité', 'woohipaypro') ;
                
        if(count($tokens_data) == 1){
                        
            $tokens_label = $cost.' '.esc_html__('à chaque consultation de la page.', 'woohipaypro');
            
        }else if(count($tokens_data) == 2){
            
            $delay      = explode('-',$tokens_data[1]);
            
            if(count($delay) == 1 && intval($delay[0]) == 0){
                
                $tokens_label   = $cost.' '.esc_html__('= accès illimité à la page.', 'woohipaypro').' '.$delay_label;
                
                $delay_access   = '00-00-0000 00:00:00';
                
            }else if(count($delay) == 2){
                
                $delay_num  = $delay[0];
            
                $delay_unit = $delay[1];
                
                $date       = new DateTime();
                
                if($delay_unit == 'min'){
                    
                    if(intval($delay_num) >= 1){
                        
                        $date->modify('+'.intval($delay_num).' minutes');
                        
                    }else{
                        
                        $date->modify('+'.intval($delay_num).' minute');
                        
                    }
                                        
                    $delay_label    = intval($delay_num) >= 1 ? $delay_num.esc_html__(' minutes', 'woohipaypro') : $delay_num.esc_html__(' minute', 'woohipaypro');
                    
                    $delay_access   = $date->getTimestamp();
                    
                }else if($delay_unit == 'h'){
                    
                    if(intval($delay_num) >= 1){
                        
                        $date->modify('+'.intval($delay_num).' hours');
                        
                    }else{
                        
                        $date->modify('+'.intval($delay_num).' hour');
                        
                    }
                    
                    $delay_label    = intval($delay_num) >= 1 ? $delay_num.esc_html__(' heures', 'woohipaypro') : $delay_num.esc_html__(' heure', 'woohipaypro');
                    
                    $delay_access   = $date->getTimestamp();
                    
                }else if($delay_unit == 'd'){
                    
                    if(intval($delay_num) >= 1){
                        
                        $date->modify('+'.intval($delay_num).' days');
                        
                    }else{
                        
                        $date->modify('+'.intval($delay_num).' day');
                        
                    }
                    
                    $delay_label    = intval($delay_num) >= 1 ? $delay_num.esc_html__(' jours', 'woohipaypro') : $delay_num.esc_html__(' jour', 'woohipaypro');
                    
                    $delay_access   = $date->getTimestamp();
                    
                }else if($delay_unit == 'm'){
                    
                    if(intval($delay_num) >= 1){
                        
                        $date->modify('+'.intval($delay_num).' months');
                        
                    }else{
                        
                        $date->modify('+'.intval($delay_num).' month');
                        
                    }
                    
                    $delay_label    = intval($delay_num) >= 1 ? $delay_num.esc_html__(' mois', 'woohipaypro') : $delay_num.esc_html__(' mois', 'woohipaypro');
                    
                    $delay_access   = $date->getTimestamp();
                    
                }else if($delay_unit == 'y'){
                                        
                    if(intval($delay_num) >= 1){
                        
                        $date->modify('+'.intval($delay_num).' years');
                        
                    }else{
                        
                        $date->modify('+'.intval($delay_num).' year');
                        
                    }
                    
                    $delay_label    = intval($delay_num) >= 1 ? $delay_num.esc_html__(' ans', 'woohipaypro') : $delay_num.esc_html__(' an', 'woohipaypro');
                    
                    $delay_access   = $date->getTimestamp();
                    
                }
                
                if(in_array($locale,$en)){
        
                    $delay_access = date('Y-m-d H:i:s',$delay_access);

                }else{

                    $delay_access = date('d-m-Y H:i:s',$delay_access);

                }
                
                $tokens_label = $cost.' '.esc_html__('= accès à la page pendant', 'woohipaypro').' '.$delay_label.'.<br/>'.esc_html__('Expiration de l\'accès à la page : ', 'woohipaypro').$delay_access.'.';
                
            }
            
        }
                
        if($tokens_meta && $tokens_data){
            
            if(is_user_logged_in()){
                
                $user_id        = get_current_user_id();
                
                $credits        = get_user_meta($user_id, $tokens_meta, true);
                
                $access         = get_user_meta($user_id, 'woohipaypro_access', false);
                
                $user_access    = $access[0];
                
                if(count($tokens_data) == 2){
                    
                    /**
                     * If access data for restricted content in database...
                     */
                    if($access){
                        
                        if(array_key_exists($post_id, $user_access)){
                                                        
                            if($user_access[$post_id] == '00-00-0000 00:00:00'){
                                
                                $expiration = 0;
                                
                            }else{
                                
                                $expiration = new DateTime($user_access[$post_id]);
                                
                            }
                                                        
                            if($expiration == 0){
                                
                                $notice  = '<div id="woohipaypro-restricted-notice">'.esc_html__('Votre accès à ce contenu est illimité.', 'woohipaypro').'</div>';
                                
                                $content = $post->post_content.$notice;
                                
                            }else if($expiration != 0 && time() < $expiration->getTimestamp()){
                                
                                $notice  = '<div id="woohipaypro-restricted-notice">'.esc_html__('Vous avez accès à ce contenu jusqu\'au :', 'woohipaypro').' '.$user_access[$post_id].'</div>';
                                                                
                                $content = $post->post_content.$notice;
                                
                            }else if($expiration != 0 && time() > $expiration->getTimestamp()){
                                
                                if($credits){
                    
                                    if(intval($credits) >= intval($tokens_spent)){
                                        
                                        $user_access[$post_id] = $delay_access;

                                        update_user_meta($user_id, 'woohipaypro_access', $user_access);

                                        update_user_meta($user_id, $tokens_meta, intval($credits)-intval($tokens_spent));

                                        $notice  = '<div id="woohipaypro-restricted-notice">'.esc_html__('Vous avez accès à ce contenu jusqu\'au :', 'woohipaypro').' '.$user_access[$post_id].'</div>';
                                                                
                                        $content = $post->post_content.$notice;

                                    }else{

                                        $need_credits = (intval($tokens_spent)-intval($credits)) >= 1 ? (intval($tokens_spent)-intval($credits)).' '.$options[$tokens_meta][1] : (intval($tokens_spent)-intval($credits)).' '.$options[$tokens_meta][0];

                                        $content = '<div id="woohipaypro-restricted-access">
    <h1>'.get_the_title($post_id).'</h1>
    <p><em>'.$post->post_excerpt.'</em></p>
    <hr/>
    <h2>'.esc_html__('Accès restreint', 'woohipaypro').'</h2>
    <p><div class="woohipaypro-stop icon centered-icon"></div></p>
    <p>'.esc_html__('Le contenu de cette page est restreint.', 'woohipaypro').'<br/>'.esc_html__('La consultation de cette page coûte :', 'woohipaypro').' <strong>'.$cost.'</strong>.<br/>'.esc_html__('Il vous manque :', 'woohipaypro').' <strong>'.$need_credits.'</strong>.</p>
    <p><small><em>'.$tokens_label.'</em></small></p>
    <p><a href="'.esc_url(get_permalink(woocommerce_get_page_id('shop'))).'"><i class="woohipaypro-card icon"></i> '.esc_html__('Acheter des', 'woohipaypro').' '.$options[$tokens_meta][1].'</a></p>
</div>';

                                    }

                                }else{

                                    $content = '<div id="woohipaypro-restricted-access">
    <h1>'.get_the_title($post_id).'</h1>
    <p><em>'.$post->post_excerpt.'</em></p>
    <hr/>
    <h2>'.esc_html__('Accès restreint', 'woohipaypro').'</h2>
    <p><div class="woohipaypro-stop icon centered-icon"></div></p>
    <p>'.esc_html__('Le contenu de cette page est restreint.', 'woohipaypro').'<br/>'.esc_html__('La consultation de cette page coûte :', 'woohipaypro').' <strong>'.$cost.'</strong>.<br/>'.esc_html__('Vous possédez :', 'woohipaypro').' <strong>0 '.$options[$tokens_meta][0].'</strong>.</p>
    <p><small><em>'.$tokens_label.'</em></small></p>
    <p><a href="'.esc_url(get_permalink(woocommerce_get_page_id('shop'))).'"><i class="woohipaypro-card icon"></i> '.esc_html__('Acheter des', 'woohipaypro').' '.$options[$tokens_meta][1].'</a></p>
</div>';

                                }
                                
                            }
                            
                        }else{
                            
                            if($credits){
                    
                                if(intval($credits) >= intval($tokens_spent)){

                                    $user_access[$post_id] = $delay_access;

                                    update_user_meta($user_id, 'woohipaypro_access', $user_access);

                                    update_user_meta($user_id, $tokens_meta, intval($credits)-intval($tokens_spent));
                                    
                                    if($delay_access == '00-00-0000 00:00:00'){
                                        
                                        $notice  = '<div id="woohipaypro-restricted-notice">'.__('Votre accès à ce contenu est illimité.', 'woohipaypro').'</div>';
                                
                                        $content = $post->post_content.$notice;
                                        
                                    }else{
                                        
                                        $notice  = '<div id="woohipaypro-restricted-notice">'.__('Vous avez accès à ce contenu jusqu\'au :', 'woohipaypro').' '.$user_access[$post_id].'</div>';
                                                                
                                        $content = $post->post_content.$notice;
                                        
                                    }

                                }else{

                                    $need_credits = (intval($tokens_spent)-intval($credits)) >= 1 ? (intval($tokens_spent)-intval($credits)).' '.$options[$tokens_meta][1] : (intval($tokens_spent)-intval($credits)).' '.$options[$tokens_meta][0];

                                    $content = '<div id="woohipaypro-restricted-access">
    <h1>'.get_the_title($post_id).'</h1>
    <p><em>'.$post->post_excerpt.'</em></p>
    <hr/>
    <h2>'.esc_html__('Accès restreint', 'woohipaypro').'</h2>
    <p><div class="woohipaypro-stop icon centered-icon"></div></p>
    <p>'.esc_html__('Le contenu de cette page est restreint.', 'woohipaypro').'<br/>'.esc_html__('La consultation de cette page coûte :', 'woohipaypro').' <strong>'.$cost.'</strong>.<br/>'.esc_html__('Il vous manque :', 'woohipaypro').' <strong>'.$need_credits.'</strong>.</p>
    <p><small><em>'.$tokens_label.'</em></small></p>
    <p><a href="'.esc_url(get_permalink(woocommerce_get_page_id('shop'))).'"><i class="woohipaypro-card icon"></i> '.esc_html__('Acheter des', 'woohipaypro').' '.$options[$tokens_meta][1].'</a></p>
</div>';

                                }

                            }else{

                                $content = '<div id="woohipaypro-restricted-access">
    <h1>'.get_the_title($post_id).'</h1>
    <p><em>'.$post->post_excerpt.'</em></p>
    <hr/>
    <h2>'.esc_html__('Accès restreint', 'woohipaypro').'</h2>
    <p><div class="woohipaypro-stop icon centered-icon"></div></p>
    <p>'.esc_html__('Le contenu de cette page est restreint.', 'woohipaypro').'<br/>'.esc_html__('La consultation de cette page coûte :', 'woohipaypro').' <strong>'.$cost.'</strong>.<br/>'.esc_html__('Vous possédez :', 'woohipaypro').' <strong>0 '.$options[$tokens_meta][0].'</strong>.</p>
    <p><small><em>'.$tokens_label.'</em></small></p>
    <p><a href="'.esc_url(get_permalink(woocommerce_get_page_id('shop'))).'"><i class="woohipaypro-card icon"></i> '.esc_html__('Acheter des', 'woohipaypro').' '.$options[$tokens_meta][1].'</a></p>
</div>';

                            }
                            
                        }
                    
                    /**
                     * If no access data for restricted content in database...
                     */
                    }else{
                        
                        if($credits){
                    
                            if(intval($credits) >= intval($tokens_spent)){
                                
                                $user_access = array($post_id => $delay_access);
                                
                                update_user_meta($user_id, 'woohipaypro_access', $user_access);

                                update_user_meta($user_id, $tokens_meta, intval($credits)-intval($tokens_spent));

                                if($delay_access == '00-00-0000 00:00:00'){
                                        
                                    $notice  = '<div id="woohipaypro-restricted-notice">'.esc_html__('Votre accès à ce contenu est illimité.', 'woohipaypro').'</div>';

                                    $content = $post->post_content.$notice;

                                }else{

                                    $notice  = '<div id="woohipaypro-restricted-notice">'.esc_html__('Vous avez accès à ce contenu jusqu\'au :', 'woohipaypro').' '.$user_access[$post_id].'</div>';

                                    $content = $post->post_content.$notice;

                                }

                            }else{

                                $need_credits = (intval($tokens_spent)-intval($credits)) >= 1 ? (intval($tokens_spent)-intval($credits)).' '.$options[$tokens_meta][1] : (intval($tokens_spent)-intval($credits)).' '.$options[$tokens_meta][0];

                                $content = '<div id="woohipaypro-restricted-access">
    <h1>'.get_the_title($post_id).'</h1>
    <p><em>'.$post->post_excerpt.'</em></p>
    <hr/>
    <h2>'.esc_html__('Accès restreint', 'woohipaypro').'</h2>
    <p><div class="woohipaypro-stop icon centered-icon"></div></p>
    <p>'.esc_html__('Le contenu de cette page est restreint.', 'woohipaypro').'<br/>'.esc_html__('La consultation de cette page coûte :', 'woohipaypro').' <strong>'.$cost.'</strong>.<br/>'.esc_html__('Il vous manque :', 'woohipaypro').' <strong>'.$need_credits.'</strong>.</p>
    <p><small><em>'.$tokens_label.'</em></small></p>
    <p><a href="'.esc_url(get_permalink(woocommerce_get_page_id('shop'))).'"><i class="woohipaypro-card icon"></i> '.esc_html__('Acheter des', 'woohipaypro').' '.$options[$tokens_meta][1].'</a></p>
</div>';

                            }

                        }else{

                            $content = '<div id="woohipaypro-restricted-access">
    <h1>'.get_the_title($post_id).'</h1>
    <p><em>'.$post->post_excerpt.'</em></p>
    <hr/>
    <h2>'.esc_html__('Accès restreint', 'woohipaypro').'</h2>
    <p><div class="woohipaypro-stop icon centered-icon"></div></p>
    <p>'.esc_html__('Le contenu de cette page est restreint.', 'woohipaypro').'<br/>'.esc_html__('La consultation de cette page coûte :', 'woohipaypro').' <strong>'.$cost.'</strong>.<br/>'.esc_html__('Vous possédez :', 'woohipaypro').' <strong>0 '.$options[$tokens_meta][0].'</strong>.</p>
    <p><small><em>'.$tokens_label.'</em></small></p>
    <p><a href="'.esc_url(get_permalink(woocommerce_get_page_id('shop'))).'"><i class="woohipaypro-card icon"></i> '.esc_html__('Acheter des', 'woohipaypro').' '.$options[$tokens_meta][1].'</a></p>
</div>';

                        }
                        
                    }
                
                }else if(count($tokens_data) == 1){
                    
                    if($credits){
                    
                        if(intval($credits) >= intval($tokens_spent)){

                            update_user_meta($user_id, $tokens_meta, intval($credits)-intval($tokens_spent));

                            $notice  = '<div id="woohipaypro-restricted-notice">'.$cost_extract.' '.__('de votre compte.', 'woohipaypro').'</div>';

                            $content = $post->post_content.$notice;

                        }else{

                            $need_credits = (intval($tokens_spent)-intval($credits)) >= 1 ? (intval($tokens_spent)-intval($credits)).' '.$options[$tokens_meta][1] : (intval($tokens_spent)-intval($credits)).' '.$options[$tokens_meta][0];

                            $content = '<div id="woohipaypro-restricted-access">
    <h1>'.get_the_title($post_id).'</h1>
    <p><em>'.$post->post_excerpt.'</em></p>
    <hr/>
    <h2>'.esc_html__('Accès restreint', 'woohipaypro').'</h2>
    <p><div class="woohipaypro-stop icon centered-icon"></div></p>
    <p>'.esc_html__('Le contenu de cette page est restreint.', 'woohipaypro').'<br/>'.esc_html__('La consultation de cette page coûte :', 'woohipaypro').' <strong>'.$cost.'</strong>.<br/>'.esc_html__('Il vous manque :', 'woohipaypro').' <strong>'.$need_credits.'</strong>.</p>
    <p><small><em>'.$tokens_label.'</em></small></p>
    <p><a href="'.esc_url(get_permalink(woocommerce_get_page_id('shop'))).'"><i class="woohipaypro-card icon"></i> '.esc_html__('Acheter des', 'woohipaypro').' '.$options[$tokens_meta][1].'</a></p>
</div>';

                        }

                    }else{

                        $content = '<div id="woohipaypro-restricted-access">
    <h1>'.get_the_title($post_id).'</h1>
    <p><em>'.$post->post_excerpt.'</em></p>
    <hr/>
    <h2>'.esc_html__('Accès restreint', 'woohipaypro').'</h2>
    <p><div class="woohipaypro-stop icon centered-icon"></div></p>
    <p>'.esc_html__('Le contenu de cette page est restreint.', 'woohipaypro').'<br/>'.esc_html__('La consultation de cette page coûte :', 'woohipaypro').' <strong>'.$cost.'</strong>.<br/>'.esc_html__('Vous possédez :', 'woohipaypro').' <strong>0 '.$options[$tokens_meta][0].'</strong>.</p>
    <p><small><em>'.$tokens_label.'</em></small></p>
    <p><a href="'.get_permalink(woocommerce_get_page_id('shop')).'"><i class="woohipaypro-card icon"></i> '.esc_html__('Acheter des', 'woohipaypro').' '.$options[$tokens_meta][1].'</a></p>
</div>';

                    }
                    
                }
                
            }else{
                
                $content = '<div id="woohipaypro-restricted-access">
    <h1>'.get_the_title($post_id).'</h1>
    <p><em>'.$post->post_excerpt.'</em></p>
    <hr/>
    <h2>'.esc_html__('Accès restreint', 'woohipaypro').'</h2>
    <p><div class="woohipaypro-stop icon centered-icon"></div></p>
    <p>'.esc_html__('Le contenu de cette page est restreint.<br/>Vous devez vous connecter ou créer un compte pour consulter le contenu de cette page.', 'woohipaypro').'</p>
    <p><small><em>'.$tokens_label.'</em></small></p>
    <p><a href="'.esc_url(wp_login_url(get_permalink())).'"><i class="woohipaypro-unlock icon"></i> '.esc_html__('Connexion', 'woohipaypro').'</a></p>
    <p><a href="'.esc_url(wp_registration_url()).'"><i class="woohipaypro-key icon"></i> '.esc_html__('Créer un compte', 'woohipaypro').'</a></p>
</div>';
                
            }
            
        }else{
            
            $content = $post->post_content;
            
        }
        
        return $content;
        
    }
        
}

add_filter('the_content', 'woohipaypro_restrict_content');

/**
 * Hook to add custom price data for recurring payment products
 */
function woohipaypro_recurring_payment_data_product(){
    
    global $wpdb;
    
    global $woocommerce;
    
    global $product;
    
    $post_id = $product->get_id();
    
    $allow_hipaysubscription = get_post_meta($post_id, 'allow_hipaysubscription', true);
    
    $hipaysubscription_data  = get_post_meta($post_id, 'hipaysubscription_data');
    
    $options                 = get_option('woohipaypro_opts');
    
    if($allow_hipaysubscription == 'no'){
        
        if($hipaysubscription_data[0]['cu_tokens_hipaysubscription'] != '' && intval($hipaysubscription_data[0]['cu_tokens_hipaysubscription']) > 0){
            
            $content = intval($hipaysubscription_data[0]['cu_tokens_hipaysubscription']) > 1 ?
                '<div class="woohipaypro-tokens-label-product"><strong>'.$hipaysubscription_data[0]['cu_tokens_hipaysubscription'].' '.$options[$hipaysubscription_data[0]['cycle_hipaysubscription']][1].'</strong> <small><em>'.esc_html__('crédités sur votre compte après achat.','woohipaypro').'</em></small></div>' :
                '<div class="woohipaypro-tokens-label-product"><strong>'.$hipaysubscription_data[0]['cu_tokens_hipaysubscription'].' '.$options[$hipaysubscription_data[0]['cycle_hipaysubscription']][0].'</strong> <small><em>'.esc_html__('crédité sur votre compte après achat.','woohipaypro').'</em></small></div>';
            
        }
        
    }else if($allow_hipaysubscription == 'yes'){
        
        if(intval($hipaysubscription_data[0]['type_hipaysubscription']) == 0){
            
            $frequency = intval($hipaysubscription_data[0]['cl_freq_hipaysubscription']) > 1 ? esc_html__('tous les','woohipaypro').' '.$hipaysubscription_data[0]['cl_freq_hipaysubscription'].' '.esc_html__('jours.','woohipaypro') : esc_html__('chaque','woohipaypro').' '.$hipaysubscription_data[0]['cl_freq_hipaysubscription'].' '.esc_html__('jour.','woohipaypro') ;
                
            $content = '<div class="woohipaypro-rebill-badge"><strong>'.esc_html__('ABONNEMENT','woohipaypro').'</strong> <small><em>'.esc_html__('Paiement','woohipaypro').' '.$frequency.'</em></small></div>';
            
            if($hipaysubscription_data[0]['cl_tokens_hipaysubscription'] != '' && intval($hipaysubscription_data[0]['cl_tokens_hipaysubscription']) > 0){
                            
                $content .= intval($hipaysubscription_data[0]['cl_tokens_hipaysubscription']) > 1 ?
                    '<div class="woohipaypro-tokens-label-product"><strong>'.$hipaysubscription_data[0]['cl_tokens_hipaysubscription'].' '.$options[$hipaysubscription_data[0]['cycle_hipaysubscription']][1].'</strong> <small><em>'.esc_html__('crédités sur votre compte à chaque paiement.','woohipaypro').'</em></small></div>' :
                    '<div class="woohipaypro-tokens-label-product"><strong>'.$hipaysubscription_data[0]['cl_tokens_hipaysubscription'].' '.$options[$hipaysubscription_data[0]['cycle_hipaysubscription']][0].'</strong> <small><em>'.esc_html__('crédité sur votre compte à chaque paiement.','woohipaypro').'</em></small></div>';

            }
            
        }else if(intval($hipaysubscription_data[0]['type_hipaysubscription']) == 1){
            
            $frequency  = intval($hipaysubscription_data[0]['cl_freq_hipaysubscription']) > 1 ? esc_html__('tous les','woohipaypro').' '.$hipaysubscription_data[0]['cl_freq_hipaysubscription'].' '.esc_html__('jours','woohipaypro') : esc_html__('chaque','woohipaypro').' '.$hipaysubscription_data[0]['cl_freq_hipaysubscription'].' '.esc_html__('jour.','woohipaypro') ;
            
            $delay      = intval($hipaysubscription_data[0]['cu_freq_hipaysubscription']) > 1 ? $hipaysubscription_data[0]['cu_freq_hipaysubscription'].' '.esc_html__('jours','woohipaypro') : $hipaysubscription_data[0]['cu_freq_hipaysubscription'].' '.esc_html__('jour','woohipaypro') ;
            
            $content = '<div class="woohipaypro-rebill-badge"><strong>'.esc_html__('ABONNEMENT','woohipaypro').'</strong></div>';
            
            if($hipaysubscription_data[0]['cu_tokens_hipaysubscription'] != '' && intval($hipaysubscription_data[0]['cu_tokens_hipaysubscription']) > 0){
                            
                $content .= intval($hipaysubscription_data[0]['cu_tokens_hipaysubscription']) > 1 ?
                    '<div class="woohipaypro-tokens-label-product smallmarge"><strong>'.$hipaysubscription_data[0]['cu_tokens_hipaysubscription'].' '.$options[$hipaysubscription_data[0]['cycle_hipaysubscription']][1].'</strong> <small><em>'.esc_html__('crédités sur votre compte au 1er paiement.','woohipaypro').'</em></small></div>' :
                    '<div class="woohipaypro-tokens-label-product smallmarge"><strong>'.$hipaysubscription_data[0]['cu_tokens_hipaysubscription'].' '.$options[$hipaysubscription_data[0]['cycle_hipaysubscription']][0].'</strong> <small><em>'.esc_html__('crédité sur votre compte au 1er paiement.','woohipaypro').'</em></small></div>';

            }
            
            if($hipaysubscription_data[0]['cl_tokens_hipaysubscription'] != '' && intval($hipaysubscription_data[0]['cl_tokens_hipaysubscription']) > 0){
                
                $rebill_tokens = intval($hipaysubscription_data[0]['cl_tokens_hipaysubscription']) > 1 ?
                    '<br/><small><strong class="woohipaypro-underline">'.$hipaysubscription_data[0]['cl_tokens_hipaysubscription'].' '.$options[$hipaysubscription_data[0]['cycle_hipaysubscription']][1].'</strong> <em>'.esc_html__('crédités sur votre compte à chaque paiement dès le second paiement.','woohipaypro').'</em></small>' :
                    '<br/><small><strong class="woohipaypro-underline">'.$hipaysubscription_data[0]['cl_tokens_hipaysubscription'].' '.$options[$hipaysubscription_data[0]['cycle_hipaysubscription']][0].'</strong> <em>'.esc_html__('crédité sur votre compte à chaque paiement dès le second paiement.','woohipaypro').'</em></small>';
                
            }
                        
            $content .= '<div class="woohipaypro-custom-rebill">
<small>'.esc_html__('Second paiement après','woohipaypro').' '.$delay.' : <strong>'.$hipaysubscription_data[0]['cu_price_hipaysubscription'].' '.get_woocommerce_currency_symbol().'</strong>.</small><br/>
<small>'.esc_html__('Puis paiement','woohipaypro').' '.$frequency.' : <strong>'.$hipaysubscription_data[0]['cu_price_hipaysubscription'].' '.get_woocommerce_currency_symbol().'</strong>.</small>
'.$rebill_tokens.'</div>';
            
        }
        
    }
    
    echo $content;
    
}

add_action('woocommerce_before_add_to_cart_button', 'woohipaypro_recurring_payment_data_product');

/**
 * Shortcode to display simple tokens data, from meta name, for user in front office
 */
function woohipaypro_show_tokens($atts){
    
    global $wpdb;
    
    $options = get_option('woohipaypro_opts');
    
    $user_id = get_current_user_id();
    
    /**
     * $atts:
     * array(
     *      'meta', // meta name in database to target tokens label
     *      'label' // if "yes", tokens label is showed, if "no" not. Default: "no"
     * )
     * 
     * Ex.: [woohipaypro_tokens meta="my_meta" label="yes"]
     */
    
    if(is_user_logged_in()){
        
        extract($atts);
        
        $icon = '<div class="woohipaypro-token icon woohipaypro-shortcode-tokens"></div>';

        $account_tokens = esc_url(get_permalink(get_option('woocommerce_myaccount_page_id')).'woohipaypro-tokens/');

        if(array_key_exists($atts['meta'],$options)){

            $data = get_user_meta($user_id, $atts['meta'], true);

            if($data){

                if($atts['label']){

                    $label = $data > 1 ? ' <small><em>'.$options[$atts['meta']][1].'</em></small>' : ' <small><em>'.$options[$atts['meta']][0].'</em></small>';

                    $label = $atts['label'] == 'yes' ? $label : '';

                    $tokens = $icon.' <a href="'.$account_tokens.'">'.woohipaypro_number_format($data).$label.'</a>';

                }else{

                    $label = ' <small><em>'.$options[$atts['meta']][0].'</em></small>';

                    $label = $atts['label'] == 'yes' ? $label : '';

                    $tokens = $icon.' <a href="'.$account_tokens.'">'.woohipaypro_number_format($data).$label.'</a>';

                }

            }else{

                $label = ' <small><em>'.$options[$atts['meta']][0].'</em></small>';

                $label = $atts['label'] == 'yes' ? $label : '';

                $tokens = $icon.' <a href="'.$account_tokens.'">0'.$label.'</a>';

            }

        }else{

            $tokens = '<small>'.esc_html__('La meta','woohipaypro').' '.$atts['meta'].' '.esc_html__('n\'existe pas...','woohipaypro').'</small>';

        }
        
    }else{
        
        $tokens = '';
        
    }
    
    ob_start();
    echo $tokens;
    return ob_get_clean();
    
}

add_shortcode('woohipaypro_tokens', 'woohipaypro_show_tokens');

/**
 * Shortcode to display a list of tokens data, from meta names, for user in front office
 */
function woohipaypro_show_tokens_list($atts){
    
    global $wpdb;
    
    $options = get_option('woohipaypro_opts');
    
    $user_id = get_current_user_id();
    
    /**
     * $atts:
     * array(
     *      'color', // "dark" for the dark theme, "light" for the light theme. Default: dark
     * )
     * Ex.: [woohipaypro_tokens_list color="dark"]
     */
    
    if(is_user_logged_in()){
        
        extract($atts);
        
        $theme = $atts['color'];
        
        $arr = array('dark','light');
        
        $theme = !in_array($theme,$arr) ? 'dark' : $theme;
        
        $icon = '<div class="woohipaypro-token icon woohipaypro-shortcode-tokens"></div>';
        
        $account_tokens = esc_url(get_permalink(get_option('woocommerce_myaccount_page_id')).'woohipaypro-tokens/');
                
        $main = ' <a href="'.$account_tokens.'">'.$icon.' '.__('Crédits/jetons','woohipaypro');
        
        $tokens = '<div id="woohipaypro-all-tokens">'.$main.'<ul id="woohipaypro-tokens-list-'.$theme.'">';
        
        foreach($options as $k => $v){
            
            $data = get_user_meta($user_id, $k, true);
            
            if($data){
                
                $label = $data > 1 ? ' <small><em>'.$v[1].'</em></small>' : ' <small><em>'.$v[0].'</em></small>';
                
                $tokens .= '<li>'.$icon.' '.$data.' '.$label.'</li>';
                
            }else{
                
                $tokens .= '<li>'.$icon.' 0 <small><em>'.$v[0].'</em></small></li>';
                
            }
            
        }
                
        $tokens .= '</ul></a></div>';
        
    }else{
        
        $tokens = '';
        
    }
    
    ob_start();
    echo $tokens;
    return ob_get_clean();
    
}

add_shortcode('woohipaypro_tokens_list', 'woohipaypro_show_tokens_list');

/**
 * Shortcode to display a simple list of tokens data, from meta names, for user in front office
 */
function woohipaypro_show_tokens_simple_list($atts){
    
    global $wpdb;
    
    $options = get_option('woohipaypro_opts');
    
    $user_id = get_current_user_id();
    
    /**
     * $atts:
     * array(
     *      'color', // "dark" for the dark theme, "light" for the light theme. Default: dark
     * )
     * Ex.: [woohipaypro_tokens_list color="dark"]
     */
    
    if(is_user_logged_in()){
        
        extract($atts);
        
        $theme = $atts['color'];
        
        $arr = array('dark','light');
        
        $theme = !in_array($theme,$arr) ? 'dark' : $theme;
        
        $icon = '<div class="woohipaypro-token icon woohipaypro-shortcode-simple-tokens"></div>';
        
        $account_tokens = esc_url(get_permalink(get_option('woocommerce_myaccount_page_id')).'woohipaypro-tokens/');
                
        $main = ' <a href="'.$account_tokens.'">'.$icon.' '.esc_html__('Crédits/jetons','woohipaypro').'</a>';
        
        $tokens = '<div id="woohipaypro-all-tokens-list-'.$theme.'">'.$main.'<ul id="woohipaypro-tokens-simple-list-'.$theme.'">';
        
        foreach($options as $k => $v){
            
            $data = get_user_meta($user_id, $k, true);
            
            if($data){
                
                $label = $data > 1 ? ' <small><em>'.$v[1].'</em></small>' : ' <small><em>'.$v[0].'</em></small>';
                
                $tokens .= '<li>'.$icon.' '.$data.' '.$label.'</li>';
                
            }else{
                
                $tokens .= '<li>'.$icon.' 0 <small><em>'.$v[0].'</em></small></li>';
                
            }
            
        }
                
        $tokens .= '</ul></div>';
        
    }else{
        
        $tokens = '';
        
    }
    
    ob_start();
    echo $tokens;
    return ob_get_clean();
    
}

add_shortcode('woohipaypro_tokens_simple_list', 'woohipaypro_show_tokens_simple_list');

/**
 * Shortcode to load a button to make an order from a product
 */
function woohipaypro_load_button_order($atts){
    
    global $wpdb;
    
    global $woocommerce;
        
    /**
     * $atts:
     * array(
     *      'product_id', // product ID to target
     *      'class',      // css class to target
     *      'label'       // label you want on the button
     *      'price'       // custom price to set to the product: floatval
     * )
     * Ex.: [woohipaypro_button_order product_id="69" class="btn-primary" label="Souscrire"]
     */
    
    extract($atts);
    
    $class = $atts['class'] == '' ? '' : ' '.$atts['class'];
    
    $label = $atts['label'] == '' ? esc_html__('Acheter','woohipaypro') : esc_html(sanitize_text_field($atts['label']));
    
    $price = $atts['price'] == '' ? '' : floatval($atts['price']);
        
    $link  = esc_url($woocommerce->cart->get_cart_url());
    
    if($atts['product_id'] != '' && get_post_status(intval($atts['product_id'])) !== false){
        
        $button = '<a href="javascript:void(0);" class="woohipaypro-button-order'.$class.'" data-product="'.$atts['product_id'].'" data-price="'.$price.'" data-target="'.$link.'">'.$label.'</a>';
        
    }else{
        
        $button = esc_html(__('Le produit ciblé n\'existe pas. ID du produit ciblé : ','woohipaypro').$atts['product_id']);
        
    }
    
    ob_start();
    echo $button;
    return ob_get_clean();
    
}

add_shortcode('woohipaypro_button_order', 'woohipaypro_load_button_order');

/**
 * Function to get product_id from woohipaypro button order, make the order, and redirect to checkout
 */
function woohipaypro_make_order(){
    
    global $wp;
    
    global $wpdb;
    
    global $woocommerce;
    
    $product_id = $_POST['woohipaypro_product_id'] != '' ? intval($_POST['woohipaypro_product_id']) : NULL;
    
    $price      = $_POST['woohipaypro_price'] == '' ? '' : floatval($_POST['woohipaypro_price']);
    
    $target     = $_POST['woohipaypro_target'] != '' ? esc_url($_POST['woohipaypro_target']) : NULL;
        
    if($product_id != NULL && $target != NULL){
        
        $product = wc_get_product($product_id);
        
        if($price != ''){
            
            $cart_item_data = array(
                'custom_order'  => 'yes',
                'custom_price'  => $price
            );
            
        }else{
            
            $cart_item_data = array(
                'custom_order'  => 'yes',
                'custom_price'  => NULL
            );
            
        }
                
        if(is_user_logged_in()){
            
            $user_id = get_current_user_id();
           
            $order = wc_create_order(array('customer_id' => $user_id));
            
        }else{
            
            $order = wc_create_order(array('customer_id' => $order->get_user_id()));
            
        }
        
        WC()->cart->add_to_cart($product_id, 1, '', '', $cart_item_data);
        
        WC()->cart->calculate_totals();
        
        WC()->cart->set_session();
        
        WC()->cart->maybe_set_cart_cookies();
        
        $result = array(
            'label'     => esc_html__('ok','woohipaypro'),
            'action'    => $target
        );
        
    }else{
        
        $result = array(
            'label'     => esc_html__('erreur','woohipaypro'),
            'action'    => esc_html__('Une erreur est survenue : le bouton est mal configuré.','woohipaypro')
        );
        
    }
    
    echo wp_send_json(json_encode($result));
    
    wp_die();
    
}

add_action('wp_ajax_nopriv_woohipaypro_make_order', 'woohipaypro_make_order');
add_action('wp_ajax_woohipaypro_make_order','woohipaypro_make_order');

/**
 * Hook to calculate total cart if custom price
 */
function woohipaypro_add_custom_product($cart_object){
    
    if(!WC()->session->__isset("reload_checkout")){
        
        if(is_admin() && ! defined('DOING_AJAX')){ return; }

        if(did_action('woocommerce_before_calculate_totals') >= 2){ return; }
        
        foreach($cart_object->cart_contents as $key => $value){
            
            if(isset($value["custom_order"]) && $value["custom_order"] == 'yes'){
                                
                if(isset($value["custom_price"]) && $value["custom_price"] != NULL){
                                    
                    $value['data']->set_price($value["custom_price"]);

                }
                
            }
                        
        } 
        
    }
    
}

add_action('woocommerce_before_calculate_totals', 'woohipaypro_add_custom_product', 10, 1);

/**
 * Stop/cancel user subscription formula
 */
function woohipaypro_stop_formula(){
    
    global $wp;
    
    global $wpdb;
    
    $formula_id = sanitize_text_field($_POST['woohipaypro_formula_id']);
    
    if($formula_id != ''){
        
        $woohipaypro_opts = get_option('woocommerce_woohipaypro_settings');
        
        $options = array( 
            'compression'   => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP,
            'cache_wsdl'    => WSDL_CACHE_NONE,
            'soap_version'  => SOAP_1_1,
            'encoding'      => 'UTF-8'
        );

        $client = new SoapClient('https://ws.hipay.com/soap/subscription-v2?wsdl', $options);

        $stop = $client->cancel(array('parameters'=>array(
            'wsLogin'           => $woohipaypro_opts['publishable_key'],
            'wsPassword'        => $woohipaypro_opts['private_key'],
            'subscriptionId'    => $formula_id
        )));
        
        $response = $stop->cancelResult->code;
                
        if($response == 0){
            
            woohipaypro_update_stopped_formula($formula_id);
            
            $result = array(
                'label'     => esc_html__('ok','woohipaypro'),
                'action'    => esc_html__('','woohipaypro'),
                'formula'   => $formula_id
            );
            
        }else{
            
            $result = array(
                'label'     => esc_html__('erreur','woohipaypro'),
                'action'    => esc_html__('','woohipaypro'),
                'formula'   => $formula_id
            );
            
        }
                
    }else if($formula_id == ''){
        
        $result = array(
            'label'     => esc_html__('erreur','woohipaypro'),
            'action'    => esc_html__('','woohipaypro'),
            'formula'   => $formula_id
        );
        
    }
    
    echo wp_send_json(json_encode($result));
    
    wp_die();
    
}

add_action('wp_ajax_nopriv_woohipaypro_stop_formula', 'woohipaypro_stop_formula');
add_action('wp_ajax_woohipaypro_stop_formula','woohipaypro_stop_formula');

/**
 * Function to update order/formula status if formula is stopped
 */
function woohipaypro_update_stopped_formula($id){
    
    global $wp;
    
    global $wpdb;
    
    global $woocommerce;
    
    $results    = $wpdb->get_results("select * from $wpdb->postmeta where meta_key = 'woohipaypro_subid' AND meta_value = '".$id."'", ARRAY_A);
    
    $data       = $results[0];
    
    update_post_meta($data['post_id'],'woohipaypro_subscription_status','stopped');
    
    update_post_meta($data['post_id'],'woohipaypro_subscription_stopped',date('Y-m-d H:i:s'));
        
}

/**
 * Function to add button on order for admin to stopp formula if order is a subscription
 */
function woohipaypro_admin_order(){
    
    global $wpdb;
        
    $order_id = isset($_GET['post']) && !empty($_GET['post']) ? intval($_GET['post']) : NULL;
    
    $type     = $wpdb->get_results("select * from $wpdb->postmeta where post_id = '".$order_id."' AND meta_key='woohipaypro_type'", ARRAY_A);
    
    if($type[0]['meta_value'] == 'subscription'){
        
        $subscription = $wpdb->get_results("select * from $wpdb->postmeta where post_id = '".$order_id."' AND meta_key='woohipaypro_subid'", ARRAY_A);
        
        $sub_status   = $wpdb->get_results("select * from $wpdb->postmeta where post_id = '".$order_id."' AND meta_key='woohipaypro_subscription_status'", ARRAY_A);
        
        if($sub_status[0]['meta_value'] == 'active'){
            
            $content = '<div id="woohipaypro-admin-order">
    <div id="woohipaypro-admin">
        <h3 class="first">'.esc_html__('Type de commande :','woohipaypro').'</h3>
        <p class="order-type">'.esc_html__('Abonnement / paiements récurrents','woohipaypro').'</p>
        <h3>'.esc_html__('Arrêter l\'abonnement du client :','woohipaypro').'</h3>
        <button type="button" id="woohipaypro-admin-cancel-subscription" class="button button-primary" name="cancel-subscription" data-subscription="'.$subscription[0]['meta_value'].'" data-error="'.esc_html__('L\'abonnement n\'a pas pu être arrêté : arrêtez l\'abonnement directement sur le site Hipay Wallet.','woohipaypro').'">'.esc_html__('Arrêter l\'abonnement','woohipaypro').'</button>
        <small class="warning">'.esc_html__('ATTENTION : cette action est irréversible. En cliquant sur le bouton vous arrêterez définitivement l\'abonnement du client !','woohipaypro').'</small>
    </div>
</div>
';
            
        }else if($sub_status[0]['meta_value'] == 'stopped'){
            
            $stopped_date = $wpdb->get_results("select * from $wpdb->postmeta where post_id = '".$order_id."' AND meta_key='woohipaypro_subscription_stopped'", ARRAY_A);
            
            $content = '<div id="woohipaypro-admin-order">
    <div id="woohipaypro-admin">
        <h3 class="first">'.esc_html__('Type de commande :','woohipaypro').'</h3>
        <p class="order-type">'.esc_html__('Abonnement / paiements récurrents','woohipaypro').'</p>
        <h3>'.esc_html__('Abonnement stoppé :','woohipaypro').'</h3>
        <p class="info">'.esc_html__('L\'abonnement pour ce client a été stoppé le :','woohipaypro').'<br/><strong>'.$stopped_date[0]['meta_value'].'</strong>.</p>
    </div>
</div>
';
            
        }
                
    }else{
        
        $content = '<div id="woohipaypro-admin-order">
    <div id="woohipaypro-admin">
        <h3 class="first">'.esc_html__('Type de commande :','woohipaypro').'</h3>
        <p class="order-type">'.esc_html__('Commande à paiement unique','woohipaypro').'</p>
    </div>
</div>
';
        
    }
        
    echo $content;
    
}

add_action('woocommerce_admin_order_data_after_order_details', 'woohipaypro_admin_order');

/**
 * Callback function to stop customer formula from admin
 */
function woohipaypro_admin_stop_formula(){
    
    global $wpdb;
    
    $sub_id = sanitize_text_field($_POST['woohipaypro_subcription']);
    
    if($sub_id != ''){
        
        $woohipaypro_opts = get_option('woocommerce_woohipaypro_settings');
        
        $options = array( 
            'compression'   => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP,
            'cache_wsdl'    => WSDL_CACHE_NONE,
            'soap_version'  => SOAP_1_1,
            'encoding'      => 'UTF-8'
        );

        $client = new SoapClient('https://ws.hipay.com/soap/subscription-v2?wsdl', $options);

        $stop = $client->cancel(array('parameters'=>array(
            'wsLogin'           => $woohipaypro_opts['publishable_key'],
            'wsPassword'        => $woohipaypro_opts['private_key'],
            'subscriptionId'    => $sub_id
        )));
        
        $response = $stop->cancelResult->code;
                
        if($response == 0){
            
            woohipaypro_update_stopped_formula($sub_id);
            
            echo "ok";
            
        }else{
            
            echo 'nok';
            
        }
                
    }else{
        
        echo 'nok';
        
    }
        
    wp_die();
    
}

add_action('wp_ajax_woohipaypro_admin_stop_formula','woohipaypro_admin_stop_formula');

/**
 * Add custom columns data in admin orders list
 */
function woohipaypro_admin_orders_columns($columns){
    
    $new_columns = array();
    
    foreach($columns as $column_name => $column_info){

        $new_columns[ $column_name ] = $column_info;

        if('order_status' === $column_name){
            
            $new_columns['order_type'] = esc_html__('Type', 'woohipaypro');
                        
        }
        
    }
    
    return $new_columns;
    
}

add_filter('manage_edit-shop_order_columns', 'woohipaypro_admin_orders_columns', 20);

/**
 * Function to add content to Type column in orders list
 */
function woohipaypro_admin_orders_type_column_content($column){
    
    global $wpdb;
    
    global $post;
    
    if('order_type' === $column){
        
        $type = $wpdb->get_results("select * from $wpdb->postmeta where post_id = '".$post->ID."' AND meta_key='woohipaypro_type'", ARRAY_A);
    
        if($type[0]['meta_value'] == 'subscription'){
            
            $subscription = $wpdb->get_results("select * from $wpdb->postmeta where post_id = '".$post->ID."' AND meta_key='woohipaypro_subid'", ARRAY_A);
        
            $sub_status   = $wpdb->get_results("select * from $wpdb->postmeta where post_id = '".$post->ID."' AND meta_key='woohipaypro_subscription_status'", ARRAY_A);
                        
            if($subscription[0]['meta_value']){
                
                $sub_id = esc_html__('ID de l\'abonnement :','woohipaypro').' <strong class="sub-sub-id">'.$subscription[0]['meta_value'].'</strong>';
                
            }else{
                
                $sub_id = esc_html__('ID de l\'abonnement :','woohipaypro').' <small><strong class="sub-sub-id">'.esc_html__('En attente de paiement...','woohipaypro').'</strong></small>';
                
            }
                        
            if($sub_status[0]['meta_value']){
                
                $status = $sub_status[0]['meta_value'] == 'active' ? '<span class="active-sub">'.esc_html__('EN COURS','woohipaypro').'</span>' : '<span class="stopped-sub">'.esc_html__('STOPPÉ','woohipaypro').'</span>' ;
                
            }else{
                
                $status = '<span class="stopped-sub">'.esc_html__('En attente de paiement...','woohipaypro').'</span>';
                
            }

            $content = '<span class="subscription-payment">'.esc_html__('Abonnement','woohipaypro').' <small>/ '.$status.'</small><br/><small class="sub-sub">'.$sub_id.'</small></span>';

        }else{

            $content = '<span class="oneshot-payment"><em>'.esc_html__('Paiement unique','woohipaypro').'</em></span>';

        }

        echo $content;
        
    }
        
}

add_action('manage_shop_order_posts_custom_column', 'woohipaypro_admin_orders_type_column_content');

/**
 * Custom function to save logs
 */
if(!function_exists('write_log')){

    function write_log($log){
        if (true === WP_DEBUG){
            if (is_array($log) || is_object($log)){
                error_log(print_r($log, true));
            } else {
                error_log($log);
            }
        }
    }

}
