<?php
/**
 * Plugin Name: AstroPay for WooCommerce
 * Description: Gateway de pago para WooCommerce
 * Version: 2.0.4
 * Requires PHP: 7.0
 * Author: Conexa
 * Author URI: https://conexa.ai
 * Text Domain: astropay
 * WC requires at least: 4
 * WC tested up to: 5.0
 *
 * @package Ecomerciar\AstroPay\AstroPay
 */

use Ecomerciar\AstroPay\Helper\Helper;
use Ecomerciar\AstroPay\Sdk\AstropaySdk;

defined( 'ABSPATH' ) || exit();

add_action( 'plugins_loaded', array( 'AstroPay', 'init' ) );
add_action( 'activated_plugin', array( 'AstroPay', 'activation' ) );

/**
 * Plugin's base Class
 */
class AstroPay {

	const VERSION     = '2.0.4';
	const PLUGIN_NAME = 'AstroPay';
	const MAIN_FILE   = __FILE__;
	const MAIN_DIR    = __DIR__;

	const GATEWAY_ID            = 'wc_astropay';
	const META_ORDER_PAYMENT_ID = '_ASTROPAY_DEPOSIT_ID';
	const META_ORDER_PAYMENT_ID_INTERNAL = '_ASTROPAY_INTERNAL_DEPOSIT_ID';
	const META_ORDER_CASHOUT_ID = '_ASTROPAY_DEPOSIT_ID';
	const META_ORDER_CASHOUT_ID_INTERNAL = '_ASTROPAY_INTERNAL_DEPOSIT_ID';
	const WC_WEBHOOK_URL     = '/wc-api/wc-astropay';
	
	/**
	 * Checks system requirements
	 *
	 * @return bool
	 */
	public static function check_system() {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
		$system = self::check_components();

		if ( $system['flag'] ) {
			deactivate_plugins( plugin_basename( __FILE__ ) );
			echo '<div class="notice notice-error is-dismissible">'
			. '<p>' .
				sprintf(
					__(
						'<strong>%1$s</strong> Requiere al menos %2$s versión %3$s o superior.',
						'astropay'
					),
					self::PLUGIN_NAME,
					$system['flag'],
					$system['version']
				) .
				'</p>'
			. '</div>';
			return false;
		}

		if ( ! class_exists( 'WooCommerce' ) ) {
			deactivate_plugins( plugin_basename( __FILE__ ) );
			echo '<div class="notice notice-error is-dismissible">'
			. '<p>' .
				sprintf(
					__(
						'WooCommerce debe estar activo antes de usar <strong>%s</strong>',
						'astropay'
					),
					self::PLUGIN_NAME
				) .
				'</p>'
			. '</div>';
			return false;
		}
		return true;
	}

	/**
	 * Check the components required for the plugin to work (PHP, WordPress and WooCommerce)
	 *
	 * @return array
	 */
	private static function check_components() {
		global $wp_version;
		$flag = $version = false;

		if ( version_compare( PHP_VERSION, '7.0', '<' ) ) {
			$flag    = 'PHP';
			$version = '7.0';
		} elseif ( version_compare( $wp_version, '5.4', '<' ) ) {
			$flag    = 'WordPress';
			$version = '5.4';
		} elseif (
			! defined( 'WC_VERSION' ) ||
			version_compare( WC_VERSION, '3.8.0', '<' )
		) {
			$flag    = 'WooCommerce';
			$version = '3.8.0';
		}

		return array(
			'flag'    => $flag,
			'version' => $version,
		);
	}

	/**
	 * Print Notices
	 *
	 * @return void
	 */
	public static function print_notices() {
		
	}

	/**
	 * Inits our plugin
	 *
	 * @return void
	 */
	public static function init() {
		if ( ! self::check_system() ) {
			return false;
		}

        spl_autoload_register(
			function ( $class ) {
				// Plugin base Namespace.
				if ( strpos( $class, 'AstroPay' ) === false ) {
					return;
				}
				$class     = str_replace( '\\', '/', $class );
				$parts     = explode( '/', $class );
				$classname = array_pop( $parts );
                if (  $classname === 'AstroPay') {
					return;
				}
				$filename = $classname;
                $filename = str_replace( 'AstroPay', 'Astropay', $filename );
				$filename = str_replace( 'WooCommerce', 'Woocommerce', $filename );
				$filename = str_replace( 'WC_', 'Wc', $filename );
				$filename = str_replace( 'WC', 'Wc', $filename );
				$filename = preg_replace( '/([A-Z])/', '-$1', $filename );
				$filename = 'class' . $filename;
				$filename = strtolower( $filename );
				$folder   = strtolower( array_pop( $parts ) );				
				require_once plugin_dir_path( __FILE__ ) . 'src/' . $folder . '/' . $filename . '.php';
			}
		);        
		include_once __DIR__ . '/hooks.php';
		Helper::init();
		self::load_textdomain();
		self::print_notices();
		return true;
	}

	/**
	 * Create a link to the settings page, in the plugins page
	 *
	 * @param array $links
	 * @return array
	 */
	public static function create_settings_link( array $links ) {
		$link =
			'<a href="' .
			esc_url(
				get_admin_url(
					null,
					'admin.php?page=wc-settings&tab=checkout&section=wc_astropay'
				)
			) .
			'">' .
			__( 'Ajustes', 'astropay' ) .
			'</a>';
		array_unshift( $links, $link );
		return $links;
	}

	/**
	 * Adds our shipping method to WooCommerce
	 *
	 * @param array $shipping_methods
	 * @return array
	 */
	public static function add_payment_method( $gateways ) {
		$gateways[] = '\Ecomerciar\AstroPay\Gateway\WC_AstroPay';
		return $gateways;
	}

	/**
	 * Loads the plugin text domain
	 *
	 * @return void
	 */
	public static function load_textdomain() {
		load_plugin_textdomain(
			'astropay',
			false,
			basename( dirname( __FILE__ ) ) . '/i18n/languages'
		);
	}

	/**
	 * Activation Plugin Actions
	 *
	 * @return void
	 */
	public static function activation( $plugin ) {
		if ( ! class_exists( 'WooCommerce' ) ) {
			return false;
		}
		self::redirect_to_onboarding_on_activation( $plugin );
	}

	/**
	 * Redirects to onboarding page on register_activation_hook
	 */
	public static function redirect_to_onboarding_on_activation( $plugin ) {
		if ( $plugin == plugin_basename( self::MAIN_FILE ) ) {
			exit(
				wp_redirect(
					admin_url(
						'admin.php?page=wc-astropay-onboarding'
					)
				)
			);
		}
		return true;
	}

	

	/**
	 * Registers all scripts to be loaded laters
	 *
	 * @return void
	 */
	/**
	 * Registers all scripts to be loaded laters
	 *
	 * @return void
	 */
	public static function register_admin_scripts() {
		wp_register_style('wc-astropay-onboarding', Helper::get_assets_folder_url() . '/css/onboarding.css');

	}

	/**
	 * Registers all scripts to be loaded laters
	 *
	 * @return void
	 */
	public static function register_front_scripts() {
		wp_register_script(
			'astropay-modal',
            'https://js.astropay.com/v2/sdk.js',
		);
		wp_register_script(
			'astropay-gateway',
			Helper::get_assets_folder_url() . '/js/gateway.js',
			array( 'jquery', 'astropay-modal' )
		);
	}
}
