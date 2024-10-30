<?php
/*
 * Plugin Name: Boz Prod WooCommerce Hipay Wallet Pro
 * Plugin URI: https://www.bozprod.eu/
 * Description: Allow simple and reccuring payments on WooCommerce with HiPay Wallet payment gateway.
 * Author: BOZ PROD
 * Author URI: https://www.bozprod.eu/
 * Version: 1.0.0
 * Text Domain: woohipaypro
 * Domain Path: /languages/
 */

// Constants
define('WOOHIPAY_ROOT_FILE', __FILE__);
define('WOOHIPAY_ROOT_PATH', dirname(__FILE__));
define('WOOHIPAY_ROOT_URL', plugins_url('/', __FILE__));
// define('WOOHIPAY_PLUGIN_VERSION', '1.0.0');
define('WOOHIPAY_PLUGIN_VERSION', time());
define('WOOHIPAY_PLUGIN_SLUG', basename(dirname(__FILE__)));
define('WOOHIPAY_PLUGIN_BASE', plugin_basename(__FILE__));
define('WOOHIPAY_PLUGIN_PAGE',admin_url().'admin.php?page=woohipaypro');

function woohipaypro_load_plugin_textdomain() {
    load_plugin_textdomain('woohipaypro', false, basename(dirname(__FILE__)).'/languages');
}
add_action('plugins_loaded', 'woohipaypro_load_plugin_textdomain');

include WOOHIPAY_ROOT_PATH.'/includes/functions.php';

include WOOHIPAY_ROOT_PATH.'/includes/woohipaypro.php';