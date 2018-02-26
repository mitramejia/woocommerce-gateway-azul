<?php

// =============================================================
//
// Name: class-wc-azul-response
// -> Description: Handles the response object from azul
//
// Author: mitramejia 
// Created at: 5/21/17
//
// =============================================================


class WC_Azul_Response
{
    /**
     * @var
     */
    protected $order;

    /**
     * WC_Azul_Response constructor.
     *
     * @param $order
     */
    public function __construct($order)
    {
        $this->order = $order;
    }

    /**
     * @return array
     */
    public function get_response_args()
    {
        return apply_filters('wc_azul_response', array(
            'OrderNumber'       => $_GET['OrderNumber'],
            'Amount'            => $_GET['Amount'],
            'AuthorizationCode' => $_GET['AuthorizationCode'],
            'DateTime'          => $_GET['DateTime'],
            'ResponseCode'      => $_GET['ResponseCode'],
            'IsoCode'           => $_GET['IsoCode'],
            'ResponseMessage'   => $_GET['ResponseMessage'],
            'ErrorDescription'  => $_GET['ErrorDescription'],
            'RRN'               => $_GET['RRN'],
            'AuthKey'           => WC_Azul_API::get_auth_key()
        ));
    }


    /**
     * @param $response_hash
     *
     * @return bool
     */
    private function verify_response_order_number()
    {
        $response_order_number = $_GET['OrderNumber'];
        $current_order_number  = $this->order->get_order_number();
        if ($response_order_number == $current_order_number) {
            return true;
        } else {
            return false;
        }
    }


}