<?php
/**
 * Assets Trait
 *
 * @package Ecomerciar\AstroPay\Helper
 */

namespace Ecomerciar\AstroPay\Helper;

trait AssetsTrait {

	/**
	 * Gets Assets Folder URL
	 *
	 * @return string
	 */
	public static function get_assets_folder_url() {
		return plugin_dir_url( \AstroPay::MAIN_FILE ) . 'assets';
	}

}
