<?php
/**
 * Class PhoneTrait
 *
 * @package  Ecomerciar\AstroPay\Helper\PhoneTrait
 */

namespace Ecomerciar\AstroPay\Helper;

/**
 * Phone Trait
 */
trait PhoneTrait {

    public static function sanitize_phone_number( $number, $country ) {
		//Convert phone number to numeric value
		$phone = str_replace( array( '+', '-' ), '', filter_var( $number, FILTER_SANITIZE_NUMBER_INT ) );
		$phone = ltrim( $phone, '0' );

		//Obtain international country calling code for the given country
		$intl_prefix = WC()->countries->get_country_calling_code( $country );
        $intl_prefix = is_array( $intl_prefix ) ? $intl_prefix[0] : $intl_prefix;

		//Prefix to phone if not already added
		preg_match( "/(\d{1,4})[0-9.\- ]+/", $phone, $prefix );
		if ( strpos( $prefix[1], $intl_prefix ) !== 0 ) {
			$phone = $intl_prefix . $phone;
		}
		
		//Return the prefixed phone number
		return $phone;	
	}
}