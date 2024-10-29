<?php
/**
 * Class AstroPaySdk
 *
 * @package  Ecomerciar\AstroPay\Helper\AstroPaySdk
 */

namespace Ecomerciar\AstroPay\Sdk;

use Ecomerciar\AstroPay\Api\AstroPayApi;
use Ecomerciar\AstroPay\Helper\Helper;

use \WC_Subscriptions_Order;
/**
 * Main Class AstroPay Sdk.
 */
class AstroPaySdk {

	private $api_key;
	private $api_secret;
	private $sdk_key;
    private $signature;

	const JSON = 'application/json';
	/**
	 * Constructor.
	 *
	 * @param string $api_key AstroPay API Key.
	 * @param string $api_secret AstroPay API Secret.
	 * @param string $sdk_key AstroPay SDK API Key.
	 */
	public function __construct(
		string $api_key,
		string $api_secret,
        string $sdk_api_key
	) {		
		$this->api_key       = $api_key;
		$this->api_secret    = $api_secret;
		$this->sdk_key       = $sdk_api_key;
		$this->api           = new astroPayApi();
		//$this->set_access_token( $access_token );		
	}

    private function sign_payload($payload){
        $json = json_encode($payload);
        return hash_hmac("sha256", $json, $this->api_secret, false);
    }

	/**
	 * Validate API Key
	 *
	 * @param string $apiKey Api Key.
	 */
	public function validate_api_key(){		
		$data_to_send = [];
		try {
			$res = $this->api->get(
				'/deposit/testAPIkey/status',
				$data_to_send,
				array(
					'Merchant-Gateway-Api-Key' => $this->api_key,
					'Content-Type'  => self::JSON,
					'accept'        => self::JSON,
				)
			);
		} catch ( \Exception $e ) {
			Helper::log_error( __FUNCTION__ . ': ' . $e->getMessage() );
			return array();
		}
		return $this->handle_response( $res, __FUNCTION__ );
	}

