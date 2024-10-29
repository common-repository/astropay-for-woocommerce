<?php
/**
 * Class WC_AstroPay
 *
 * @package  Ecomerciar\AstroPay\Gateway\WC_AstroPay
 */

namespace Ecomerciar\AstroPay\Gateway;

use Ecomerciar\AstroPay\Helper\Helper;
use Ecomerciar\AstroPay\Sdk\AstroPaySdk;
use \WC_Subscriptions_Order;

defined( 'ABSPATH' ) || class_exists( '\WC_Payment_Gateway' ) || exit();

/**
 * Main Class AstroPay Payment.
 */
class WC_AstroPay extends \WC_Payment_Gateway {

	/**
	 * Constructor for the gateway.
	 */
	public function __construct() {
		$this->id                 = \AstroPay::GATEWAY_ID;
		$this->has_fields         = false;
		$this->method_title       = __( 'AstroPay', 'astropay' );
		$this->method_description = __('Accept payments using AstroPay.','astropay');

		// Define user set variables
		$this->title = __( 'AstroPay', 'astropay' );
		$this->instructions = $this->get_option(
			$this->description,
			$this->method_description
		);
		$this->icon         =
			Helper::get_assets_folder_url() . '/img/logotype_astropay_primary_ok.png';
		
		// Load the settings.
		$this->init_form_fields();
		$this->init_settings();
		$this->supports[] = 'products';
		$this->supports[] = 'refunds';
		$this->supports[] = 'subscriptions';
		$this->supports[] = 'subscription_cancellation';
		$this->supports[] = 'subscription_suspension';
		$this->enabled      				= $this->get_option( 'enabled' );		
		$this->title      					= $this->get_option( 'title' );	
		$this->description      			= $this->get_option( 'description' );	
		$this->environment_mode     		= $this->get_option( 'wc_astropay_environment_mode' );	
		$this->api_key      				= $this->get_option( 'wc_astropay_api_key' );	
		$this->api_secret      				= $this->get_option( 'wc_astropay_api_secret' );	
		$this->sdk_api_key     				= $this->get_option( 'wc_astropay_sdk_api_key' );	
		$this->api_key_sandbox      		= $this->get_option( 'wc_astropay_api_key_sandbox' );	
		$this->api_secret_sandbox      		= $this->get_option( 'wc_astropay_api_secret_sandbox' );	
		$this->sdk_api_key_sandbox      	= $this->get_option( 'wc_astropay_sdk_api_key_sandbox' );	
		$this->api_key_europe      			= $this->get_option( 'wc_astropay_api_key_europe' );	
		$this->api_secret_europe      		= $this->get_option( 'wc_astropay_api_secret_europe' );	
		$this->sdk_api_key_europe      		= $this->get_option( 'wc_astropay_sdk_api_key_europe' );	
		$this->api_key_europe_sandbox      	= $this->get_option( 'wc_astropay_api_key_europe_sandbox' );	
		$this->api_secret_europe_sandbox    = $this->get_option( 'wc_astropay_api_secret_europe_sandbox' );	
		$this->sdk_api_key_europe_sandbox   = $this->get_option( 'wc_astropay_sdk_api_key_europe_sandbox' );	
		$this->pay_by_astropay_enabled      = $this->get_option( 'wc_astropay_pay_by_astropay_enabled' );	
		$this->log_enabled      			= $this->get_option( 'wc_astropay_log_enabled' );		
		$this->merchant_category_code		= $this->get_option( 'wc_astropay_merchant_category_code');		
		
		if (Helper::is_sandbox()){
			$this->sdk = new AstroPaySdk($this->api_key_sandbox, $this->api_secret_sandbox, $this->sdk_api_key_sandbox);
			$this->use_europe = (!empty($this->api_key_europe_sandbox) || !empty($this->api_secret_europe_sandbox)  || !empty($this->sdk_api_key_europe_sandbox));
			$this->sdk_europe = new AstroPaySdk($this->api_key_europe_sandbox, $this->api_secret_europe_sandbox, $this->sdk_api_key_europe_sandbox);			
		} else {
			$this->sdk = new AstroPaySdk($this->api_key, $this->api_secret, $this->sdk_api_key);
			$this->use_europe = (!empty($this->api_key_europe)  || !empty($this->api_secret_europe)  || !empty($this->sdk_api_key_europe));
			$this->sdk_europe = new AstroPaySdk($this->api_key_europe, $this->api_secret_europe, $this->sdk_api_key_europe);
		}
		

		$this->enqueue_settings_js();
		
		add_action(
			'woocommerce_update_options_payment_gateways_' . $this->id,
			array(
				$this,
				'process_admin_options',
			)
		);
		add_action(
			'woocommerce_update_options_payment_gateways_' . $this->id,
			array(
				$this,
				'validate_options',
			)
		);
		add_action(
			'woocommerce_thankyou_' . $this->id,
			array(
				$this,
				'thankyou_page',
			)
		);		
	}	
	
