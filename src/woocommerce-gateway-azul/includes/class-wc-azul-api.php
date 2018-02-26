<?php

// =============================================================
//
// Name: WC_Azul_API
// -> Description: 
//
// Author: mitramejia 
// Created at: 5/3/17
//
// =============================================================

class WC_Azul_API {
	/**
	 * Secret API Key.
	 *
	 * @var string
	 */
	private static $auth_key;

	/**
	 * Azul test environment url.
	 *
	 * @var string
	 */
	const TEST_ACTION_URL = 'https://pruebas.azul.com.do/PaymentPage/';

	/**
	 * Azul alternate production environment url. If the production url is not available.
	 * This one should be used instead
	 *
	 * @var string
	 */
	const ALTERNATE_PRODUCTION_ACTION_URL = 'https://contpagos.azul.com.do/PaymentPage/';

	/**
	 * Azul production environment url.
	 *
	 * @var string
	 */
	const PRODUCTION_ACTION_URL = 'https://pagos.azul.com.do/PaymentPage/';


	public static function get_action_url() {
		$options = get_option( 'woocommerce_azul_settings' );
		if ( $options['test_mode'] == 'yes' ) {
			return self::TEST_ACTION_URL;
		} else {
			return self::get_production_action_url();
		}
	}

	/**
	 *  Returns the production webservice url if available,
	 *  if not return the alternate production web service url
	 *
	 * @return string
	 */
	public static function get_production_action_url() {
		if ( self::is_available( self::PRODUCTION_ACTION_URL ) ) {
			return self::PRODUCTION_ACTION_URL;
		} else {
			return self::ALTERNATE_PRODUCTION_ACTION_URL;
		}
	}

	/**
	 * Checks if a domain is available
	 *
	 * @param string $domain
	 *
	 * @return boolean
	 */
	public static function is_available( $domain ) {
		$curl_init = curl_init( $domain );
		curl_setopt( $curl_init, CURLOPT_CONNECTTIMEOUT, 10 );
		curl_setopt( $curl_init, CURLOPT_HEADER, true );
		curl_setopt( $curl_init, CURLOPT_NOBODY, true );
		curl_setopt( $curl_init, CURLOPT_RETURNTRANSFER, true );

		//get answer
		$response = curl_exec( $curl_init );

		curl_close( $curl_init );
		if ( $response ) {
			return true;
		}

		return false;
	}

	/**
	 * Set secret API Key.
	 *
	 * @param string $auth_key
	 */
	public static function set_auth_key( $auth_key ) {
		self::$auth_key = $auth_key;
	}

	/**
	 * Get secret key.
	 *
	 * @return string
	 */
	public static function get_auth_key() {
		if ( ! self::$auth_key ) {
			$options = get_option( 'woocommerce_azul_settings' );
			// If test mode is enabled use the test auth key
			if ( $options['test_mode'] == 'yes' ) {
				if ( isset( $options['test_auth_key'] ) ) {
					self::set_auth_key( $options['test_auth_key'] );
				}
			} else {
				if ( isset( $options['auth_key'] ) ) {
					self::set_auth_key( $options['auth_key'] );
				}
			}
		}

		return self::$auth_key;
	}


	/**
	 * Takes an array of values and hashes them using SHA512 and UNICODE
	 *
	 * @param array $args
	 *
	 * @return string
	 */
	public static function generate_hash( $args ) {
		$response_string = implode( $args );
		mb_convert_encoding( $response_string, 'UTF-16LE', 'ASCII' );

		return hash( 'sha512', $response_string );
	}

}