	/**
	 * Create deposit
	 *
	 * @param int $order_id ID for WC Order.
	 *
	 * @return array
	 */
	public function request_deposit($order_id, $options = [
		'pbap' => '']){		
		$order        = wc_get_order( $order_id );
		$data_to_send = array(
			'amount'				=> floatval( $order->get_total() ),
			'currency'              => $order->get_currency(),
			'country'            	=> $order->get_billing_country(),
			'merchant_deposit_id'   => 'WCD' . $order_id . "-" . wp_generate_uuid4(),
			'callback_url'          => get_site_url( null, \AstroPay::WC_WEBHOOK_URL),
			'user' 					=> array(
				'merchant_user_id' 	=> $order->get_billing_email(),
				'email' 			=> $order->get_billing_email(),
				'phone' 			=> $order->get_billing_phone(),
				'first_name' 		=> $order->get_billing_first_name(),
				'last_name' 		=> $order->get_billing_last_name(),
				'country' 			=> $order->get_billing_country(),
			),
			'product'            	=> array(
				'mcc' 				=> '5399',
				'merchant_code' 	=> 'WC01',
				'description' 		=> __( 'Shop at ', 'astropay' ) . get_bloginfo( 'name' ),				
			),
			'redirect_url' 			=> $order->get_checkout_order_received_url(),
			'visual_info' 			=> array(
				'merchant_name' 	=> get_bloginfo( 'name' ),									
			),
		);

		//Pay by AstroPay
		if(!empty($options['pbap'])){
			$data_to_send['payment_method_code'] = $options['pbap'];
		}
		if (class_exists('WC_Subscriptions_Order')){
			if ( WC_Subscriptions_Order::order_contains_subscription( $order_id ) ) {
				$order_items = $order->get_items();
				// Only one subscription allowed in the cart
				//$product = $order->get_product_from_item( $order_items[0] );				
				
				$billing_period = WC_Subscriptions_Order::get_subscription_period( $order );
				$trial_period = WC_Subscriptions_Order::get_subscription_trial_period( $order );
				$sign_up_fee = WC_Subscriptions_Order::get_sign_up_fee( $order );			
				$initial_payment = WC_Subscriptions_Order::get_total_initial_payment( $order );			
				$price_per_period = WC_Subscriptions_Order::get_recurring_total( $order );			
				$subscription_interval = WC_Subscriptions_Order::get_subscription_interval( $order );			
				$subscription_installments = WC_Subscriptions_Order::get_subscription_length( $order ) / $subscription_interval;			
				$subscription_trial_length = WC_Subscriptions_Order::get_subscription_trial_length( $order );
			
				
				switch ($billing_period) {
					case 'day':
						$interval_type = "DAY";
						$interval_amount = $subscription_interval;
						break;
					case 'week':
						$interval_type = "WEEK";
						$interval_amount = $subscription_interval;
						break;
					case 'month':
						$interval_type = "MONTH";
						$interval_amount = $subscription_interval;
						break;
					case 'year':
						$interval_type = "MONTH";
						$interval_amount = 12 * $subscription_interval;
						break;					
				}
				$data_to_send['recurrence'] = array(
					'recurrence_id'		=> $data_to_send['merchant_deposit_id'] . '_recurrence',
					'interval_type'		=> $interval_type, 	//"DAY", "WEEK", "MONTH"
					'interval_amount'	=> $interval_amount,
					'interval_count'	=> empty($subscription_installments)? 99: $subscription_installments,
				);
				$data_to_send['amount'] = floatval( $price_per_period );			

			}
		}			
		
		try {
			$res = $this->api->post(
				'/deposit/init',
				$data_to_send,
				array(
					'Merchant-Gateway-Api-Key' => $this->api_key,
					'Signature' => $this->sign_payload($data_to_send),
					'Content-Type'  => self::JSON,
					'accept'        => self::JSON,
				)
			);
		} catch ( \Exception $e ) {
			Helper::log_error( __FUNCTION__ . ': ' . $e->getMessage() );
			return array();
		}

		return $this->handle_response( $res, __FUNCTION__ );
	}

	/**
	 * Get deposit data
	 *
	 * @param string $deposit_external_id AstroPay Payment Intention.
	 *
	 * @return array
	 */
	public function get_deposit_data( $deposit_external_id ) {
		try {
			$res = $this->api->get(
				'/deposit/'.$deposit_external_id.'/status',
				array(),
				array(
					'Merchant-Gateway-Api-Key' => $this->api_key,
					'Content-Type'  => self::JSON,
					'accept'        => self::JSON,
				)
			);
		} catch ( \Exception $e ) {
			Helper::log_error( __FUNCTION__ . ': ' . $e->getMessage() );
			return array();
		}
		return $this->handle_response( $res, __FUNCTION__ );
	}

	/**
	 * Get Recurrence data
	 *
	 * @param string $deposit_external_id AstroPay Payment Intention.
	 *
	 * @return array
	 */
	public function get_recurrence_data( $deposit_internal_id ) {
		try {
			$res = $this->api->get(
				'/recurrence/'.$deposit_internal_id . '_recurrence',
				array(),
				array(
					'Merchant-Gateway-Api-Key' => $this->api_key,
					'Content-Type'  => self::JSON,
					'accept'        => self::JSON,
				)
			);
		} catch ( \Exception $e ) {
			Helper::log_error( __FUNCTION__ . ': ' . $e->getMessage() );
			return array();
		}
		return $this->handle_response( $res, __FUNCTION__ );
	}

	/**
	 * Get Payment Methods
	 * 
	 * @param string $country AstroPay Country.
	 * 
	 */
	 public function get_payment_methods( $country ){
		try {
			$res = $this->api->get(
				'/paymentMethods?country='.$country,
				array(),
				array(
					'Merchant-Gateway-Api-Key' => $this->api_key,
					'Content-Type'  => self::JSON,
					'accept'        => self::JSON,
				)
			);
		} catch ( \Exception $e ) {
			Helper::log_error( __FUNCTION__ . ': ' . $e->getMessage() );
			return array();
		}
		return $this->handle_response( $res, __FUNCTION__ );
	 }

