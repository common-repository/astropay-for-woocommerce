<?php
/**
 * Class Request Deposit Action
 *
 * @package  Ecomerciar\AstroPay\Gateway\RequestDepositAction
 */

namespace Ecomerciar\AstroPay\Gateway;

use Ecomerciar\AstroPay\Helper\Helper;
use Ecomerciar\AstroPay\Sdk\AstroPaySdk;
use Ecomerciar\AstroPay\Gateway\WC_AstroPay;
/**
 * Orders Base Action Class
 */
abstract class RequestDepositAction {

	/**
	 * Run Action
	 *
	 * @param int $order_id ID for WC Order.
	 *
	 * @return array
	 */
	public static function run( $order_id ) {
		$order = wc_get_order( $order_id );
		
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
		$options = [
			'pbap' => isset($_POST['pbap'])? $_POST['pbap'] : '',
		];
		$response = $sdk->request_deposit( $order_id, $options);
		
		if ( isset( $response['status'] ) && 'PENDING' === $response['status'] ) {
			$order->add_order_note(
				sprintf(
					__(
						'AstroPay deposit created. ID %s',
						'astropay'
					),
					$response['deposit_external_id']
				)
			);
			$order->update_meta_data(
				\AstroPay::META_ORDER_PAYMENT_ID,
				$response['deposit_external_id']
			);
			$order->update_meta_data(
				\AstroPay::META_ORDER_PAYMENT_ID_INTERNAL,
				$response['merchant_deposit_id']
			);
			$order->save();
		} else {
			$order->add_order_note(
				sprintf(
					__(
						'It was not possible to create a deposit.',
						'astropay'
					)
				)
			);
		}

		return $response;
	}

	/**
	 * Validates Post parameters for Ajax Request
	 *
	 * @return bool/string
	 */
	public static function validate_ajax_request() {
		$errorCd = '';
		if ( ! isset( $_POST['nonce'] ) ) {
			$errorCd = 'missing nonce';
		} else {
			if ( ! wp_verify_nonce( wp_unslash( sanitize_text_field( $_POST['nonce'] ) ), \AstroPay::GATEWAY_ID ) ) {
				$errorCd = 'nonce';
			}
		}

		if ( ! isset( $_POST['order_id'] ) ) {
			$errorCd = 'missing order_id';
		} else {
			if ( empty( $_POST['order_id'] ) ) {
				$errorCd = 'order_id';
			}

			$order_id = filter_var( wp_unslash( $_POST['order_id'] ), FILTER_SANITIZE_NUMBER_INT );
			$order    = wc_get_order( $order_id );
			if ( ! $order ) {
				$errorCd = 'not order';
			}

			$payment_method = $order->get_payment_method();
			if ( empty( $payment_method ) ) {
				$errorCd = 'not payment method';
			}

			if ( \AstroPay::GATEWAY_ID !== $payment_method ) {
				$errorCd = 'not modo';
			}
		}		
		
		if( ! empty( $errorCd ) ){
			return $errorCd;
		}			

		return true;
	}

	/**
	 * Ajax Callback
	 */
	public static function ajax_callback_wp() {
		$ret_validate = static::validate_ajax_request();
		if ( $ret_validate !== true ) {
			wp_send_json_error( $ret_validate );
		}

		$order_id = filter_var( wp_unslash( $_POST['order_id'] ), FILTER_SANITIZE_NUMBER_INT );

		$ret = static::run( $order_id );
		if ( $ret ) {
			wp_send_json_success( $ret );
		} else {
			wp_send_json_error();
		}
	}
}
