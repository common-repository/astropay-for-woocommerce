<?php
/**
 * Class CountryCurrencyTrait
 *
 * @package  Ecomerciar\AstroPay\Helper\DebugTrait
 */

namespace Ecomerciar\AstroPay\Helper;

/**
 * Database Trait
 */
trait CountryCurrencyTrait {

	/**
     * Return the current transaction currency
	 * - Supports WOOCS currency switcher
	 */
    public static function get_currency() {
        if ( class_exists( 'WOOCS' ) ) {
            global $WOOCS;
            $currency = strtoupper( $WOOCS->storage->get_val( 'woocs_current_currency' ) );
        } else {
            $currency = get_woocommerce_currency();
        }
        return $currency;
	} 

	/**
	 * Get Rest of the World Countries
	 */
	public static function get_rotw_countries(){
		return array(
			'AL' => 	'ALL',		//Albania	
			'DZ' => 	'DZD', 	//Algeria									
			'AO' => 	'AOA', 	//Angola									
			'AR' => 	'ARS', 	//Argentina								
			'AM' => 	'AMD', 	//Armenia									
			'AU' => 	'AUD', 	//Australia								
			'AZ' => 	'AZN', 	//Azerbaijan								
			'BH' => 	'BHD', 	//Bahrain									
			'BD' => 	'BDT', 	//Bangladesh								
			'BT' => 	'BTN', 	//Bhutan									
			'BO' => 	'BOB', 	//Bolivia									
			'BR' => 	'BRL', 	//Brazil									
			'BF' => 	'XOF', 	//Burkina Faso							
			'BI' => 	'BIF', 	//Burundi									
			'CM' => 	'XAF', 	//Cameroon								
			'CA' => 	'CAD', 	//Canada									
			'CL' => 	'CLP', 	//Chile									
			'CN' => 	'CNY', 	//China									
			'CO' => 	'COP', 	//Colombia								
			'CG' => 	'XAF', 	//Congo									
			'CD' => 	'CDF', 	//Congo, the Democratic Republic of the	
			'CR' => 	'CRC', 	//Costa Rica								
			'CI' => 	'XOF', 	//Cote DIvoire							
			'DO' => 	'DOP', 	//Dominican Republic						
			'EC' => 	'USD', 	//Ecuador									
			'EG' => 	'EGP', 	//Egypt									
			'SV' => 	'SVC', 	//El Salvador								
			'SZ' => 	'SZL', 	//Eswatini								
			'ET' => 	'ETB', 	//Ethiopia								
			'GA' => 	'XAF', 	//Gabon									
			'GH' => 	'GHS', 	//Ghana									
			'GT' => 	'GTQ', 	//Guatemala								
			'GN' => 	'GNF', 	//Guinea									
			'GY' => 	'USD', 	//Guyana									
			'HN' => 	'HNL', 	//Honduras								
			'HK' => 	'HKD', 	//Hong Kong								
			'IN' => 	'INR', 	//India									
			'ID' => 	'IDR', 	//Indonesia								
			'JP' => 	'JPY', 	//Japan									
			'JO' => 	'JOD', 	//Jordan									
			'KZ' => 	'KZT', 	//Kazakhstan								
			'KE' => 	'KES', 	//Kenya									
			'KR' => 	'KRW', 	//Korea, Republic of						
			'KW' => 	'KWD', 	//Kuwait									
			'LS' => 	'LSL', 	//Lesotho									
			'LY' => 	'LYD', 	//Libyan Arab Jamahiriya					
			'MG' => 	'MGA', 	//Madagascar								
			'MY' => 	'MYR', 	//Malaysia								
			'MV' => 	'MVR', 	//Maldives								
			'MX' => 	'MXN', 	//Mexico									
			'MN' => 	'MNT', 	//Mongolia								
			'MA' => 	'MAD', 	//Morocco									
			'MZ' => 	'MZN', 	//Mozambique								
			'MM' => 	'MMK', 	//Myanmar									
			'NA' => 	'NAD', 	//Namibia									
			'NP' => 	'NPR', 	//Nepal									
			'NZ' => 	'NZD', 	//New Zealand								
			'NI' => 	'NIO', 	//Nicaragua								
			'NG' => 	'NGN', 	//Nigeria									
			'OM' => 	'OMR', 	//Oman									
			'PK' => 	'PKR', 	//Pakistan								
			'PA' => 	'USD', 	//Panama									
			'PY' => 	'PYG', 	//Paraguay								
			'PE' => 	'PEN', 	//Peru									
			'PH' => 	'PHP', 	//Philippines								
			'QA' => 	'QAR', 	//Qatar									
			'RW' => 	'RWF', 	//Rwanda									
			'SA' => 	'SAR', 	//Saudi Arabia							
			'SN' => 	'XOF', 	//Senegal									
			'RS' => 	'RSD', 	//Serbia									
			'SL' => 	'SLL', 	//Sierra Leone							
			'SG' => 	'SGD', 	//Singapore								
			'ZA' => 	'ZAR', 	//South Africa							
			'LK' => 	'LKR', 	//Sri Lanka								
			'TW' => 	'TWD', 	//Taiwan									
			'TZ' => 	'TZS', 	//Tanzania, United Republic of			
			'TH' => 	'THB', 	//Thailand								
			'TT' => 	'TTD', 	//Trinidad and Tobago						
			'TN' => 	'TND', 	//Tunisia									
			'TR' => 	'TRY', 	//Turkey									
			'UG' => 	'UGX', 	//Uganda									
			'AE' => 	'AED', 	//United Arab Emirates					
			'UY' => 	'UYU', 	//Uruguay									
			'UZ' => 	'UZS', 	//Uzbekistan								
			'VN' => 	'VND', 	//Vietnam									
			'ZM' => 	'ZMW', 	//Zambia									
			'ZW' => 	'ZWL', 	//Zimbabwe
			'RO' =>     'RON',  //Romania		
		);						
	 }

