<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://zeleri.com/
 * @since      1.0.2
 *
 * @package    Zeleri Pay
 * @subpackage Zeleri Pay/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.2
 * @package    Zeleri Pay
 * @subpackage Zeleri Pay/includes
 * @author     Zeleri <jose.rodriguez.externo@ionix.cl>
 */
class Zeleri_Pay_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.2
	 */
	public static function deactivate() {

		$id_imagen = get_option('zeleri_id_imagen_logo'); 
		if ($id_imagen) { 
			wp_delete_attachment($id_imagen, true); 
			delete_option('zeleri_id_imagen_logo');
		}

	}
}