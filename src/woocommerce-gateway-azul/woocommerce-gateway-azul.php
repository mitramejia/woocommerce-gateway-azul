<?php
/**
 * Plugin Name:       Azul Payment Gateway for WooCommerce
 * Plugin URI:        https://github.com/mitramejia/woocommerce-gateway-azul
 * Description:       Payment gateway for Azul (https://www.azul.com.do)
 * WooCommerce. Version:           0.1.0 Author:            Mitra Mejía Author URI:        https://mitramejia.com
 * Requires at least: 4.2 Tested up to:      4.7.2 Text Domain:       city-fc Domain Path:       languages Network:
 *       false GitHub Plugin URI: https://github.com/mitramejia/woocommerce-gateway-azul Azul Standard Payment Gateway.
 *
 * @class          WC_Azul
 * @extends        WC_Payment_Gateway
 * @version        1.0.0
 * @author         Mitra Mejía
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WC_Azul' ) ) :

	$plugin_url  = untrailingslashit( plugins_url( basename( plugin_dir_path( __FILE__ ) ), basename( __FILE__ ) ) );
	$plugin_path = untrailingslashit( plugin_dir_path( __FILE__ ) );

	/**
	 * Required minimums and constants
	 */

	define( 'WC_AZUL_VERSION', '1.0.0' );
	define( 'WC_AZUL_MIN_PHP_VER', '5.5' );
	define( 'WC_AZUL_MIN_WC_VER', '1.0.0' );
	define( 'WC_AZUL_MAIN_FILE', __FILE__ );
	define( 'WC_AZUL_PLUGIN_URL', $plugin_url );
	define( 'WC_AZUL_PLUGIN_PATH', $plugin_path );

	class WC_Azul {
		/**
		 * @var Singleton The reference the *Singleton* instance of this class
		 */
		private static $instance;
		/**
		 * @var Reference to logging class.
		 */
		private static $log;

		/**
		 * Returns the *Singleton* instance of this class.
		 *
		 * @return Singleton The *Singleton* instance.
		 */
		public static function get_instance() {
			if ( null === self::$instance ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Notices (array)
		 *
		 * @var array
		 */
		public $notices = array();

		protected function __construct() {
			// Add Azul Payment Gateway to WooCommerce
			add_action( 'admin_init', array( $this, 'check_environment' ) );
			add_action( 'admin_notices', array( $this, 'admin_notices' ), 15 );
			add_action( 'plugins_loaded', array( $this, 'init' ) );

		}

		/**
		 * Init the plugin after plugins_loaded so environment variables are set.
		 */
		public function init() {
			// Don't hook anything else in the plugin if we're in an incompatible environment
			if ( self::get_environment_warning() ) {
				return;
			}

			// Load plugin text domain
			load_plugin_textdomain( 'woocommerce-gateway-azul', false,
				plugin_basename( dirname( __FILE__ ) ) . '/languages' );


			// Add settings link to plugin list in admin
			add_filter( 'plugin_action_links_' . plugin_basename( WC_AZUL_MAIN_FILE ),
				array( $this, 'add_settings_link' ) );

			// Add dependencies
			include_once( dirname( __FILE__ ) . '/includes/class-wc-gateway-azul.php' );
			include_once( dirname( __FILE__ ) . '/includes/class-wc-azul-api.php' );
			include_once( dirname( __FILE__ ) . '/includes/class-wc-azul-request.php' );
			include_once( dirname( __FILE__ ) . '/includes/class-wc-azul-response.php' );

			// Initialize the gateway itself
			add_filter( 'woocommerce_payment_gateways', function ( $gateways ) {
				$gateways[] = 'WC_Gateway_Azul';

				return $gateways;
			} );
		}

		/**
		 * Private clone method to prevent cloning of the instance of the
		 * *Singleton* instance.
		 *
		 * @return void
		 */
		private function __clone() {
		}

		/**
		 * Private unserialize method to prevent unserializing of the *Singleton*
		 * instance.
		 *
		 * @return void
		 */
		private function __wakeup() {
		}

		/**
		 * The backup sanity check, in case the plugin is activated in a weird way,
		 * or the environment changes after activation. Also handles upgrade routines.
		 */
		public function check_environment() {

			$environment_warning = self::get_environment_warning();
			if ( $environment_warning && is_plugin_active( plugin_basename( __FILE__ ) ) ) {
				$this->add_admin_notice( 'bad_environment', 'error', $environment_warning );
			}
			if ( ! class_exists( 'WC_Azul_API' ) ) {
				include_once( dirname( __FILE__ ) . '/includes/class-wc-azul-api.php' );
			}
			$secret = WC_Azul_API::get_auth_key();
			if ( empty( $secret ) && ! ( isset( $_GET['page'], $_GET['section'] ) && 'wc-settings' === $_GET['page'] && 'azul' === $_GET['section'] ) ) {
				$setting_link = $this->get_setting_link();
				$this->add_admin_notice( 'prompt_connect', 'notice notice-warning',
					sprintf( __( 'Azul is almost ready. To get started, <a href="%s">set your Azul auth key</a>.',
						'woocommerce-gateway-azul' ), $setting_link ) );
			}
		}

		/**
		 * Checks the environment for compatibility problems.  Returns a string with the first incompatibility
		 * found or false if the environment has no problems.
		 */
		static function get_environment_warning() {
			if ( version_compare( phpversion(), WC_AZUL_MIN_PHP_VER, '<' ) ) {
				$message = __( 'WooCommerce Azul - The minimum PHP version required for this plugin is %1$s. You are running %2$s.',
					'woocommerce-gateway-azul' );

				return sprintf( $message, WC_AZUL_MIN_PHP_VER, phpversion() );
			}

			if ( ! defined( 'WC_VERSION' ) ) {
				return __( 'WooCommerce Azul requires WooCommerce to be activated to work.',
					'woocommerce-gateway-azul' );
			}

			if ( version_compare( WC_VERSION, WC_AZUL_MIN_WC_VER, '<' ) ) {
				$message = __( 'WooCommerce Azul - The minimum WooCommerce version required for this plugin is %1$s. You are running %2$s.',
					'woocommerce-gateway-azul' );

				return sprintf( $message, WC_AZUL_MIN_WC_VER, WC_VERSION );
			}

			if ( ! function_exists( 'curl_init' ) ) {
				return __( 'WooCommerce Azul - cURL is not installed.', 'woocommerce-gateway-azul' );
			}

			return false;
		}


		/**
		 * Display any notices we've collected thus far (e.g. for connection, disconnection)
		 */
		public function admin_notices() {
			foreach ( (array) $this->notices as $notice_key => $notice ) {
				echo "<div class='" . esc_attr( $notice['class'] ) . "'><p>";
				echo wp_kses( $notice['message'], array( 'a' => array( 'href' => array() ) ) );
				echo '</p></div>';
			}
		}

		/**
		 * Allow this class and other classes to add slug keyed notices (to avoid duplication)
		 */
		public function add_admin_notice( $slug, $class, $message ) {
			$this->notices[ $slug ] = array(
				'class'   => $class,
				'message' => $message,
			);
		}

		/**
		 * Get setting link.
		 *
		 * @since 1.0.0
		 * @return string Setting link
		 */
		public function get_setting_link() {
			$use_id_as_section = function_exists( 'WC' ) ? version_compare( WC()->version, '2.6', '>=' ) : false;
			$section_slug      = $use_id_as_section ? 'azul' : strtolower( 'WC_Gateway_Azul' );

			return admin_url( 'admin.php?page=wc-settings&tab=checkout&section=' . $section_slug );
		}


		/**
		 * Add setting link to plugin list in admin.
		 *
		 * @since 1.0.0
		 * @return array of links
		 */
		public function add_settings_link( $links ) {
			// Insert the link at the beginning
			$settings_link = '<a href="' . $this->get_setting_link() . '">' . __( 'Settings',
					'woocommerce-gateway-azul' ) . '</a>';
			array_unshift( $links, $settings_link );

			return $links;
		}


		/**
		 * Get the plugin path.
		 *
		 * @return string
		 */
		public static function plugin_frontend_path() {
			return untrailingslashit( plugins_url( '/', __FILE__ ) );
		}


		/**
		 * Render a php template
		 *
		 * @param $template_name
		 * @param array $template_data
		 * @param bool $output
		 *
		 * @return string|void
		 */
		public static function get_template(
			$template_name,
			$template_data = array(),
			$output = true
		) {
			$templatePath = null;
			try {
				if ( ! $output ) {
					ob_start();
				}

				if ( $template_data and is_array( $template_data ) ) {
					extract( $template_data );
				}

				if ( $template_data and is_object( $template_data ) ) {
					extract( (array) $template_data );
				}

				$template_path = WC_AZUL_PLUGIN_PATH . '/templates/' . $template_name . '.php';

				include( $template_path );
				if ( ! $output ) {
					return ob_get_clean();
				}
			} catch ( \Throwable $error ) {
				$template = $template_path . $template_name;
				$message  = sprintf( 'Trying to include %s  Error: %s ', $template, $error->getMessage() );
				trigger_error( $message, E_USER_NOTICE );
			}
		}

		/**
		 * What rolls down stairs
		 * alone or in pairs,
		 * and over your neighbor's dog?
		 * What's great for a snack,
		 * And fits on your back?
		 * It's log, log, log
		 */
		public static function log( $message ) {
			if ( empty( self::$log ) ) {
				self::$log = new WC_Logger();
			}
			self::$log->add( 'woocommerce-gateway-azul', $message );
		}

	} //end WC_Gateway_Azul class

endif;

$GLOBALS['wc_azul'] = WC_Azul::get_instance();
