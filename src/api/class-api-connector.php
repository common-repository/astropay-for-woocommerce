<?php
/**
 * Class ApiConnector
 *
 * @package  Ecomerciar\AstroPay\Api\ApiConnector
 */

namespace Ecomerciar\AstroPay\Api;

use Ecomerciar\AstroPay\Helper\Helper;

defined( 'ABSPATH' ) || exit();
/**
 * Abstract API Class
 */
abstract class ApiConnector {

	/**
	 * Executes API Request
	 *
	 * @param string $method HTTP Method Request.
	 * @param string $url URL Target Request.
	 * @param array  $body Data to send.
	 * @param array  $headers HTTP Headers for Requests.
	 * @return string
	 */
	protected function exec(
		string $method,
		string $url,
		array $body,
		array $headers
	) {

		$args['method']  = $method;
		$args['headers'] = $headers;
		if ( strtoupper( $method ) === 'GET' ) {
			$args['body'] = $body;
		} else {
			$args['body'] = wp_json_encode( $body );
		}

		$request = wp_safe_remote_request( $url, $args );
		if ( is_wp_error( $request ) ) {
			return false;
		}

		$response = wp_remote_retrieve_body( $request );

		Helper::log( '==============================================>' );
		Helper::log( $url );
		Helper::log( $headers );
		Helper::log( 'Request > ' );
		Helper::log( wp_json_encode( $body ) );
		Helper::log( 'Response > ' );
		Helper::log( $response );

		return json_decode( $response, true );
	}

	/**
	 * Executes Post Request
	 *
	 * @param string $endpoint URL Target Request.
	 * @param array  $body Data to send.
	 * @param array  $headers HTTP Headers for Requests.
	 * @return string
	 */
	public function post(
		string $endpoint,
		array $body = array(),
		array $headers = array()
	) {
		$url = $this->get_base_url() . $endpoint;
		return $this->exec( 'POST', $url, $body, $headers );
	}

	/**
	 * Executes Get Request
	 *
	 * @param string $endpoint URL Target Request.
	 * @param array  $body Data to send.
	 * @param array  $headers HTTP Headers for Requests.
	 * @return string
	 */
	public function get( string $endpoint, array $body = array(), array $headers = array() ) {
		$url = $this->get_base_url() . $endpoint;
		if ( ! empty( $body ) ) {
			$url .= '?' . http_build_query( $body );
		}
		return $this->exec( 'GET', $url, array(), $headers );
	}

	/**
	 * Executes Put Request
	 *
	 * @param string $endpoint URL Target Request.
	 * @param array  $body Data to send.
	 * @param array  $headers HTTP Headers for Requests.
	 * @return string
	 */
	public function put( string $endpoint, array $body = array(), array $headers = array() ) {
		$url = $this->get_base_url() . $endpoint;
		return $this->exec( 'PUT', $url, $body, $headers );
	}

	/**
	 * Executes Patch Request
	 *
	 * @param string $endpoint URL Target Request.
	 * @param array  $body Data to send.
	 * @param array  $headers HTTP Headers for Requests.
	 * @return string
	 */
	public function patch(
		string $endpoint,
		array $body = array(),
		array $headers = array()
	) {
		$url = $this->get_base_url() . $endpoint;
		return $this->exec( 'PATCH', $url, $body, $headers );
	}

	/**
	 * Executes Delete Request
	 *
	 * @param string $endpoint URL Target Request.
	 * @param array  $body Data to send.
	 * @param array  $headers HTTP Headers for Requests.
	 * @return string
	 */
	public function delete(
		string $endpoint,
		array $body = array(),
		array $headers = array()
	) {
		$url = $this->get_base_url() . $endpoint;
		return $this->exec( 'DELETE', $url, $body, $headers );
	}

	/**
	 * Get Base Url
	 */
	abstract public function get_base_url();
}