	/**
	 * Custom process admin options
	 *
	 * @return mixed
	 * @throws WC_WooMercadoPago_Exception Admin Options Exception.
	 */
	public function validate_options() {
		global $current_section;
		
		if ($current_section ===\AstroPay::GATEWAY_ID){
			$options = Helper::get_options(\AstroPay::GATEWAY_ID);
			if (Helper::is_sandbox()){
				$sdk = new AstroPaySdk($options['api_key_sandbox'], $options['api_secret_sandbox'], $options['sdk_api_key_sandbox']);
				$use_europe = (!empty($options['api_key_europe_sandbox']) || !empty($options['api_secret_europe_sandbox'])  || !empty($options['sdk_api_key_europe_sandbox']));
				$sdk_europe = new AstroPaySdk($options['api_key_europe_sandbox'], $options['api_secret_europe_sandbox'], $options['sdk_api_key_europe_sandbox']);			
			} else {
				$sdk = new AstroPaySdk($options['api_key'], $options['api_secret'], $options['sdk_api_key']);
				$use_europe = (!empty($options['api_key_europe'])  || !empty($options['api_secret_europe'])  || !empty($options['sdk_api_key_europe']));
				$sdk_europe = new AstroPaySdk($options['api_key_europe'], $options['api_secret_europe'], $options['sdk_api_key_europe']);
			}


			if ( ! $sdk->validate_api_key() ){
				add_action(
					'admin_notices',
					function () {
						global $current_section;
						if ( $current_section === \AstroPay::GATEWAY_ID ) {
							echo '<div class="notice notice-error is-dismissible">';
							echo '<p>' . __( 'Main credentials for <strong> astropay </strong> are not correct.' ,'astropay') . '</p>';
							echo '</div>';
						}
					}
				);
			}
			if ( $use_europe && ! $sdk_europe->validate_api_key() ){	
				add_action(
					'admin_notices',
					function () {
						global $current_section;
						if ( $current_section === \AstroPay::GATEWAY_ID ) {
							echo '<div class="notice notice-error is-dismissible">';
								echo '<p>' . __( 'Europe credentials for <strong> astropay </strong> are not correct.' ,'astropay') . '</p>';
								echo '</div>';
						}
					}
				);
			}
		}

	}	

	/**
	 * Initialize Gateway Settings Form Fields
	 */
	public function init_form_fields() {
		$this->form_fields = include 'settings.php';
	}

	/**
	 * Process the payment and return the result
	 *
	 * @param int $order_id ID of Woo Order.
	 *
	 * @return array
	 */
	public function process_payment( $order_id ) {	
		$order = wc_get_order( $order_id );
		$pbap = isset( $_POST['pbap'] ) ? sanitize_text_field( $_POST['pbap'] ) : '';
					
		// Return thankyou redirect.
		return array(
			'result'   => 'success',		
			'redirect' => add_query_arg( 'astropay_cta', true, add_query_arg( 'pbap', $pbap, $order->get_checkout_payment_url( true ) ) ),
		);
	}

	/**
	 * Output for the order received page.
	 */
	public function thankyou_page() {
		// Nothing to add, but required to avoid Warnings.
	}

	/**
	 * Set if AstroPay must be available or not
	 *
	 * @param Array $available_gateways Array of Available Gateways.
	 *
	 * @return Array
	 */
	public static function available_payment_method( $available_gateways ) {
		if(WC()->customer){
			if(!Helper::validate_country_code(WC()->customer->get_billing_country()) && isset( $available_gateways[ \AstroPay::GATEWAY_ID ])){
				unset( $available_gateways[ \AstroPay::GATEWAY_ID ] );			
			}
		}		
		return $available_gateways;
	}



