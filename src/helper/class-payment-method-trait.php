<?php
/**
 * Assets Trait
 *
 * @package Ecomerciar\AstroPay\Helper
 */

namespace Ecomerciar\AstroPay\Helper;

trait PaymentMethodTrait {


    public static function get_payment_methods(){
        return [
            'AB' => 'Agribank',
            'AE' => 'American Express',
            'AL' => 'Airtel Money',
            'AS' => 'Asia Commercial Bank',
            'BB' => 'Banco Brasil',
            'BC' => 'BCP',
            'BD' => 'BIDV Bank',
            'BL' => 'Boleto',
            'BQ' => 'Banorte',
            'BT' => 'Bitcoin',
            'BU' => 'Baloto',
            'BV' => 'Bancomer',
            'BW' => 'Bodega Aurrera',
            'BX' => 'Banco de Chile',
            'BY' => 'CIMB Bank Berhad',
            'CR' => 'Carulla',
            'CU' => 'Circulo K',
            'CZ' => 'Bank Central Asia',
            'DC' => 'Diners',
            'DO' => 'DongA Bank',
            'EF' => 'PagoEfectivo',
            'EL' => 'Elo',
            'EM' => 'Eximbank',
            'EX' => 'Almacenes Exito',
            'EY' => 'Efecty',
            'FA' => 'Farmacias del ahorro',
            'FB' => 'Farmacia Benavides',
            'FC' => 'FreeCharge',
            'FO' => 'Facilito',
            'GO' => 'Google Pay',
            'GS' => 'Government Savings Bank',
            'HC' => 'Caja Huancayo',
            'HO' => 'Hong Leong Bank Berhad',
            'HP' => 'Hipercard',
            'IA' => 'Itau',
            'IB' => 'InterBank',
            'IG' => 'Internet Banking',
            'IR' => 'Interac Online',
            'IX' => 'Pix',
            'JM' => 'Jio Money',
            'KB' => 'Bangkok Bank',
            'KR' => 'Krungsri',
            'KS' => 'Kasikorn Bank',
            'KT' => 'Krung Thai Bank',
            'LC' => 'Loterias Caixa',
            'MB' => 'Mandiri Bank',
            'MC' => 'Mastercard',
            'MJ' => 'Multicaja',
            'MM' => 'Mobile Money',
            'MP' => 'M-Pesa',
            'MY' => 'Maybank Berhad',
            'NB' => 'Net Banking India',
            'NG' => 'Bank Negara Indonesia',
            'OM' => 'Ola Money',
            'OX' => 'OXXO',
            'PC' => 'PSE',
            'PH' => 'PhonePe',
            'PM' => 'Perfect Money',
            'PU' => 'Public Bank Berhad',
            'RH' => 'RHB Banking Group',
            'SA' => 'Siam Commercial Bank',
            'SC' => 'Santander',
            'SE' => 'Spei',
            'SF' => 'Banco Safra',
            'SJ' => 'Banco Sicredi',
            'SK' => 'Sacombank',
            'SS' => 'Sams Club',
            'SU' => 'Superama',
            'SX' => 'Surtimax',
            'TB' => 'Thai Military Bank',
            'TC' => 'ToditoCash',
            'TH' => 'Techcombank',
            'TL' => 'Trustly',
            'TR' => 'Bank Transfer',
            'UD' => 'Ussd',
            'UI' => 'UPI',
            'UL' => 'Banrisul',
            'US' => 'Caja Cusco',
            'VC' => 'Virtual Account - Bank Negara Indonesia',
            'VE' => 'Verve',
            'VI' => 'Visa',
            'VJ' => 'Virtual Account - Mandiri Bank Indonesia',
            'VM' => 'Virtual Account - MayBank Indonesia',
            'VN' => 'Vietin Bank',
            'VT' => 'Vietcombank',
            'WA' => 'Walmart',
            'WP' => 'WebPay',
            'WU' => 'Western Union',
            'ZB' => 'Zimpler Banking',
            'ZP' => 'Zimpler'
        ];
    }

	/**
	 * Gets Assets Folder URL
	 *
	 * @return string
	 */
	public static function get_payment_method_descr( $code ) {
		$paymentMethods = self::get_payment_methods();
        return (isset($paymentMethods[$code]))? $paymentMethods[$code] : $code;
	}

}
