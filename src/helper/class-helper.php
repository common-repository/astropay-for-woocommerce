<?php
/**
 * Helper Class
 *
 * @package  Ecomerciar\AstroPay\Helper
 */

namespace Ecomerciar\AstroPay\Helper;

/**
 * Helper Main Class
 */
class Helper {	   
   use TemplatesTrait;
   use AssetsTrait;
   use SettingsTrait;
   use LoggerTrait;
   use DebugTrait;
   use DatabaseTrait;
   use PaymentMethodTrait;
   use CountryCurrencyTrait;
   use PhoneTrait;
}
