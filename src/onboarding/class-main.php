<?php
namespace Ecomerciar\AstroPay\Onboarding;

use Ecomerciar\AstroPay\Helper\Helper;
use Ecomerciar\AstroPay\Sdk\AstroPaySdk;

defined('ABSPATH') || exit;

/**
 * Main Onboarding Class
 */
class Main {

  /**
  * Register Onboarding Page
  */
  public static function register_onboarding_page(){
    add_options_page('Onboarding - AstroPay', 'Onboarding - AstroPay', 'manage_options', 'wc-astropay-onboarding', ['\Ecomerciar\AstroPay\Onboarding\Main', 'content'] );
  }

  /**
  * Get content
  */
   public static function content(){
    $data = [];
    $data['register'] = 'NONE';
    $data['exit_url'] = get_admin_url(null, '');
    $data['settings_url'] = get_admin_url(null, 'admin.php?page=wc-settings&tab=checkout&section=wc_astropay');   
    if(isset($_POST['option_page']) && 'wc-astropay-settings-onboarding' === $_POST['option_page'] ){
      if(isset($_POST['submit'])){
        if(!empty(filter_var($_POST['wc_astropay_api_key_sandbox'], FILTER_SANITIZE_STRING) && !empty(filter_var($_POST['wc_astropay_api_key_sandbox'], FILTER_SANITIZE_STRING))) && !empty(filter_var($_POST['wc_astropay_api_key_sandbox'], FILTER_SANITIZE_STRING))){
          Helper::set_option('api_key_sandbox', filter_var($_POST['wc_astropay_api_key_sandbox'], FILTER_SANITIZE_STRING));      
          Helper::set_option('api_secret_sandbox', filter_var($_POST['wc_astropay_api_secret_sandbox'], FILTER_SANITIZE_STRING));      
          Helper::set_option('sdk_api_key_sandbox', filter_var($_POST['wc_astropay_sdk_api_key_sandbox'], FILTER_SANITIZE_STRING));    
          /*Helper::set_option('merchant_category_code', filter_var($_POST['wc_astropay_merchant_category_code'], FILTER_SANITIZE_STRING));*/
          $data['register'] = 'OK';
          
          $sdk = new AstroPaySdk(Helper::get_option('api_key_sandbox'), Helper::get_option('api_secret_sandbox'), Helper::get_option('sdk_api_key_sandbox'));
          if ( ! $sdk->validate_api_key() ){
            $data['register'] = 'NOK';
          }

        } else {
          $data['register'] = 'NOK';
        }
        /*
        $sdk = new PickitSdk();       
        if($sdk->register()){
          $data['register'] = 'OK';
        } else {
          $data['register'] = 'NOK';
        }*/

       
      }
    }  
    $data['api-key'] = Helper::get_option('api_key_sandbox');
    $data['api-secret'] = Helper::get_option('api_secret_sandbox');
    $data['sdk-api-key'] = Helper::get_option('sdk_api_key_sandbox');
    $data['merchant_category_code'] = Helper::get_option('merchant_category_code');
    helper::get_template_part('page', 'onboarding',  $data );
    wp_enqueue_style('wc-astropay-onboarding');
   } 
}
