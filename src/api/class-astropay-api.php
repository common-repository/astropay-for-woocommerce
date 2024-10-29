<?php
/**
 * Class AstroPayApi
 *
 * @package  Ecomerciar\AstroPay\Api\MODOApi
 */

namespace Ecomerciar\AstroPay\Api;

use Ecomerciar\AstroPay\Helper\Helper;
defined( 'ABSPATH' ) || exit();
/**
 * MODO API Class
 */
class AstroPayApi extends ApiConnector implements ApiInterface {

	const API_BASE_URL = 'https://onetouch-api.astropay.com/merchant/v1';
	const API_BASE_URL_SANDBOX = 'https://onetouch-api-sandbox.astropay.com/merchant/v1';
	/**
	 * Class Constructor
	 *
	 * @param array $settings Settings Object.
	 */
	public function __construct( ) {			

	}

	/**
	 *  Get Base Url
	 *
	 * @return String
	 */
	public function get_base_url() {
		return (Helper::is_sandbox())? $this::API_BASE_URL_SANDBOX : $this::API_BASE_URL;
	}
}