	 /**
	 * Get Europe Countries
	 */
	public static function get_europe_countries(){
		return array(
			'AD' => 	'EUR',		//Andorra					
			'BE' => 	'EUR',		//Belgium					
			'BA' => 	'BAM',		//Bosnia and Herzegovina	
			'BG' => 	'BGN',		//Bulgaria				
			'HR' => 	'HRK',		//Croatia					
			'CY' => 	'EUR',		//Cyprus					
			'CZ' => 	'CZK',		//Czech Republic			
			'EE' => 	'EUR',		//Estonia					
			'FI' => 	'EUR',		//Finland					
			'FR' => 	'EUR',		//France					
			'DE' => 	'EUR',		//Germany					
			'GI' => 	'GIP',		//Gibraltar				
			'GB' => 	'GBP',		//Great Britain			
			'GR' => 	'EUR',		//Greece					
			'HU' => 	'HUF',		//Hungary					
			'IS' => 	'ISK',		//Iceland					
			'IE' => 	'EUR',		//Ireland					
			'IT' => 	'EUR',		//Italy					
			'LV' => 	'EUR',		//Latvia					
			'LT' => 	'EUR',		//Lithuania				
			'MT' => 	'EUR',		//Malta					
			'ME' => 	'EUR',		//Montenegro				
			'NL' => 	'EUR',		//Netherlands				
			'NO' => 	'NOK',		//Norway					
			'PL' => 	'PLN',		//Poland					
			'PT' => 	'EUR',		//Portugal				
			'SK' => 	'EUR',		//Slovakia				
			'SI' => 	'EUR',		//Slovenia				
			'ES' => 	'EUR',		//Spain					
			'SE' => 	'SEK',		//Sweden					
			'CH' => 	'CHF',		//Switzerland				
		);
	}

	
	/**
	 * Get list of countries
	 */
	public static function get_countries(){
		return array_merge(
			self::get_rotw_countries(), 
			self::get_europe_countries()
		);
	}

	/**
	 * Check if valid country
	 */
	public static function validate_country_code($country){
		return isset(self::get_countries()[$country]);
	}

	/**
	 * Check if valid country/currency
	 */
	public static function validate_country_currency_code($country, $currency){
		$ret = false;
		if (self::validate_country_code($country)){
			$ret = (self::get_countries()[$country] == $currency);
		}
		return $ret;
	}

	/**
	 * Check if it's europe country
	 */
	public static function is_europe_country($country){
		return isset(self::get_europe_countries()[$country]);
	}

}