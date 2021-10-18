<?php

/**
 * Plugin Name: Jokul Checkout Woocommerce
 * Author Name: Rizky Zulkarnaen
 * Description: Simple plugin for jokul checkout
 */

// Validate plugin woocommerce is active
if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) return;


add_action('plugins_loaded', 'jokul_checkout_init', 0);

function jokul_checkout_init(){
  if (!class_exists('WC_Payment_Gateway') ) {
    return;
  }

  //Jokul Checkout Main
  require_once dirname( __FILE__ ) . '/Init/MainInit.php';

  add_filter('woocommerce_payment_gateways', 'jokul_checkout_add_payment_gateway');
}

// add jokul checkout as payment channel
function jokul_checkout_add_payment_gateway($gateways){
  $gateways[] = 'WC_Jokul_Checkout_Gateway';
  return $gateways;
}
