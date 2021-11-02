<?php

require_once dirname( __FILE__ ) . '../../Module/JokulCheckoutModule.php';

class WC_Jokul_Checkout_Gateway extends WC_Payment_Gateway
{
    /**
      * Contruct of jokul checkout
      */
    public function __construct()
    {

        $this->init_form_fields();
        $this->id                   = 'jokul-checkout';
        $this->has_fields           = true;
        $this->method_title         = __( $this->pluginTitle(), 'jokul-checkout-woo' );
        $this->method_description   = $this->getDefaultDescription();

        $this->init_settings();

        // Get Settings
        $this->enabled = $this->get_option('enabled');
        $this->title              = $this->get_option( 'jokul_title' );
        $this->description        = $this->get_option( 'jokul_description' );
        $this->environmentJC      = $this->get_option( 'select_jokul_environment' );
        $this->clientidJC          = ($this->environmentJC == 'production') ? $this->get_option( 'client_id_production' ) : $this->get_option( 'client_id_sandbox' );
        $this->secretkeyJC         = ($this->environmentJC == 'production') ? $this->get_option( 'secret_key_production' ) : $this->get_option( 'secret_key_sandbox' );
        $this->expirypaymentJC     = $this->get_option( 'expiry_payment' );
        // $this->emailNotifications = $this->get_option('email_notifications');

        add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
        // Hook for adding JS script to admin config page
        add_action( 'wp_enqueue_scripts', array( &$this, 'jokul_checkout_admin_scripts' ));
        // Payment page show
        add_action( 'woocommerce_receipt_' . $this->id, array( $this, 'receipt_page' ) );
        // order status hook
        add_action( 'woocommerce_notif_jokul_' . $this->id, array( $this, 'jokul_checkout_callback' ) );
    }

    /**
     * Enqueue Javascripts
     * Add JS script file to Worpress Header
     */
    public function jokul_checkout_admin_scripts() {
      wp_enqueue_script( 'jokul-checkout', 'https://jokul.doku.com/jokul-checkout-js/v1/jokul-checkout-1.0.0.js' );
    }

    /**
     * Get details of Form Fields
     */
    public function init_form_fields()
    {
        $this->form_fields = require('JCFormFields.php');
    }

    /**
     * Admin Panel Options
     * See definition on the extended abstract class
     * @access public
     * @return void
     */
    public function admin_options() { ?>
      <h3><?php _e( $this->pluginTitle(), 'jokul-checkout-woo' ); ?></h3>
      <p><?php _e($this->getDefaultDescription(), 'jokul-checkout-woo' ); ?></p>
      <table class="form-table">
        <?php
          // Generate the HTML For the settings form. generated from `init_form_fields`
          $this->generate_settings_html();
        ?>
      </table><!--/.form-table-->
      <?php
    }

    /**
     * Process Payment
     * @return payment
     */
    public function process_payment($order_id)
    {
        global $woocommerce;
        $order  = wc_get_order($order_id);
        $amount = $order->order_total;
        $lineItem = array();

        foreach ($order->get_items() as $item_id => $item ) {
            $_product = wc_get_product($item->get_product_id());
            $Price = $_product->get_price();

            $lineItem[] = array('name' => $item->get_name(), 'price' => $Price, 'quantity' => $item->get_quantity());
        }

        $params = array(
            'customerId' => 0 !== $order->get_customer_id() ? $order->get_customer_id() : null,
            'customerEmail' => $order->get_billing_email(),
            'customerName' => $order->get_billing_first_name()." ".$order->get_billing_last_name(),
            'amount' => $amount,
            'invoiceNumber' => $order->get_order_number(),
            'expiryTime' => $this->expirypaymentJC,
            'phone' => $order->billing_phone,
            'country' => $order->billing_country,
            'address' => $order->shipping_address_1,
            'lineItem' => $lineItem,
            'callbackUrl' => $this->get_return_url($order)
        );

        $config = array(
            'client_id' => $this->clientidJC,
            'shared_key' => $this->secretkeyJC,
            'environment' => $this->environmentJC
        );
        $response = JokulCheckoutPayment::hitApi($config, $params);
        if( !is_wp_error( $response )) {
            if ($response['message'][0] == 'SUCCESS') {
                update_post_meta('12', 'JCPage', $response['response']['payment']['url']);
                return array(
                    'result' => 'success',
                    'redirect' => $order->get_checkout_payment_url( true )
                );
            } else {
              wc_add_notice( __('Payment error:', 'woothemes') . 'Gagal 1', 'error' );
              return;
            }
        } else {
            wc_add_notice('Gagal 2.', 'error');
        }
    }

    public function process_admin_options()
    {
        $this->init_settings();

        $post_data = $this->get_post_data();

        foreach ($this->get_form_fields() as $key => $field) {
            if ('title' !== $this->get_field_type($field)) {
                try {
                    if ('expiry_payment' == $key && $post_data['woocommerce_' . $this->id . '_expiry_payment'] == null) {
                        $this->settings[$key] = $this->get_field_default($field);
                    } else {
                        $this->settings[$key] = $this->get_field_value($key, $field, $post_data);
                    }
                } catch (Exception $e) {
                    $this->add_error($e->getMessage());
                }
            }
        }

        return update_option($this->get_option_key(), apply_filters('woocommerce_settings_api_sanitized_fields_' . $this->id, $this->settings), 'yes');
    }

    /**
     * Hook function that will be auto-called by WC on receipt page
     * Output HTML for Snap payment page.
     * @param  string $order_id generated by WC
     * @return string HTML
     */
     function receipt_page( $order_id ) {
         global $woocommerce;
         // Separated as Shared PHP included by multiple class
         require_once(dirname(__FILE__) . '../../Module/JokulPaymentPage.php');
     }

     /**
      * Hook function that when Jokul send parameter result payment to Woocommerce
      * Output HTML for Snap payment page. Including `snap.pay()` part
      * @return string Update status Woocommerce
      */
      function jokul_checkout_callback() {
          // Separated as Shared PHP included by multiple class
          require_once(dirname(__FILE__) . '../../Module/JokulNotify.php');

      }

    /**
     * @return string
     */
    public function pluginTitle() {
      return "Jokul Checkout";
    }

    /**
     * @return string
     */
    protected function getDefaultTitle() {
      return __('All Supported Payment', 'jokul-checkout-woo');
    }

    /**
     * @return string
     */
    protected function getDefaultDescription() {
      return __('Accept all various supported payment methods. Choose your preferred payment on the next page. Secure payment via Jokul.', 'jokul-checkout-woo');
    }

    /**
     * @return string The main gateway plugin's Notification Payment URL that will be displayed to config page
     */
    public function notification_url_payment(){
      return add_query_arg( array('wc-api' => 'WC_Gateway_Jokul_Checkout',
        'notif' => 'payment'),
        home_url( '/' )
      );
    }

    /**
     * @return string The main gateway plugin's Notification Qris URL that will be displayed to config page
     */
    public function notification_url_qris(){
      return add_query_arg( array('wc-api' => 'WC_Gateway_Jokul_Checkout',
        'notif' => 'qris'),
        home_url( '/' )
      );
    }
}
