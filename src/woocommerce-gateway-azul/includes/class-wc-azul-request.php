<?php

/* =============================================================
//
// Name: class-wc-azul-request.php
// -> Description: Constructs the request object
//
// Author: mitramejia 
// Created at: 5/21/17
//
// ============================================================= */

class WC_Azul_Request
{
    /**
     * @var
     */
    protected $order;
    /**
     * Pointer to woocommerce azul gateway.
     *
     * @var WC_Gateway_Azul
     */
    protected $gateway;

    /**
     * WC_Azul_Request constructor.
     *
     * @param $gateway
     * @param $order
     */
    public function __construct($gateway, $order)
    {
        $this->order   = $order;
        $this->gateway = $gateway;
    }

    /**
     * Return all fields required to create the auth hash.
     *
     * @return array
     */
    public function get_auth_hash_args()
    {
        $order        = $this->order;
        $gateway = $this->gateway;
        $order_number = $order->get_order_number();
        $thank_you_page = $this->gateway->get_option('response_url');

        return apply_filters('wc_azul_request', array(
            'MerchantId'        => $gateway->merchant_id,
            'MerchantName'      => $gateway->merchant_name,
            'MerchantType'      => $gateway->merchant_type,
            'CurrencyCode'      => $gateway->currency_code,
            'OrderNumber'       => $order_number,
            'Amount'            => $order->order_total * 100,
            'ITBIS'             => $order->order_tax * 100,
            'ApprovedUrl'       => $thank_you_page . '?action=Approved',
            'DeclinedUrl'       => $thank_you_page . '?action=Declined',
            'CancelUrl'         => $thank_you_page . '?action=Cancelled&OrderNumber=' . $order_number,
            'UseCustomField1'   => '0',
            'CustomField1Label' => '',
            'CustomField1Value' => '',
            'UseCustomField2'   => '0',
            'CustomField2Label' => '',
            'CustomField2Value' => '',
            'AuthKey'           => WC_Azul_API::get_auth_key()
        ));

    }

    public function get_azul_thank_you_page_url()
    {
        $thank_you_page_url = get_permalink(get_page_by_title('Gracias Por Reservar'));
        $home_page_url      = get_site_url();
        if (isset($thank_you_page_url)) {
            return $thank_you_page_url;
        } else {
            return $home_page_url;
        }
    }

    public function get_request_form_args()
    {
        $azul_args = $this->get_auth_hash_args();

        // Add hashed values as AuthHash
        $azul_args['AuthHash'] = WC_Azul_API::generate_hash($azul_args);
        // Add the action url to be redirected to
        $azul_args['FormAction'] = WC_Azul_API::get_action_url();
        // Remove AuthKey from array before sending data to the form template
        if (isset($azul_args['AuthKey'])) {
            unset($azul_args['AuthKey']);
        };

        return $azul_args;
    }

}