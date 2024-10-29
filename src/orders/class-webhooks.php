<?php
/**
 * Class Webhooks
 *
 * @package  Ecomerciar\AstroPay\Orders\Webhooks
 */

namespace Ecomerciar\AstroPay\Orders;

use Ecomerciar\AstroPay\Helper\Helper;
use Ecomerciar\AstroPay\Sdk\AstroPaySdk;
use Ecomerciar\AstroPay\Gateway\WC_AstroPay;

defined( 'ABSPATH' ) || exit();

/**
 * WebHook's base Class
 */
class Webhooks {

	const OK    = 'HTTP/1.1 200 OK';
	const ERROR = 'HTTP/1.1 500 ERROR';

	/**
	 * Receives the webhook and check if it's valid to proceed
	 *
	 * @param string $data Webhook json Data for testing purpouses.
	 *
	 * @return bool
	 */
	public static function listener( string $data = null ) {

		// Takes raw data from the request.
		if ( is_null( $data ) || empty( $data ) ) {
			$json = file_get_contents( 'php://input' );
		} else {
			$json = $data;
		}

		Helper::log_info( 'Webhook received' );
		Helper::log(
			__FUNCTION__ .
				__( '- Webhook received from AstroPay:', 'astropay' ) .
				$json
		);

		$process = self::process_webhook( $json );

		if ( is_null( $data ) ) {
			// Real Webhook.
			if ( $process ) {
				header( self::OK );
			} else {
				header( self::ERROR );
				wp_die(
					__( 'WooCommerce AstroPay Webhook not valid.', 'astropay' ),
					'AstroPay Webhook',
					array( 'response' => 500 )
				);
			}
		} else {
			// For testing purpouse.
			return $process;			
		}
	}


	/**
	 * Process Webhook
	 *
	 * @param json $json Webhook data for.
	 *
	 * @return bool
	 */
	public static function process_webhook( $json ) {

		// Converts it into a PHP object.
		$data = json_decode( $json, true );

		if ( empty( $json ) || ! self::validate_input( $data ) ) {
			return false;
		}

		return self::handle_webhook( $data );		
	}

	/**
	 * Get Order Id from Data Json
	 *
	 * @param array $data Webhook data.
	 *
	 * @return int
	 */
	private static function get_order_id( array $data ) {
		if( isset($data['deposit_external_id']) ){
			$astropay_id  = filter_var( $data['deposit_external_id'], FILTER_SANITIZE_STRING );
			return Helper::find_order_by_itemmeta_value(
				\AstroPay::META_ORDER_PAYMENT_ID,
				$astropay_id
			);
		}
		if( isset($data['merchant_cashout_id']) ){
			$astropay_id  = filter_var( $data['merchant_cashout_id'], FILTER_SANITIZE_STRING );
			return Helper::find_order_by_itemmeta_value(
				\AstroPay::META_ORDER_CASHOUT_ID_INTERNAL,
				$astropay_id
			);
		}
		
	}

	/**
	 * Validates the incoming webhook
	 *
	 * @param array $data Webhook data to be validated.
	 *
	 * @return bool
	 */
	private static function validate_input( array $data ) {
		$return = true;
		$data   = wp_unslash( $data );

		if ( ( ! isset( $data['deposit_external_id'] ) || empty( $data['deposit_external_id'] )) && (! isset( $data['merchant_cashout_id'] ) || empty( $data['merchant_cashout_id'] )) ) {
			Helper::log(
				__FUNCTION__ .
					__( '- Webhook received without deposit_external_id or merchant_cashout_id.', 'astropay' )
			);
			$return = false;
		}		
		if ( ! isset( $data['status'] ) || empty( $data['status'] ) ) {
			Helper::log(
				__FUNCTION__ .
					__( '- Webhook received without status.', 'astropay' )
			);
			$return = false;
		} else {

			if ( 'APPROVED' !== $data['status']  &&  'CANCELLED' !== $data['status'] &&  'PENDING' !== $data['status']) {
				Helper::log(
					__FUNCTION__ .
						__( '- Webhook received status: ' . $data['status']  , 'astropay' )
				);
				$return = false;
			}
		}

		if ( $return ) {
			/*Tiene AstroPay como medio de pago?*/
			$order_id = self::get_order_id( $data );
			if ( empty( $order_id ) || is_null( $order_id ) || ! is_int( $order_id ) ) {
				Helper::log(
					__FUNCTION__ .
						__(
							'- Webhook received without order related.',
							'astropay'
						)
				);
				$return = false;
			}
		}

		return $return;
	}

	/**
	 * Handles and processes the webhook
	 *
	 * @param array $data webhook data to be processed.
	 *
	 * @return bool
	 */
	private static function handle_webhook( array $data ) {

		$order_id = self::get_order_id( $data );
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
		
		if(isset($data['deposit_external_id'])){
			$response = $sdk->get_deposit_data( $data['deposit_external_id'] );
			if ( 'APPROVED' === $response['status'] ) {
				$order->payment_complete();
				$order->add_order_note(
					sprintf(
						__(
							'AstroPay - Approved Payment. ID %s',
							'astropay'
						),
						$data['deposit_external_id']
					)
				);			
			} 
			if ( 'CANCELLED' === $response['status'] ) {
				$order->add_order_note(
					sprintf(
						__(
							'AstroPay - Cancelled Payment. ID %s',
							'astropay'
						),
						$data['deposit_external_id']
					)
				);
			} 		
			if ( 'PENDING' === $response['status'] ) {
				$order->add_order_note(
					sprintf(
						__(
							'AstroPay - Pending Payment. ID %s',
							'astropay'
						),
						$data['deposit_external_id']
					)
				);
			} 
		}
		if(isset($data['cashout_id'])){
			$response = $sdk->get_refund_data( $data['cashout_id'] );			

			if ( 'APPROVED' === $response['status'] ) {
				$order->payment_complete();
				$order->add_order_note(
					sprintf(
						__(
							'AstroPay - The refund has been completed and the money was creditd to customer card. ID %s',
							'astropay'
						),
						$data['cashout_id']
					)
				);			
			} 
			if ( 'CANCELLED' === $response['status'] ) {
				$order->add_order_note(
					sprintf(
						__(
							'AstroPay - The merchant cancelled the payment for the refund. ID %s',
							'astropay'
						),
						$data['cashout_id']
					)
				);
			} 		
			if ( 'PENDING' === $response['status'] ) {
				$order->add_order_note(
					sprintf(
						__(
							'AstroPay - The refund is created but is yet to be processed. ID %s',
							'astropay'
						),
						$data['cashout_id']
					)
				);
			} 
		}
		return true;
	}
}