	/**
     * enqueue_settings_js
     */
    private function enqueue_settings_js(){
        wc_enqueue_js(
			"jQuery( function( $ ) {
				$(document).ready( function(){
					function show_hide_api_fields( sandbox, europe){
						var use_europe_credentials = $('#display_europe_btn');
						var europe_api_key = $('#woocommerce_wc_astropay_wc_astropay_api_key_europe');
						var europe_api_secret = $('#woocommerce_wc_astropay_wc_astropay_api_secret_europe');
						var europe_sdk_api_key = $('#woocommerce_wc_astropay_wc_astropay_sdk_api_key_europe');
						var europe_api_key_sandbox = $('#woocommerce_wc_astropay_wc_astropay_api_key_europe_sandbox');
						var europe_api_secret_sandbox = $('#woocommerce_wc_astropay_wc_astropay_api_secret_europe_sandbox');
						var europe_sdk_api_key_sandbox = $('#woocommerce_wc_astropay_wc_astropay_sdk_api_key_europe_sandbox');

						var rotw_api_key = $('#woocommerce_wc_astropay_wc_astropay_api_key');
						var rotw_api_secret = $('#woocommerce_wc_astropay_wc_astropay_api_secret');
						var rotw_sdk_api_key = $('#woocommerce_wc_astropay_wc_astropay_sdk_api_key');
						var rotw_api_key_sandbox = $('#woocommerce_wc_astropay_wc_astropay_api_key_sandbox');
						var rotw_api_secret_sandbox = $('#woocommerce_wc_astropay_wc_astropay_api_secret_sandbox');
						var rotw_sdk_api_key_sandbox = $('#woocommerce_wc_astropay_wc_astropay_sdk_api_key_sandbox');
						
						if(sandbox){
							$(europe_api_key).closest('tr').hide();
							$(europe_api_secret).closest('tr').hide();
							$(europe_sdk_api_key).closest('tr').hide();
							$(rotw_api_key).closest('tr').hide();
							$(rotw_api_secret).closest('tr').hide();
							$(rotw_sdk_api_key).closest('tr').hide();
							
							
							$(rotw_api_key_sandbox).closest('tr').show();
							$(rotw_api_secret_sandbox).closest('tr').show();
							$(rotw_sdk_api_key_sandbox).closest('tr').show();

							if( europe_api_key_sandbox.val() || europe_api_secret_sandbox.val() || europe_sdk_api_key_sandbox.val() ){
								europe = true;
							} 		

							if(europe == false){
								$(europe_api_key_sandbox).closest('tr').hide();
								$(europe_api_secret_sandbox).closest('tr').hide();
								$(europe_sdk_api_key_sandbox).closest('tr').hide();																
								$(use_europe_credentials).attr('data-toogle','closed');							
							} else {
								$(europe_api_key_sandbox).closest('tr').show();
								$(europe_api_secret_sandbox).closest('tr').show();
								$(europe_sdk_api_key_sandbox).closest('tr').show();	
								$(use_europe_credentials).attr('data-toogle','opened');
							}
						} else {							
							$(rotw_api_key).closest('tr').show();
							$(rotw_api_secret).closest('tr').show();
							$(rotw_sdk_api_key).closest('tr').show();

							$(europe_api_key_sandbox).closest('tr').hide();
							$(europe_api_secret_sandbox).closest('tr').hide();
							$(europe_sdk_api_key_sandbox).closest('tr').hide();
							$(rotw_api_key_sandbox).closest('tr').hide();
							$(rotw_api_secret_sandbox).closest('tr').hide();
							$(rotw_sdk_api_key_sandbox).closest('tr').hide();
							if( europe_api_key.val() || europe_api_secret.val() || europe_sdk_api_key.val() ){
								europe = true;
							}
							if(europe == false){
								$(europe_api_key).closest('tr').hide();
								$(europe_api_secret).closest('tr').hide();
								$(europe_sdk_api_key).closest('tr').hide();
								$(use_europe_credentials).attr('data-toogle','closed');
							} else {
								$(europe_api_key).closest('tr').show();
								$(europe_api_secret).closest('tr').show();
								$(europe_sdk_api_key).closest('tr').show();
								$(use_europe_credentials).attr('data-toogle','opened');
							}
						}

					}
					
					var sandbox_select = $('#woocommerce_wc_astropay_wc_astropay_environment_mode');
					var display_europe = false;
					var display_sandbox = false;

					if ( 'sandbox' === $( sandbox_select ).val() ) {
						display_sandbox = true;
					}
					
					
					show_hide_api_fields(display_sandbox, display_europe);					

					var use_europe_credentials = $('#display_europe_btn');
					use_europe_credentials.css('cursor','pointer');
					use_europe_credentials.each( function(){
						$(this).click(function(){
							show_hide_api_fields( ( 'sandbox' === $( sandbox_select ).val() ), ($(this).attr('data-toogle') === 'closed'));
						})
					});

					sandbox_select.each( function(){
						$(this).change(function(){
							show_hide_api_fields( ( 'sandbox' === $( this ).val() ), false);
						})
					});

				});
			});"
		);
    }

	/**
	 * Add fields to pre-select method of payment
	 *
	 */
    public function payment_fields() {
		if ( $this->description ) {
			echo wpautop( wptexturize( $this->description ) );
		}

		if ($this->pay_by_astropay_enabled == 'yes'){	
			$country = WC()->customer->get_billing_country();	
			$res = $this->sdk->get_payment_methods($country);
			if(isset($res['paymentMethods'])){
				$methods = $res['paymentMethods']; 
			}
			?>
			<style>
				p.astropay-payment-method-i{
					padding-bottom: 10px;
				}
				p.astropay-payment-method-i * img.astropay-pbap{
					float: unset ;
					vertical-align: middle ; 
					display: inline ; 
					padding-left: 2px ; 
					padding-right: 2px ; 
				}
			</style>
			<fieldset>
				<p class="form-row" id="payment_method_options_for_astropay">
					<?php
					if ( empty( $methods ) ) {
						if ( ! empty( $message ) ) {
							//echo '<div class="woocommerce-error">' . $message . '</div>';
						}
					} else {?>
						<label for="pbap"><?php echo __( "Choose a Payment Method :", 'astropay' )?> </label>
						<?php
						foreach( $methods as $data ) {
							$code = $data['code'];?>					
							<p class="astropay-payment-method-i">
								<input type="radio" name="astropay-pbap" value="<?php echo esc_attr($code)?>"/>
								<img class="astropay" src="<?php echo esc_url("https://getapp.astropaycard.com/img/payment_methods/{$code}.svg");?>" alt="<?php echo esc_attr($code)?>"/> 
								<span><?php echo esc_html(Helper::get_payment_method_descr($code))?></span>
							</p>
							<?php
						}
						?>
						<style>
							p.astropay-payment-method-i{
								cursor: pointer;
							}
						</style>
						<script>
							jQuery(document).ready(function($){
								$("p.astropay-payment-method-i").each( function(){
									$(this).click(function(){
										$("input", $(this)).prop("checked", true);
									})
								});
							});
						</script>
						<?php
					}?>
				</p>
				<div class="clear"></div>
			</fieldset>
			<?php
			
		}	
			
    } 

	/**
	 * Process Refunds
	 */
	public function process_refund($order_id, $amount = NULL, $reason = '') {      

		if ( empty( $order_id ) ) return false;

		if ( empty( $amount ) ) {
			$this->log_message( "Skipping ZERO value refund for Order ID $order_id ");
			return false;
		}
		
        $order = wc_get_order($order_id);
        $order_data = $order->get_data();

		$mci = 'WCCI' . $order_id . "-" . wp_generate_uuid4();
        $response = $this->sdk->request_refund($order_id, [
            'amount' => $amount,
            'reason' => $reason,
			'cashout_id' => $mci,			
        ]);
 				
        $merchant_cashout_id = $mci;
		$cashout_id = isset($response['cashout_id'])? $response['cashout_id']:'';
        
		if(empty($cashout_id)){
            return new \WP_Error( 'wcastropay-refound-rsp', __( 'AstroPay Cashout failed.',  'astropay' ) );
        }
        $order->add_order_note(sprintf(__('AstroPay Cashout requested. Merchant Cashout Id: %s - AstroPay Cashout Id: %s', 'astropay'), $merchant_cashout_id, $cashout_id ));           
		$order->update_meta_data(
			\AstroPay::META_ORDER_CASHOUT_ID,
			$cashout_id
		);
		$order->update_meta_data(
			\AstroPay::META_ORDER_CASHOUT_ID_INTERNAL,
			$merchant_cashout_id
		);
		
      $order->save();
 
      return true;

    }

	/**
	 * Process Refunds
	 */
	public static function cancel_subscription( $subscription ) {
		$order_id = $subscription->get_parent_id();
		$sdk = Helper::get_sdk_for_order_id($order_id);
		$response = $sdk->cancel_recurrent_deposit($order_id);
		if(isset($response['status'])){
			if($response['status'] == "INACTIVE"){
				$subscription->add_order_note( __('Recurrent deposit was cancelled at AstroPay.', 'astropay') );
			} else {
				$subscription->add_order_note( __('It was not possible cancel the recurrent deposit at AstroPay.', 'astropay') );
			}
		} else {
			$subscription->add_order_note( __('It was not possible cancel the recurrent deposit at AstroPay.', 'astropay') );
		}		
	}


	/**
	 * Hook 
	 * 
	 */
	public static function process_renewal($amount, \WC_Order $order){
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

		$deposit_internal_id = get_post_meta($order->get_id(), \AstroPay::META_ORDER_PAYMENT_ID_INTERNAL, true);
		$response = $sdk->get_recurrence_data( $deposit_internal_id );
		if(!isset($response['deposits'])){
			foreach($response['deposits'] as $deposit){
				if( $deposit['status'] == "APPROVED"){
					$order->payment_complete();
					$order->add_order_note( sprintf(__('AstroPay Recurrent Deposit. Merchant Deposit Id: %s - AstroPay Deposit Id: %s', 'astropay'),  $deposit['merchant_deposit_id'] ,  $deposit['depositt_external_id']  ) );
				}	
			}
		} else {
			$order->add_order_note( __('It was not possible check the recurrent deposit status at AstroPay.', 'astropay') );
		}
		
	}
}