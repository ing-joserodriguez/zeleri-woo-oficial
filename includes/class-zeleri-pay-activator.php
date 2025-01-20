<?php

/**
 * Fired during plugin activation
 *
 * @link       https://zeleri.com/
 * @since      1.0.2
 *
 * @package    Zeleri Pay
 * @subpackage Zeleri Pay/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.2
 * @package    Zeleri Pay
 * @subpackage Zeleri Pay/includes
 * @author     Zeleri <jose.rodriguez.externo@ionix.cl>
 */
class Zeleri_Pay_Activator {

	public static function zeleri_subir_imagen_logo() {
		global $wp_filesystem;

    if ( ! function_exists( 'WP_Filesystem' ) ) {
      require_once( ABSPATH . 'wp-admin/includes/file.php' );
    }

    WP_Filesystem();

		$plugin_dir = plugin_dir_path(__FILE__); // Obtiene la ruta del directorio del plugin
    $parent_dir = dirname($plugin_dir); // Obtiene la ruta del directorio superior
		$ruta_imagen = $parent_dir . '/admin/images/logo-zeleri.png';
		$nombre_imagen = 'logo-zeleri.png'; 
		$tipo_imagen = 'image/png'; 

		if ( ! $wp_filesystem->exists( $ruta_imagen ) ) {
			//error_log("El archivo no existe: " . $ruta_imagen);
			return false;
		}

		$image_data = $wp_filesystem->get_contents( $ruta_imagen );
    if ( $image_data === false ) {
      //error_log("Error al leer el archivo: " . $ruta_imagen);
      return false;
    }

		$subida = wp_upload_bits($nombre_imagen, null, $image_data);

		if (!$subida['error']) {
			$archivo = array( 
				'guid' => $subida['url'], 
				'post_mime_type' => $tipo_imagen, 
				'post_title' => preg_replace('/\.[^.]+$/', '', $nombre_imagen), 
				'post_content' => '', 
				'post_status' => 'inherit' 
			); 
			$id_adjunto = wp_insert_attachment($archivo, $subida['file']); 
			
			require_once(ABSPATH . 'wp-admin/includes/image.php'); 
			$metadata = wp_generate_attachment_metadata($id_adjunto, $subida['file']); 
			wp_update_attachment_metadata($id_adjunto, $metadata); 
			return $id_adjunto; 
		} 

		return false; 
	}

	public static function activate() {

		if (!get_option('zeleri_id_imagen_logo')) {
			$id_imagen = self::zeleri_subir_imagen_logo();
			if ($id_imagen) {
				update_option('zeleri_id_imagen_logo', $id_imagen); 
			}
		}

	}
}