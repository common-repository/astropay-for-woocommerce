<?php
/**
 * Class LoggerTrait
 *
 * @package  Ecomerciar\Astropay\Helper\LoggerTrait
 */

namespace Ecomerciar\Astropay\Helper;

/**
 * Logger Trait
 */
trait LoggerTrait {

	private static $logger;
	private static $source = \AstroPay::PLUGIN_NAME;

	/**
	 * Clean Logger (for testing purpouses)
	 */
	public static function clean() {
		self::$logger = null;
	}

	/**
	 * Inits our logger singleton
	 *
	 * @return void
	 */
	public static function init() {
		if ( function_exists( 'wc_get_logger' ) && ! isset( self::$logger ) ) {
			self::$logger = wc_get_logger();
		}
		return self::$logger;
	}

	/**
	 * Logs an info message
	 *
	 * @param mixed $msg
	 * @return void
	 */
	public static function log_info( $msg ) {
		self::$logger->info(
			wc_print_r( $msg, true ),
			array(
				'source' => self::$source,
			)
		);
	}

	/**
	 * Logs an error message
	 *
	 * @param mixed $msg
	 * @return void
	 */
	public static function log_error( $msg ) {
		self::$logger->error(
			wc_print_r( $msg, true ),
			array(
				'source' => self::$source,
			)
		);
	}

	/**
	 * Logs an warning message
	 *
	 * @param mixed $msg
	 * @return void
	 */
	public static function log_warning( $msg ) {
		self::$logger->warning(
			wc_print_r( $msg, true ),
			array(
				'source' => self::$source,
			)
		);
	}

	/**
	 * Logs a debug message
	 *
	 * @param mixed $msg
	 * @return void
	 */
	public static function log_debug( $msg ) {
		self::$logger->debug(
			wc_print_r( $msg, true ),
			array(
				'source' => self::$source,
			)
		);
	}
}