	 /**
	 * Create deposit
	 *
	 * @param int $order_id ID for WC Order.
	 *
	 * @return array
	 */
	public function request_refund($order_id, $options = [
		'amount' => 0,
		'reason' => '',
		'cashout_id' => '']){		
		$order        = wc_get_order( $order_id );
		$data_to_send = [
			'amount' => round( $options['amount'], 2 ),
			'currency' => $order->get_currency(),
			'country' => $order->get_billing_country(),
			'merchant_cashout_id' => $options['cashout_id'],
			'callback_url' => get_site_url( null, \AstroPay::WC_WEBHOOK_URL),
			'user' => [
				'merchant_user_id' => $order->get_customer_id(),
				'email' => $order->get_billing_email(),
				'phone' => Helper::sanitize_phone_number($order->get_billing_phone(), $order->get_billing_country()),
				'first_name' => $order->get_billing_first_name(),
				'last_name' => $order->get_billing_last_name(),
				'address' => [
					'line1' => $order->get_billing_address_1(),
					'line2' => $order->get_billing_address_2(),
					'city' => $order->get_billing_city(),
					'province' => $order->get_billing_state(),
					'country' => $order->get_billing_country(),
					'zip' => $order->get_billing_postcode()
				]
			]
		];

		try {
			$res = $this->api->post(
				'/cashout',
				$data_to_send,
				array(
					'Merchant-Gateway-Api-Key' => $this->api_key,
					'Signature' => $this->sign_payload($data_to_send),
					'Content-Type'  => self::JSON,
					'accept'        => self::JSON,
				)
			);
		} catch ( \Exception $e ) {
			Helper::log_error( __FUNCTION__ . ': ' . $e->getMessage() );
			return array();
		}

		return $this->handle_response( $res, __FUNCTION__ );
	}

	/**
	 * Get deposit data
	 *
	 * @param string $deposit_external_id AstroPay Payment Intention.
	 *
	 * @return array
	 */
	public function get_refund_data( $cashout_id ) {
		try {
			$res = $this->api->get(
				'/cashout/'.$cashout_id.'/status',
				array(),
				array(
					'Merchant-Gateway-Api-Key' => $this->api_key,
					'Content-Type'  => self::JSON,
					'accept'        => self::JSON,
				)
			);
		} catch ( \Exception $e ) {
			Helper::log_error( __FUNCTION__ . ': ' . $e->getMessage() );
			return array();
		}
		return $this->handle_response( $res, __FUNCTION__ );
	}
	
	/**
	 * Cancel Subscription
	 */
	public function cancel_recurrent_deposit($order_id){		
		$order        = wc_get_order( $order_id );
		$recurrence_id = $order->get_meta(
			\AstroPay::META_ORDER_PAYMENT_ID_INTERNAL
		) . "_recurrence";
		try {
			$res = $this->api->put(
				'/recurrence/'.$recurrence_id.'/cancel',
				array(),
				array(
					'Merchant-Gateway-Api-Key' => $this->api_key,
					'Signature' => $this->sign_payload([]),
					'Content-Type'  => self::JSON,
					'accept'        => self::JSON,
				)
			);
		} catch ( \Exception $e ) {
			Helper::log_error( __FUNCTION__ . ': ' . $e->getMessage() );
			return array();
		}
		return $this->handle_response( $res, __FUNCTION__ );
	}

	/**
	 * Handle Response
	 *
	 * @param array  $response Response data.
	 * @param string $function_name Function function is calling from.
	 *
	 * @return array
	 */
	protected function handle_response(
		$response = array(),
		string $function_name = ''
	) {
		if ( 'validate_api_key' === $function_name ) {
			if ( isset( $response['error'] ) &&  "commons_unauthorized" === $response['error'] ) {
				return false;
			} else {
				return true;
			}
		}
		return $response;
	}
}

