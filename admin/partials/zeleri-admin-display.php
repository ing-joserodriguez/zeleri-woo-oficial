<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://https://zeleri.com/
 * @since      1.0.2
 *
 * @package    Zeleri
 * @subpackage Zeleri/admin/partials
 */

  // WP_List_Table is not loaded automatically so we need to load it in our application
	if( ! class_exists( 'WP_List_Table' ) ) {
		require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
	}

	require_once( zeleri_root_dir() . 'includes/class-zeleri-pay-table.php' );
?>

<?php 
	// check user capabilities
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
?>

<?php 
$zeleri_nonce = isset( $_GET['zeleri_nonce'] ) ? sanitize_text_field( wp_unslash( $_GET['zeleri_nonce'] ) ) : '';
$pagina_configuracion = '';

if ( wp_verify_nonce( $zeleri_nonce, 'zeleri_nonce_action' ) ) {
	if( isset($_GET['section']) && $_GET['section'] === 'zeleri_pay_payment_gateways'){
		$pagina_configuracion = 'Tarjetas';
	}
	else{
		$pagina_configuracion = 'Transferencias';	
	}
}
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<!--<div class="container menu-principal p-5 my-5">-->
	<!--<div class="container"> -->
			<div class="row">
				<div class="col-md-2">
					<ul class="nav nav-tabs flex-column">
						<li class="nav-item">
							<a class="nav-link nav-link-zeleri <?php if(!isset($_GET['tab_pane'])) echo 'active'; ?>" data-toggle="tab" href="#tabZeleriInicio">Inicio <i class="ph-bold ph-caret-right"></i></a>
						</li>
						<li class="nav-item">
							<a class="nav-link nav-link-zeleri <?php if( isset($_GET['tab_pane']) && $_GET['tab_pane'] == 'tabZeleriTransacciones' )  echo 'active'; ?>" data-toggle="tab" href="#tabZeleriTransacciones">Transacciones <i class="ph-bold ph-caret-right"></i></a>
						</li>
						<li class="nav-item">
							<a class="nav-link nav-link-zeleri <?php if( isset($_GET['tab_pane']) && $_GET['tab_pane'] == 'tabZeleriConfiguracion' )  echo 'active'; ?>" data-toggle="tab" href="#tabZeleriConfiguracion">Configuración <i class="ph-bold ph-caret-right"></i></a>
						</li>
					</ul>
				</div>
				<div class="col-md-10">
					<div class="tab-content">
						<!--INICIO-->
						<div id="tabZeleriInicio" class="tab-pane fade <?php if(!isset($_GET['tab_pane'])) echo 'show active'; ?>">
							<div class="container">
								<div class="row">
									<div class="col" style="padding: 0px; margin-bottom: 20px;">
										<?php echo wp_get_attachment_image( get_option('zeleri_id_imagen_logo'), 'full', false, array( 'height' => '70px', 'alt' => 'Zeleri logo' ) ) ?>
									</div>
								</div>
								<div class="row zeleri-row-header">
									<div class="col zeleri-header-verification-plugin">

										<h3>¡Te damos la bienvenida a Zeleri <?php echo esc_html($pagina_configuracion); ?>!</h3>
										<p>Asegúrate de completar todos los pasos para comenzar a operar con el plugin de Zeleri en tu comercio.</p>
									</div>
								</div> 
								<div class="row">
									<div class="col">
										<ul class="zeleri-verificacion-plugin">
											
											<li>
												<div class="row">
													<div class="col">
														<i class="ph-bold ph-number-circle-one"></i>
														<p>Plugin de Zeleri instalado</p>
													</div>
												</div>
											</li>
											
											<li>
												<div class="row">
													<div class="col">
														<i class="ph-bold ph-number-circle-two"></i>
														<p>Mi comercio está activo en Zeleri </br>
															<span class="xs">Puedes verificar el estado de tu comercio en el portal Zeleri.</span>
														</p>
													</div>
													<div class="col">
														<a href="https://portal.zeleri.com/login" target="_blank">Verificar</a>
													</div>
												</div>
											</li>
											
											<li>
												<div class="row">
													<div class="col">
														<i class="ph-bold ph-number-circle-three"></i>
														<p>Mis llaves de API y Zeleri están activas para operar </br> 
															<span class="xs">Solicita tus llaves en soporte@zeleri.com</span>
														</p>
													</div>
													<div class="col">
														<a href="mailto:soporte@zeleri.com?subject=Solicitud LLaves de API y Zeleri">Verificar</a>
													</div>
												</div>
											</li>
											
											<li>
												<div class="row">
													<div class="col">
														<i class="ph-bold ph-number-circle-four"></i>
														<p>Configuracion del plugin Zeleri</p>
													</div>
													<div class="col">
														<a id="irZeleriConfiguracion" href="#">Ir a Configuracion</a>
													</div>
												</div>
											</li>

											<li>
												<div class="row">
													<div class="col">
														<i class="ph-bold ph-number-circle-five"></i>
														<p>Haz una compra real para validar que el plugin de Zeleri funciona perfectamente</p>
													</div>
												</div>
											</li>
										</ul>
									</div>
								</div>
								<div class="row zeleri-verificacion-plugin">
									<div class="col">
										<p>Si necesitas ayuda contáctanos.</p>
									</div>
								</div>
								<div class="row zeleri-verificacion-plugin">
									<div class="col">
										<i class="ph-bold ph-envelope"></i>
										<p>soporte@zeleri.com</p>
									</div>
									<div class="col">
										<i class="ph-bold ph-whatsapp-logo"></i>
										<p>+569 4420 9837</p>
									</div>
									<div class="col">
										<i class="ph-bold ph-phone"></i>
										<p>+56 2 2594 0647</p>
									</div>
								</div>
							</div>
						</div>
						<!--TRANSACCIONES-->
						<div id="tabZeleriTransacciones" class="tab-pane fade <?php if( isset($_GET['tab_pane']) && $_GET['tab_pane'] == 'tabZeleriTransacciones' )  echo 'show active'; ?>">
							<?php 
								$tablaTransaccionesZeleri = new Zeleri_Pay_Table();
								$tablaTransaccionesZeleri->prepare_items();
							?>
							<form method="post">
								<input type="hidden" name="page" value="<?php echo esc_url( admin_url('admin.php?page=wc-settings&tab=checkout&section=zeleri_pay_payment_gateways&tab_pane=tabZeleriTransacciones') ); ?>" />
								<?php $tablaTransaccionesZeleri->search_box( 'Buscar' , 'search_id' ); ?>
							</form>
							
							<a href="<?php echo esc_url( admin_url('admin.php?page=wc-settings&tab=checkout&section=zeleri_pay_payment_gateways&tab_pane=tabZeleriTransacciones') ); ?>">Mostrar Todo</a>

							<form method="post">
                	<?php $tablaTransaccionesZeleri->display(); ?>
            	</form>
						</div>
						<!--CONFIGURACION-->
						<div id="tabZeleriConfiguracion" class="tab-pane fade <?php if( isset($_GET['tab_pane']) && $_GET['tab_pane'] == 'tabZeleriConfiguracion' )  echo 'show active'; ?>">
							<table class="form-table" role="presentation">
								<?php $this->generate_settings_html(); ?>
							</table>
							<p class="submit zeleri-button-submit">
								<button name="save" class="button-primary woocommerce-save-button" type="submit" value="Guardar los cambios">Guardar los cambios</button>
								<?php wp_nonce_field( 'woocommerce-settings', '_wpnonce'); ?>
								<input type="hidden" name="_wp_http_referer" value="/wp-admin/admin.php?page=wc-settings&tab=checkout&section=zeleri_pay_payment_gateways&tab_pane=tabZeleriConfiguracion"/>
							</p>
						</div>
					</div>
				</div>
			</div>
		</div>
	<!--</div>-->
<!--</div>-->

