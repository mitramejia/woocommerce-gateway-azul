<?php
/* =============================================================
//
// Name: settings-azul.php
// -> Description: Settings for Azul Gateway.
//
// Author: mitramejia 
// Created at: 5/2/17
//
// ============================================================= */


if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

return apply_filters( 'wc_azul_settings', array(

    'enabled' => array(
        'title'   => __( 'Activate payments with Azul', 'woocommerce-gateway-azul' ),
        'type'    => 'checkbox',
        'label'   => __( 'Activate payments with Azul', 'woocommerce-gateway-azul' ),
        'default' => 'yes'
    ),

    'test_mode' => array(
        'title'   => __( 'Activate testing mode', 'woocommerce-gateway-azul' ),
        'type'    => 'checkbox',
        'label'   => __( 'Activate testing mode', 'woocommerce-gateway-azul' ),
        'default' => 'yes'
    ),

    'merchant_id' => array(
        'title'   => __( 'Merchant Number', 'woocommerce-gateway-azul' ),
        'type'    => 'text',
        'description' => __( 'Merchant Number assigned by Azul when the affiliation is approved', 'woocommerce-gateway-azul' ),
        'label'   => __( 'Merchant Number assigned by Azul when the affiliation is approved', 'woocommerce-gateway-azul' ),
        'desc_tip' => true,
    ),

    'test_merchant_id' => array(
        'title'   => __( 'Test Merchant Number', 'woocommerce-gateway-azul' ),
        'type'    => 'text',
        'description' => __( 'Merchant Number assigned by Azul for testing', 'woocommerce-gateway-azul' ),
        'label'   => __( 'Merchant Number assigned by Azul for testing', 'woocommerce-gateway-azul' ),
        'desc_tip' => true,
    ),

    'merchant_name' => array(
        'title'   => __( 'Merchant Name', 'woocommerce-gateway-azul' ),
        'type'    => 'text',
        'label'   => __( 'The name of the business', 'woocommerce-gateway-azul' ),
        'default' => get_bloginfo('name'),
    ),

    'merchant_type' => array(
        'title'   => __( 'Merchant Type', 'woocommerce-gateway-azul' ),
        'type'    => 'text',
        'description' => __( 'Merchant type (for informative reasons)', 'woocommerce-gateway-azul' ),
        'label'   => __( 'Merchant type (for informative reasons)', 'woocommerce-gateway-azul' ),
        'default' => get_bloginfo('name'),
        'desc_tip' => true,Ω
    ),
    'response_url'  => array(
    'title'   => __( 'Response Url', 'woocommerce-gateway-azul' ),
    'type'    => 'text',
    'description' => __( 'The Url to send back the user from Azul Payment Page', 'woocommerce-gateway-azul' ),
    'label'   => __( 'Response Url', 'woocommerce-gateway-azul' ),
    'default' => get_site_url().'/',
    'desc_tip' => true,
),
    'currency_code' => array(
        'title'   => __( 'Currency Code. If the bussines is going to charge in Dominican Pesos (DOP) use $', 'woocommerce-gateway-azul' ),
        'type'    => 'text',
        'description' => __( 'Each MID or store trades with a single currency. This value is provided by blue next to the data of access to each environment. If the bussines is going to charge in Dominican Pesos (DOP) use $', 'woocommerce-gateway-azul' ),
        'label'   => __( 'Currency Code', 'woocommerce-gateway-azul' ),
        'default' => '$',
        'desc_tip' => true,
    ),
    'test_auth_key' => array(
        'title'   => __( 'Test Auth Key', 'woocommerce-gateway-azul' ),
        'type'    => 'password',
        'label'   => __( 'Test Auth Key', 'woocommerce-gateway-azul' ),
        'description' => __( 'An Auth key to be used in testing environment. This key should only work at pruebas.azul.com.do. This Test Auth key should be given to you by a bank representative.', 'woocommerce-gateway-azul' ),
        'desc_tip'    => true,
    ),
    'auth_key' => array(
        'title'   => __( 'Auth Key', 'woocommerce-gateway-azul' ),
        'type'    => 'password',
        'label'   => __( 'Auth Key', 'woocommerce-gateway-azul' ),
        'description' => __( 'Your Auth key should be given to you by a bank representative.', 'woocommerce-gateway-azul' ),
        'desc_tip'    => true,
    ),

    'title' => array(
        'title'       => __( 'Title', 'woocommerce-gateway-azul' ),
        'type'        => 'text',
        'description' => __( 'This controls the title for the payment method the customer sees during checkout.', 'woocommerce-gateway-azul' ),
        'default'     => __( 'Payment with Azul from Banco Popular', 'woocommerce-gateway-azul' ),
        'desc_tip'    => true,
    ),

    'description' => array(
        'title'       => __( 'Description', 'woocommerce-gateway-azul' ),
        'type'        => 'textarea',
        'description' => __( 'Payment method description that the customer will see on your checkout.', 'woocommerce-gateway-azul' ),
        'default'     => __( 'Use the Azul Payment Page to pay your reservation', 'woocommerce-gateway-azul' ),
        'desc_tip'    => true,
    ),

    'instructions' => array(
        'title'       => __( 'Instructions', 'woocommerce-gateway-azul' ),
        'type'        => 'textarea',
        'description' => __( 'Instructions that will be added to the thank you page and emails.', 'woocommerce-gateway-azul' ),
        'default'     => 'Pay using the Azul Payment page',
        'desc_tip'    => true,
    ),
)) ;