<?php

// =============================================================
//
// Name: WC_Azul_Payment_Form
// -> Description: 
//
// Author: mitramejia 
// Created at: 4/21/17
//
// =============================================================


class WC_Gateway_Azul extends WC_Payment_Gateway {

	/**
	 * @var string
	 */
	private $auth_key;
	/**
	 * @var bool
	 */
	public $test_mode;
	/**
	 * @var string
	 */
	public $merchant_id;
	/**
	 * @var string
	 */
	public $merchant_name;
	/**
	 * @var string
	 */
	public $merchant_type;
	/**
	 * @var string
	 */
	public $currency_code;

	private $response;

	private $request;

	/**
	 * WC_Gateway_Azul constructor.
	 */
	public function __construct() {
		$this->id                 = 'azul';
		$this->icon               = WC_AZUL_PLUGIN_URL . '/assets/images/logo_azul.png';
		$this->has_fields         = false;
		$this->order_button_text  = __( 'Next', 'woocommerce-gateway-azul' );
		$this->method_title       = __( 'Azul', 'woocommerce-gateway-azul' );
		$this->method_description = sprintf( __( 'Use the Azul Payment Page.',
			'woocommerce-gateway-azul' ),
			admin_url( 'admin.php?page=wc-status' ) );

		// Load the form fields.
		$this->init_form_fields();

		// Load the settings.
		$this->init_settings();

		// Add Azul settings
		$this->enabled       = $this->get_option( 'enabled' );
		$this->test_mode     = 'yes' === $this->get_option( 'test_mode' );
		$this->auth_key      = $this->test_mode ? $this->get_option( 'test_auth_key' ) : $this->get_option( 'auth_key' );
		$this->title         = $this->get_option( 'title' );
		$this->description   = $this->get_option( 'description' );
		$this->merchant_id   = $this->test_mode ? $this->get_option( 'test_merchant_id' ) : $this->get_option( 'merchant_id' );
		$this->merchant_name = $this->get_option( 'merchant_name' );
		$this->merchant_type = $this->get_option( 'merchant_type' );
		$this->currency_code = $this->get_option( 'currency_code' );

		$this->set_test_mode_description();
		// Set private key
		WC_Azul_API::set_auth_key( $this->auth_key );

		// Save settings
		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id,
			array( $this, 'process_admin_options' ) );

		// When the user hits the thank you page, add azul form and submission button
		add_action( 'woocommerce_thankyou_' . $this->id, array( $this, 'generate_payment_form' ), 20 );

		add_filter( 'woocommerce_thankyou_order_received_text', array( $this, 'remove_default_order_received_text' ) );
	}

	private function set_test_mode_description() {
		if ( $this->test_mode ) {
			$this->description .= ' ' . __( 'Test Mode Enabled.',
					'woocommerce-gateway-azul' );
			$this->description = trim( $this->description );
		}
	}


	/**
	 * Initialize Gateway Settings Form Fields
	 */
	public function init_form_fields() {
		$this->form_fields = include( WC_AZUL_PLUGIN_PATH . '/includes/admin/settings-azul.php' );
	}


	/**
	 * @param int $order_id
	 *
	 * @return array
	 */
	public function process_payment( $order_id ) {
		$order = new WC_Order( $order_id );

		// Mark as on-hold (we're awaiting the cheque)
		$order->update_status( 'on-hold', __( 'Awaiting azul payment', 'woocommerce-azul-gateway' ) );

		// Reduce stock levels
		$order->reduce_order_stock();
		// Remove cart
		WC()->cart->empty_cart();

		// Return thankyou redirect
		return array(
			'result'   => 'success',
			'redirect' => $this->get_return_url( $order )
		);
	}


	/**
	 * @param int $order_id
	 */
	public function generate_payment_form( $order_id ) {
		$order             = new WC_Order( $order_id );
		$azul              = new WC_Azul_Request( $this, $order );
		$request_form_args = $azul->get_request_form_args();

		// Send form fields to the form template
		WC_Azul::log( 'Generating payment form for order: ' . $order->get_order_number() . ': ' . print_r( $request_form_args,
				true ) );
		// Render the payment form
		if ( $_GET['action'] != 'Approved' ) {
			WC_Azul::get_template( 'wc-azul-payment-form', $request_form_args );
		}
	}

	public function remove_default_order_received_text() {
		return '';
	}

}