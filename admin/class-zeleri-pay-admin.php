<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://zeleri.com/
 * @since      1.0.2
 *
 * @package    Zeleri Pay
 * @subpackage Zeleri Pay/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Zeleri Pay
 * @subpackage Zeleri Pay/admin
 * @author     Zeleri <jose.rodriguez.externo@ionix.cl>
 */

 use Zeleri\WooCommerce\Zeleripay\Blocks\ZeleriPaymentMethodTDC\WCGatewayZeleriWebpayBlocks;
 use Zeleri\WooCommerce\Zeleripay\Blocks\ZeleriPaymentMethodTB\WCGatewayZeleriTBWebpayBlocks;

 #[AllowDynamicProperties]
class Zeleri_Pay_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.2
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.2
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.2
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->wc_version_since_hpos = '8.2';

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.2
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Zeleri_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Zeleri_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		
		wp_enqueue_style( $this->plugin_name.'admin-style', plugin_dir_url( __FILE__ ) . 'css/zeleri-admin.css', array(), $this->version, 'all' );
		
		wp_enqueue_style( $this->plugin_name.'admin-bootstrap', plugin_dir_url( __FILE__ ) . 'css/zeleri-bootstrap.css', array(), $this->version, 'all' );

		wp_enqueue_style( $this->plugin_name.'admin-phosphor-icons', plugin_dir_url( __FILE__ ) . 'css/zeleri-phosphor-icons-style.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.2
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Zeleri_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Zeleri_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/zeleri-admin.js', array( 'jquery' ), $this->version, false );

		wp_enqueue_script( $this->plugin_name.'admin-bootstrap', plugin_dir_url( __FILE__ ) . 'js/zeleri-bootstrap.js', array( 'jquery' ), $this->version, false );

	}

	public function index() {
		// CODE HERE
	}

	public function zeleri_woocommerce_init() {
		if ( $this->zeleri_woocommerce_is_active() ) {
			$this->zeleri_register_admin_menu();
			$this->zeleri_handle_review_notice();
			$this->zeleri_register_plugin_action_links();
		}
	}

	public function zeleri_register_payment_gateways() {
		if ( $this->zeleri_woocommerce_is_active() ) {
			require_once( zeleri_root_dir() . 'includes/class-zeleri-pay-payment-gateways.php' );
			add_filter('woocommerce_payment_gateways', function($methods) {
        $methods[] = 'Zeleri_Pay_Payment_Gateways';
				$methods[] = 'Zeleri_Pay_Payment_Gateways_TB';
        return $methods;
    	});
		}
	}

	public function zeleri_register_block_payment_gateway() { 
		if ( $this->zeleri_woocommerce_is_active() ) {
			add_action('woocommerce_blocks_loaded', function() {
				if ( class_exists( 'Automattic\WooCommerce\Blocks\Payments\Integrations\AbstractPaymentMethodType' ) ){
						
						require_once( zeleri_root_dir() . 'includes/blocks/ZeleriPaymentMethodTDC/WC_Gateway_Zeleri_Webpay_Blocks.php' ); 
						add_action(
								'woocommerce_blocks_payment_method_type_registration',
								function( Automattic\WooCommerce\Blocks\Payments\PaymentMethodRegistry $payment_method_registry ) {
										$payment_method_registry->register( new WCGatewayZeleriWebpayBlocks() );
								}
						);

						require_once( zeleri_root_dir() . 'includes/blocks/ZeleriPaymentMethodTB/WC_Gateway_Zeleri_TB_Webpay_Blocks.php' );
						add_action(
							'woocommerce_blocks_payment_method_type_registration',
							function( Automattic\WooCommerce\Blocks\Payments\PaymentMethodRegistry $payment_method_registry ) {
									$payment_method_registry->register( new WCGatewayZeleriTBWebpayBlocks() );
							}
						);

				}
			});
		}
	}

	public function zeleri_declare_hpos(){
		$hPosExists = $this->zeleri_check_if_hpos_exists();
		if ($hPosExists) {
			add_action('before_woocommerce_init', function () {
				if (class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class)) {
					\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility('custom_order_tables', dirname(plugins_url('/', __FILE__)), true);
				}
			});
		}
	}

	public function zeleri_register_admin_menu() {
		add_action('admin_menu', function () {
			add_submenu_page(
				'woocommerce',
				'Zeleri Tarjetas', // Título de la página
				'Zeleri Tarjetas', // Literal de la opción
				'manage_options', // Dejadlo tal cual
				'zeleri-pay-tdc', // Slug
				array( $this, 'zeleri_tdc_settings_view' ), // Función que llama al pulsar
				100 // Para colocarlo en la ultima posicion del submenu
			);

			add_submenu_page(
				'woocommerce',
				'Zeleri Transferencias', // Título de la página
				'Zeleri Transferencias', // Literal de la opción
				'manage_options', // Dejadlo tal cual
				'zeleri-pay-tb', // Slug
				array( $this, 'zeleri_tb_settings_view' ), // Función que llama al pulsar
				100 // Para colocarlo en la ultima posicion del submenu
			);
		});
	}

	public function zeleri_register_plugin_action_links() {
		
		$plugin_root = plugin_dir_path(dirname( __FILE__ ));
		$plugin_basename = plugin_basename($plugin_root).'/zeleri-woo-oficial.php';

    add_filter('plugin_action_links_'.$plugin_basename, function ($actionLinks) {
        $zeleriTDCSettingsLink = sprintf(
            '<a href="%s">%s</a>',
            admin_url('admin.php?page=wc-settings&tab=checkout&section=zeleri_pay_payment_gateways&zeleri_nonce=' . wp_create_nonce( 'zeleri_nonce_action' )),
            'Configurar Tarjetas'
        );
				$zeleriTBSettingsLink = sprintf(
					'<a href="%s">%s</a>',
					admin_url('admin.php?page=wc-settings&tab=checkout&section=zeleri_pay_payment_gateways_tb&zeleri_nonce=' . wp_create_nonce( 'zeleri_nonce_action' )),
					'Configurar Transferencias'
			);
        $newLinks = [
            $zeleriTDCSettingsLink,
						$zeleriTBSettingsLink
        ];

        return array_merge($actionLinks, $newLinks);
    });
	}

	public function zeleri_tdc_settings_view() {
		wp_redirect('./admin.php?page=wc-settings&tab=checkout&section=zeleri_pay_payment_gateways&zeleri_nonce=' . wp_create_nonce( 'zeleri_nonce_action' ));
    exit;
	}

	public function zeleri_tb_settings_view() {
		wp_redirect('./admin.php?page=wc-settings&tab=checkout&section=zeleri_pay_payment_gateways_tb&zeleri_nonce=' . wp_create_nonce( 'zeleri_nonce_action' ));
    exit;
	}

	public function zeleri_handle_review_notice() {
		add_action( 'admin_notices', array($this , 'zeleri_review_notice') );
	}
		
	public function zeleri_review_notice() {
		if ( isset( $_GET['section'] ) && $_GET['section'] === 'zeleri_pay_payment_gateways' || isset( $_GET['section'] ) && $_GET['section'] === 'zeleri_pay_payment_gateways_tb') {
			
			// Sanitizar el valor de $_GET['zeleri_nonce']
			$zeleri_nonce = isset( $_GET['zeleri_nonce'] ) ? sanitize_text_field( wp_unslash( $_GET['zeleri_nonce'] ) ) : '';

			if ( wp_verify_nonce( $zeleri_nonce, 'zeleri_nonce_action' ) ) {
				
				echo '<div class="notice notice-info is-dismissible" id="zeleri-review-notice">
						<div class="zeleri-notice">
								<div class="img-logo-zeleri">
										'.wp_get_attachment_image( get_option('zeleri_id_imagen_logo'), 'full', false, array( 'style' => 'width: 130px; height: 40px;', 'alt' => 'Zeleri logo' ) ).'
								</div>
								<div class="zeleri-review-text">
										<p class="zeleri-review-title">Tu opinión es importante para nosotros</p>
										<p>¿Podrías tomarte un momento para dejarnos una reseña en el repositorio de WordPress?
												Solo te tomará un par de minutos y nos ayudará a seguir mejorando y llegar a más personas como tú.</p>
								</div>
								<a class="button button-primary zeleri-button-primary"
										href="https://wordpress.org/support/plugin/zeleri-woo-oficial/reviews/#new-post"
										target="_blank" rel="noopener"
								>Dejar reseña</a>
						</div>
				</div>';
			}
		}
	}

	public function zeleri_woocommerce_is_active() {
		$woocommerce_is_present = false;

		if ( in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins'))) ) {
			$woocommerce_is_present = true;
		}

		return $woocommerce_is_present;
	}

	public function zeleri_check_if_hpos_exists(){
		$woocommerce_version = get_option( 'woocommerce_version' );
    return version_compare( $woocommerce_version, $this->wc_version_since_hpos, '>=');
  }

}
