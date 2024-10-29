<?php
/**
 * Hooks
 *
 * @package  Ecomerciar\AstroPay\
 */

defined( 'ABSPATH' ) || exit();

// --- Settings
add_filter(
	'plugin_action_links_' . plugin_basename( \AstroPay::MAIN_FILE ),
	array(
		'AstroPay',
		'create_settings_link',
	)
);

// --- Payment Method
add_filter( 'woocommerce_payment_gateways', array( 'AstroPay', 'add_payment_method' ) );
add_filter(
	'woocommerce_available_payment_gateways',
	array(
		'\Ecomerciar\AstroPay\Gateway\WC_AstroPay',
		'available_payment_method',
	)
);
add_action('woocommerce_subscription_cancelled_'.\AstroPay::GATEWAY_ID, 
	array(
		'\Ecomerciar\AstroPay\Gateway\WC_AstroPay',
		'cancel_subscription',
	)
);

// --- Onboarding
add_action( 'admin_menu',  ['\Ecomerciar\AstroPay\Onboarding\Main', 'register_onboarding_page']);
add_action( 'admin_enqueue_scripts', ['AstroPay', 'register_admin_scripts'] );

// --- Frontend buttons
add_action(
	'woocommerce_receipt_'.\AstroPay::GATEWAY_ID,
	array( '\Ecomerciar\AstroPay\Gateway\PostCheckout', 'render' ),
	90
);
add_action( 'wp_enqueue_scripts', ['AstroPay', 'register_front_scripts'] );

// --- Order Ajax Actions
add_action(
	'wp_ajax_astropay_request_deposit_action',
	array(
		'\Ecomerciar\AstroPay\Gateway\RequestDepositAction',
		'ajax_callback_wp',
	)
);
add_action(
	'wp_ajax_nopriv_astropay_request_deposit_action',
	array(
		'\Ecomerciar\AstroPay\Gateway\RequestDepositAction',
		'ajax_callback_wp',
	)
);

// --- Webhook
add_action(
	'woocommerce_api_wc-astropay',
	array(
		'\Ecomerciar\AstroPay\Orders\Webhooks',
		'listener',
	)
);

// --- Hook Suscription
do_action( 'woocommerce_scheduled_subscription_payment_' . \AstroPay::GATEWAY_ID,  
	array(
		'\Ecomerciar\AstroPay\Gateway\WC_AstroPay',
		'process_renewal',
	), 10, 2 );