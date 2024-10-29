<?php
/**
 * Settings.php
 *
 * @package  Ecomerciar\AstroPay\Gateway\
 */

namespace Ecomerciar\AstroPay\Gateway;

use Ecomerciar\AstroPay\Helper\Helper;

return apply_filters(
	'wc_astropay_form_fields',
	array(
		'enabled'                      => array(
			'title'   => __( 'Enable/Disable', 'astropay' ),
			'type'    => 'checkbox',
			'label'   => __( 'Enable AstroPay Payment Gateway',	'astropay'),
			'default' => 'yes',
		),
		'title'		=> 		array(
			'title' => __( 'Title', 'astropay' ),
			'type'  => 'text',
			'default' => __('Pay with AstroPay', 'astropay'),
			'description' => __( 'Payment method title that the customers see during the checkout.', 'astropay'),

		),	
		'description'		=> 		array(
			'title' => __( 'Description', 'astropay' ),
			'type'  => 'textarea',
			'default' => __( 'AstroPay offers multiple payment methods.' , 'astropay'),
			'description'=> __('Payment method description that the customers see during the checkout.', 'astropay'),
		),

		'wc_astropay_credentials_section'  => array(
			'title'       => __( 'AstroPay Credentials', 'astropay' ),
			'type'        => 'title',			
		),
		'wc_astropay_credentials_subtitle' => array(
			'title'       => '',
			'type'        => 'title',
			'description' => __('If you do not have your credentials to operate with AstroPay, please register <a href="https://merchants-stg.astropay.com/signup" target="_bank">here</a>.', 'astropay' ),
		),

		'wc_astropay_environment_mode' => array(
			'title'      => __('Environment Mode', 'astropay'),
			'type'      => 'select',
			'default'   => 'sandbox',                   
			'options'   => [
				'production' => 'Production',
				'sandbox'    => 'Test'                        
			],
			'description'       => __( 'Enable AstroPay Test Environment. If selected, please make sure you use test keys above.', 'astropay' ),			
			
		) ,
		/*'wc_astropay_merchant_category_code'  => array(
			'title' => __( 'Merchant Category Code', 'astropay' ),
			'type'  => 'text',
			'description'	=> __('Your MCC code is available in the Backoffice under \'Merchant Settings\'. If you\'re not able to locate it, contact your Account Manager or the <a href="https://developers-wallet.astropay.com/docs/need-help" target="_blank">commercial team</a>.', 'astropay')
		),*/

		'wc_astropay_use_rotw_credentials' => array(
			'title'       => '',
			'type'        => 'title',
			'description' => __('<a>Main credentials (rest of the world)</a>', 'astropay' ),
		),

		'wc_astropay_api_key'             => array(
			'title' => __( 'API Key', 'astropay' ),
			'type'  => 'text',
		),
		'wc_astropay_api_secret'             => array(
			'title' => __( 'API Secret', 'astropay' ),
			'type'  => 'password',
		),
		'wc_astropay_sdk_api_key'         => array(
			'title' => __( 'SDK API Key', 'astropay' ),
			'type'  => 'text',
		),

		'wc_astropay_api_key_sandbox'             => array(
			'title' => __( 'API Key', 'astropay' ),
			'type'  => 'text',
		),
		'wc_astropay_api_secret_sandbox'             => array(
			'title' => __( 'API Secret', 'astropay' ),
			'type'  => 'password',
		),
		'wc_astropay_sdk_api_key_sandbox'         => array(
			'title' => __( 'SDK API Key', 'astropay' ),
			'type'  => 'text',
		),

		'wc_astropay_use_europe_credentials' => array(
			'title'       => '',
			'type'        => 'title',
			'description' =>
				'<a name="display_europe" id="display_europe_btn">' .
				__( 'Use specific credentials for Europe +', 'astropay' ) .
				'</a>',
		),
		
		'wc_astropay_api_key_europe'             => array(
			'title' => __( 'API Key', 'astropay' ),
			'type'  => 'text',
		),
		'wc_astropay_api_secret_europe'             => array(
			'title' => __( 'API Secret', 'astropay' ),
			'type'  => 'password',
		),

		'wc_astropay_sdk_api_key_europe'         => array(
			'title' => __( 'SDK API Key', 'astropay' ),
			'type'  => 'text',
		),
		
		'wc_astropay_api_key_europe_sandbox'             => array(
			'title' => __( 'API Key', 'astropay' ),
			'type'  => 'text',
		),
		'wc_astropay_api_secret_europe_sandbox'             => array(
			'title' => __( 'API Secret', 'astropay' ),
			'type'  => 'password',
		),

		'wc_astropay_sdk_api_key_europe_sandbox'         => array(
			'title' => __( 'SDK API Key', 'astropay' ),
			'type'  => 'text',
		),		

		'wc_astropay_validate_credentials' => array(
			'title'       => '',
			'type'        => 'title',
			'description' =>
				'<p class="submit"><button name="save" class="button-primary woocommerce-save-button" type="submit" value="' .
				__( 'Validate Credencials', 'astropay' ) .
				'">' .
				__( 'Validate Credencials', 'astropay' ) .
				'</button></p>',
		),

		'wc_astropay_testanddebug_section' => array(
			'title'       => __( 'Advanced Settings', 'astropay' ),
			'type'        => 'title',
			'description' => '',
		),

		'wc_astropay_pay_by_astropay_enabled'          => array(
			'title'       => __( 'Pay by AstroPay', 'astropay' ),
			'type'        => 'checkbox',
			'label'       => __( 'Enable Pay by AstroPay. If checked, the payment method (e.g: Pix) will be displayed directly on the website.', 'astropay' ),			
			'default'     => 'no',
		),		

		'wc_astropay_log_enabled'          => array(
			'title'       => __( 'Enable/Disable', 'astropay' ),
			'type'        => 'checkbox',
			'label'       => __( 'Activate Logs', 'astropay' ),
			'description' => sprintf(
				__(
					'You can enable plugin debugging to track communication between the plugin and AstroPay API. You will be able to view the record from the <a href="%s">WooCommerce > Status > Records</a> menu.',
					'astropay'
				),
				esc_url( get_admin_url( null, 'admin.php?page=wc-status&tab=logs' ) )
			),
			'default'     => 'no',
		),
	)
);
