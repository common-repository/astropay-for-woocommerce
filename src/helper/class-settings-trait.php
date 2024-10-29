<?php
/**
 * Class SettingsTrait
 *
 * @package  Ecomerciar\AstroPay\Helper\SettingsTrait
 */

namespace Ecomerciar\AstroPay\Helper;

use Ecomerciar\AstroPay\Sdk\AstroPaySdk;
/**
 * Settings Trait
 */
trait SettingsTrait {

	/**
	 * Gets a plugin option
	 *
	 * @param string  $key Key value searching for.
	 * @param boolean $default A dafault value in case Key is not founded.
	 * 
	 * @return mixed
	 */
	public static function get_option( string $key, $default = false ) {
		return isset( self::get_options()[ $key ] ) &&
			! empty( self::get_options()[ $key ] )
			? self::get_options()[ $key ]
			: $default;
	}

	/**
	 * Get options
	 *
	 * @param string  $gateway Gateway Name.
	 * 
	 * @return Array
	 */
	public static function get_options( $gateway = 'wc_astropay' ) {
		$option = get_option( 'woocommerce_' . $gateway . '_settings' );
		return array(
			'enabled'      => isset( $option['enabled'] ) ? $option['enabled'] : 'no',
            'title'        => isset( $option['title'] ) ? $option['title'] : __('Pay with AstroPay', 'astropay'),
            'description'  => isset( $option['description'] ) ? $option['description'] : __( 'AstroPay offers multiple payment methods.' , 'astropay'),
			'api_key'     => isset( $option['wc_astropay_api_key'] )
				? $option['wc_astropay_api_key']
				: '',
			'api_secret' => isset( $option['wc_astropay_api_secret'] )
				? $option['wc_astropay_api_secret']
				: '',
			'sdk_api_key'      => isset( $option['wc_astropay_sdk_api_key'] )
				? $option['wc_astropay_sdk_api_key']
				: '',
			'api_key_europe'     => isset( $option['wc_astropay_api_key_europe'] )
				? $option['wc_astropay_api_key_europe']
				: '',
			'api_secret_europe' => isset( $option['wc_astropay_api_secret_europe'] )
				? $option['wc_astropay_api_secret_europe']
				: '',
			'sdk_api_key_europe'      => isset( $option['wc_astropay_sdk_api_key_europe'] )
				? $option['wc_astropay_sdk_api_key_europe']
				: '',
				
			'api_key_sandbox'     => isset( $option['wc_astropay_api_key_sandbox'] )
				? $option['wc_astropay_api_key_sandbox']
				: '',
			'api_secret_sandbox' => isset( $option['wc_astropay_api_secret_sandbox'] )
				? $option['wc_astropay_api_secret_sandbox']
				: '',
			'sdk_api_key_sandbox'      => isset( $option['wc_astropay_sdk_api_key_sandbox'] )
				? $option['wc_astropay_sdk_api_key_sandbox']
				: '',
			'api_key_europe_sandbox'     => isset( $option['wc_astropay_api_key_europe_sandbox'] )
				? $option['wc_astropay_api_key_europe_sandbox']
				: '',
			'api_secret_europe_sandbox' => isset( $option['wc_astropay_api_secret_europe_sandbox'] )
				? $option['wc_astropay_api_secret_europe_sandbox']
				: '',
			'sdk_api_key_europe_sandbox'      => isset( $option['wc_astropay_sdk_api_key_europe_sandbox'] )
				? $option['wc_astropay_sdk_api_key_europe_sandbox']
				: '',

			'pay_by_astropay'        => isset( $option['wc_astropay_pay_by_astropay_enabled'] )
				? $option['wc_astropay_pay_by_astropay_enabled']
				: 'no',
			'environment_mode'    => isset( $option['wc_astropay_environment_mode'] )
            ? $option['wc_astropay_environment_mode']
            : 'sandbox',
			'debug'        => isset( $option['wc_astropay_log_enabled'] )
				? $option['wc_astropay_log_enabled']
				: 'no',
			/*'merchant_category_code' => isset($option['wc_astropay_merchant_category_code'])
				? $option['wc_astropay_merchant_category_code']
				: ' ',*/
		);
	}

	/**
	 * Set options
	 *
	 * @param string  $gateway Gateway Name.
	 * 
	 * @return Array
	 */
	public static function set_option(string $key, string $value, string $gateway = 'wc_astropay' ) {
		$option = get_option( 'woocommerce_' . $gateway . '_settings' );
		$option['wc_astropay_'. $key ] = $value;
		update_option('woocommerce_' . $gateway . '_settings', $option);
	}

    /**
	 * Is Sandbox?
	 *
	 * @param string  $gateway Gateway Name.
	 * 
	 * @return Array
	 */
    public static function is_sandbox( $gateway = 'wc_astropay' ) {
        return ('sandbox'=== self::get_options($gateway)['environment_mode']);
    }

	/**
	 * Get SDK
	 */
	public static function get_sdk_for_order_id($order_id) {
		$order    = wc_get_order( $order_id );

		$options = Helper::get_options(\AstroPay::GATEWAY_ID);
		$isEurope = Helper::is_europe_country($order->get_billing_country());
		if (Helper::is_sandbox()){
			$use_europe = (!empty($options['api_key_europe_sandbox']) || !empty($options['api_secret_europe_sandbox'])  || !empty($options['sdk_api_key_europe_sandbox']));
			if($isEurope && $use_europe){
				$sdk = new AstroPaySdk($options['api_key_europe_sandbox'], $options['api_secret_europe_sandbox'], $options['sdk_api_key_europe_sandbox']);				
			} else {
				$sdk = new AstroPaySdk($options['api_key_sandbox'], $options['api_secret_sandbox'], $options['sdk_api_key_sandbox']);
			}					
		} else {
			$use_europe = (!empty($options['api_key_europe'])  || !empty($options['api_secret_europe'])  || !empty($options['sdk_api_key_europe']));
			if($isEurope && $use_europe){
				$sdk = new AstroPaySdk($options['api_key_europe'], $options['api_secret_europe'], $options['sdk_api_key_europe']);
			} else {
				$sdk = new AstroPaySdk($options['api_key'], $options['api_secret'], $options['sdk_api_key']);
			}								
		}
		return $sdk;
	}
}
