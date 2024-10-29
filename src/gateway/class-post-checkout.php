<?php
/**
 * Class PostCheckout
 *
 * @package  Ecomerciar\AstroPay\Gateway\PostCheckout
 */

namespace Ecomerciar\AstroPay\Gateway;

use Ecomerciar\AstroPay\Helper\Helper;
use Ecomerciar\AstroPay\Sdk\AstroPaySdk;
use \WC_Payment_Gateway;
use Ecomerciar\AstroPay\Gateway\WC_AstroPay;

defined( 'ABSPATH' ) || exit();
/**
 * Post Checkout Page Controller
 */
class PostCheckout {

	/**
	 * Run Action
	 *
	 * @param int $order_id ID for WC Order.
	 *
	 * @return bool
	 */
	public static function render( $order_id ) {
		$order = wc_get_order( $order_id );

		if ( \AstroPay::GATEWAY_ID !== $order->get_payment_method() ) {
			return false;
		}
		?>
		<span class="cta-astropay-post-checkout" style="font-size:110%">
		<?php echo __( 'Haga clic en el botón para completar la compra', 'astropay' ); ?>
		<span>
		<div class="checkout-top-div-container post-checkout">
		<a id="astropay-modal-cta" class="button btn btn-primary">
			<span>
				<?php echo __( 'Pagar con ', 'astropay'); ?> 
			</span>
			<img src="<?php echo Helper::get_assets_folder_url(); ?>/img/logotype_astropay_secondary_ok.png" alt="<?php echo __( 'AstroPay', 'astropay'); ?>">
		</a>
		<a id="select-gateway" class="button btn btn-secondary" href="<?php echo $order->get_checkout_payment_url( false );?>">
			<span>
				<?php echo __( 'Seleccionar otro método de pago ', 'astropay'); ?> 
			</span>			
		</a>
		</div>		
		<style>			
			#astropay-modal-cta{
				min-width: 225px;
				background: #000000;
				border-radius: 6px;
				color: white;		
				text-align:center;			
			}
			#astropay-modal-cta *{
				display: inline-block;
			}
			#astropay-modal-cta img{
				max-height: 35px;				
				vertical-align:middle;
				margin: -5px;
			}
			.astropay-container-spinner img{
				animation: rotation 1s infinite linear;
			}
			@keyframes rotation {
				from {
					transform: rotate(0deg);
				}
				to {
					transform: rotate(359deg);
				}
			}
			#astro-container{
				z-index: 9999 !important;
			}
		</style>
	<?php

	$astropay_environment = Helper::get_option('environment_mode');

	if("sandbox"==$astropay_environment){
		$astropay_app_id = Helper::get_option('sdk_api_key_sandbox');
	} else {
		$astropay_app_id = Helper::get_option('sdk_api_key');
	}
	

	?>
	<script type="text/javascript">
		var wc_astropay_settings = {
			action: "astropay_request_deposit_action",
			ajax_url : "<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>",
			ajax_nonce : "<?php echo wp_create_nonce( \AstroPay::GATEWAY_ID ); ?>", 
			order_id : "<?php echo $order_id; ?>",
			spinner_id : 'astropay-container-spinner',
			spinner_url : '<?php echo Helper::get_assets_folder_url(); ?>/img/loading-spinner.png',
			modalCallbackURL: "<?php echo $order->get_checkout_payment_url( true ); ?>",
			modalCallbackURLSuccess: "<?php echo $order->get_checkout_order_received_url(); ?>",
			astropay_cta_flag: <?php echo isset($_GET["astropay_cta"])? "true" : "false";?>,
			astropay_app_id: "<?php echo $astropay_app_id;?>",
			astropay_environment: "<?php echo $astropay_environment;?>",
			pbap: "<?php echo isset($_GET["pbap"])? $_GET["pbap"] : '';?>"
		}
	</script>
		<?php
		wp_enqueue_script( 'astropay-modal' );
		wp_enqueue_script( 'astropay-gateway' );

		return true;
	}
}
