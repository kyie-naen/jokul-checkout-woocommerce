<?php

$sandbox_key_url = 'https://sandbox.doku.com/bo/login';
$production_key_url = 'https://jokul.doku.com/bo/login';
/**
 * Build array of configurations that will be displayed on Admin Panel
 */
return apply_filters(
	'wc_Jokul_settings',
	array(
        'enabled'       => array(
            'title'     => __( 'Enable/Disable', 'jokul-checkout-woo' ),
            'type'      => 'checkbox',
            'label'     => __( 'Enable Jokul Checkout', 'jokul-checkout-woo' ),
            'default'   => 'no'
        ),
        'select_jokul_environment' => array(
          'title'           => __( 'Environment', 'jokul-checkout-woo' ),
          'type'            => 'select',
          'default'         => 'sandbox',
          'description'     => __( 'Select the Jokul Environment', 'jokul-checkout-woo' ),
          'options'         => array(
            'sandbox'           => __( 'Sandbox', 'jokul-checkout-woo' ),
            'production'        => __( 'Production', 'jokul-checkout-woo' ),
          ),
        ),
        'client_id_sandbox'       => array(
            'title'         => __("Client Id - Sandbox", 'jokul-checkout-woo'),
            'type'          => 'text',
            'description'   => sprintf(__('Input your <b>Sandbox</b> Jokul Client Id. Get the key <a href="%s" target="_blank">here</a>', 'jokul-checkout-woo' ),$sandbox_key_url),
            'default'       => ''
        ),
        'secret_key_sandbox'       => array(
            'title'         => __("Secret Key - Sandbox", 'jokul-checkout-woo'),
            'type'          => 'text',
            'description'   => sprintf(__('Input your <b>Sandbox</b> Jokul Secret Key. Get the key <a href="%s" target="_blank">here</a>', 'jokul-checkout-woo' ),$sandbox_key_url),
            'default'       => ''
        ),
        'client_id_production'    => array(
            'title'         => __("Client Id - Production", 'jokul-checkout-woo'),
            'type'          => 'text',
            'description'   => sprintf(__('Input your <b>Production</b> Jokul Client Key. Get the key <a href="%s" target="_blank">here</a>', 'jokul-checkout-woo' ),$production_key_url)
        ),
        'secret_key_production'     => array(
            'title'         => __("Secret Key - Production", 'jokul-checkout-woo'),
            'type'          => 'text',
            'description'   => sprintf(__('Input your <b>Production</b> Jokul Secret Key. Get the key <a href="%s" target="_blank">here</a>', 'jokul-checkout-woo' ),$production_key_url)
        ),
				'expiry_payment'                 => array(
            'title'         => __( 'Expiry Payment', 'jokul-checkout-woo' ),
            'type'          => 'number',
            'description'   => __( 'This will allow you to set custom duration on how long the transaction available to be paid.<br> example: 45', 'jokul-checkout-woo' ),
            'default'       => '60'
        ),
        'label_notification'             => array(
            'title'         => __( 'Notification Jokul URL', 'jokul-checkout-woo' ),
            'type'          => 'title',
            'description'   => __( 'After you have filled required config above, don\'t forget to scroll to bottom and click  <strong>Save Changes</strong> button.','jokul-checkout-woo'),
        ),
				'notify_url_payment'                 => array(
            'title'         => __( 'Notification Jokul Checkout Payment URL', 'jokul-checkout-woo' ),
            'type'          => 'text',
						'custom_attributes' => array('readonly' => 'readonly'),
            'description'   => __( 'You should set this URL on Jokul Dashboard.', 'jokul-checkout-woo' ),
            'default'       => $this->notification_url_payment()
        ),
				'notify_url_qris'                 => array(
            'title'         => __( 'Notification Jokul Checkout QRIS URL', 'jokul-checkout-woo' ),
            'type'          => 'text',
						'custom_attributes' => array('readonly' => 'readonly'),
            'description'   => __( 'You should tell the integration team to set this URL', 'jokul-checkout-woo' ),
            'default'       => $this->notification_url_qris()
        ),
        'label_config_separator'             => array(
            'title'         => __( 'II. Payment Buttons Appereance Section - Optional', 'jokul-checkout-woo' ),
            'type'          => 'title',
            'description'   => __( '-- Configure how the payment button will appear to customer, you can leave them default.','jokul-checkout-woo'),
        ),
        'jokul_title'                     => array(
            'title'         => __( 'Button Title', 'jokul-checkout-woo' ),
            'type'          => 'text',
            'description'   => __( 'This controls the payment label title which the user sees during checkout. <a href="https://github.com/veritrans/SNAP-Woocommerce#configurables"  target="_blank">This support HTML tags</a> like &lt;img&gt; tag, if you want to include images.', 'jokul-checkout-woo' ),
            'default'       => $this->getDefaultTitle(),
        ),
        'jokul_description'               => array(
            'title' => __( 'Button Description', 'jokul-checkout-woo' ),
            'type' => 'textarea',
            'description' => __( 'You can customize here the expanded description which the user sees during checkout when they choose this payment. <a href="https://github.com/veritrans/SNAP-Woocommerce#configurables"  target="_blank">This support HTML tags</a> like &lt;img&gt; tag, if you want to include images.', 'jokul-checkout-woo' ),
            'default'       => $this->getDefaultDescription(),
          )
	)
);
