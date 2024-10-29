<?php
namespace Ecomerciar\AstroPay\Helper;

/**
 * Templates Trait
 */
trait TemplatesTrait {

  /**
   * Get Template Part
   *
   * @param string $slug
   * @param string $name
   */
  public static function get_template_part( $slug, $name = null, $args = [] ) {
   do_action( "astropay_get_template_part_{$slug}", $slug, $name, $args );
   $templates = array();
   if ( isset( $name ) ){
       $templates[] = "{$slug}-{$name}.php";
     }

   $templates[] = "{$slug}.php";

   self::get_template_path( $templates, true, false, $args );
  }

  /**
   * Get Template Path & Load
   *
   * @param string $template_names
   * @param bool $load
   * @param bool $require_once
   */
  public static function get_template_path( $template_names, $load = false, $require_once = true, $args = [] ) {
     $located = '';
      foreach ( (array) $template_names as $template_name ) {
        if ( !$template_name ){
          continue;
        }
        // search file within the PLUGIN_DIR_PATH only
        if ( file_exists( \AstroPay::MAIN_DIR . "/". "templates" ."/". $template_name ) ) {
          $located = \AstroPay::MAIN_DIR . "/". "templates" ."/". $template_name;
          break;
        }
      }

      if ( $load && '' != $located ){
          load_template( $located, $require_once, $args );
        }
      return $located;
  }
}
