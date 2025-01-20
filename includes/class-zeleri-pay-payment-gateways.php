<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

require_once( zeleri_root_dir() . 'includes/class-zeleri-pay-api.php' );
require_once( zeleri_root_dir() . 'includes/class-zeleri-pay-signature.php' );

#[AllowDynamicProperties]
class Zeleri_Pay_Payment_Gateways extends WC_Payment_Gateway {

    const ID = 'zeleri_pay_payment_gateways';
    const PAYMENT_GW_DESCRIPTION = 'Permite el pago de productos y/o servicios, con tarjetas de crédito, débito y prepago a través de Zeleri';

    public function __construct() {
        $this->id = self::ID;
        $this->icon = zeleri_root_url() . 'admin/images/logo-zeleri-tarjetas.png';
        $this->method_title = __('Zeleri Tarjetas', 'zeleri-woo-oficial');
        $this->title =  '';
        $this->method_description  = $this->get_option('zeleri_payment_gateway_description', self::PAYMENT_GW_DESCRIPTION);
        $this->description  = $this->get_option('zeleri_payment_gateway_description', self::PAYMENT_GW_DESCRIPTION);

         /**
         * Carga configuración y variables de inicio.
         **/
        $this->init_form_fields();
        $this->init_settings();

        add_action('woocommerce_update_options_payment_gateways_' . $this->id, [$this, 'process_admin_options']);
        add_action('woocommerce_api_zeleri_pay_payment_gateways_success', [$this, 'check_ipn_response_success']);
        add_action('woocommerce_api_zeleri_pay_payment_gateways_fail', [$this, 'check_ipn_response_fail']);

        if (!$this->is_valid_for_use()) {
            $this->enabled = false;
        }

        $this->gateway_id = 1; //Tarjetas de crédito y débito bancarias
    }

    /**
     * Inicializar campos de formulario (Configuracion).
     **/
    public function init_form_fields() { 
        $zeleriKeyDescription = 'Puedes solicitar la Zeleri Key en soporte@zeleri.com';

        $apiKeyDescription = 'Puedes solicitar la API Key en soporte@zeleri.com';

        $this->form_fields = array(
            'enabled' => array(
                'title'     => __('Activar/Desactivar plugin:', 'zeleri-woo-oficial'),
                'type'      => 'checkbox',
                'label'     =>  __('Activar/Desactivar', 'zeleri-woo-oficial'),
                'desc_tip'  => __('Title displayed during checkout.', 'zeleri-woo-oficial'),
                'default'   => 'yes',
                'class'     => 'form-control'
            ),
            'zeleri_payment_gateway_secret' => array(
                'title'     => __('API Key (llave secreta) Produccion:', 'zeleri-woo-oficial'),
                'type'      => 'text',
                'desc_tip'  => __('Puedes solicitar la API Key en soporte@zeleri.com', 'zeleri-woo-oficial'),
                'default'   => '',
                'class'     => 'form-control',
                'required'  => true
            ),
            'zeleri_payment_gateway_token' => array(
                'title'     => __('Zeleri Key:', 'zeleri-woo-oficial'),
                'type'      => 'text',
                'desc_tip'  => __('Puedes solicitar la Zeleri Key en soporte@zeleri.com', 'zeleri-woo-oficial'),
                'default'   => '',
                'class'     => 'form-control',
                'required'  => true
            ),
            'zeleri_payment_gateway_order_status' => array(
                'title'     => __('Estado de la orden', 'zeleri-woo-oficial'),
                'type'      => 'select',
                'desc_tip'  => __('Selecciona el estado que tendrá la orden por defecto al finalizar una compra.', 'zeleri-woo-oficial'),
                'options'   => [
                    'on-hold'    => 'En espera',
                    'processing' => 'Procesando',
                    'completed'  => 'Completada',
                ],
                'default'   => '',
                'class'     => 'form-control'
            ),
            'zeleri_payment_gateway_description' => array(
                'title'     => __('Descripcion medio de pago:', 'zeleri-woo-oficial'),
                'type'      => 'textarea',
                'desc_tip'  => __('Describe el medio de pago que verá el usuario en la pantalla de pago.', 'zeleri-woo-oficial'),
                'default'   => '',
                'class'     => 'form-control',
                'required'  => true
            ),
        );
    }

    /**
     * Obtiene respuesta IPN (Instant Payment Notification).
     **/
    public function check_ipn_response_success() {
        ob_clean();
        global $woocommerce;

        $zeleri_nonce = isset( $_GET['zeleri_nonce'] ) ? sanitize_text_field( wp_unslash( $_GET['zeleri_nonce'] ) ) : '';

        if (isset($_POST) && wp_verify_nonce($zeleri_nonce, 'zeleri_nonce_action')) {
            header('HTTP/1.1 200 OK');
            $data = ( isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'GET' ) ? map_deep( $_GET, 'sanitize_text_field' ) : map_deep( $_POST, 'sanitize_text_field' );
            $title_split = explode(':', $data['title']);
            $order_id = intval(trim($title_split[1]));
            $order = wc_get_order( $order_id );
            $setting_order_status = $this->get_option('zeleri_payment_gateway_order_status', 'on-hold');

            $order->update_meta_data( 'zeleri_description', $data['description'] );
            $order->update_meta_data( 'zeleri_payment_date', $data['payment_date'] );
            $order->update_meta_data( 'zeleri_order', $data['order'] );
            $order->update_meta_data( 'zeleri_authorization_code', $data['authorization_code'] );
            $order->update_meta_data( 'zeleri_card_number', $data['card_number'] );
            $order->update_meta_data( 'zeleri_commerce_name', $data['commerce_name'] );
            $order->update_meta_data( 'zeleri_commerce_id', $data['commerce_id'] );
            $order->update_meta_data( 'zeleri_status', 'aceptado' );
            $order->update_meta_data( 'zeleri_error', '' );
            $order->update_meta_data( 'zeleri_details_error', '' );

            $order->set_transaction_id( $data['order'] );
            $order->update_status($setting_order_status); 

            if( $setting_order_status == 'completed' ){
                /* 
                    Se debe usar el metodo "payment_complete()" ya que desencadena 
                    las acciones necesarias para completar el pedido 
                    (notificar al cliente, reducir stock etc,)".
                */
                $order->payment_complete(); 
            }

            $woocommerce->cart->empty_cart();
            $redirect_url = $order->get_checkout_order_received_url();
            $order->add_order_note( wp_json_encode($data) );
            $order->save();

        } else {
            wc_add_notice( __('Zeleri Payment Error: ', 'zeleri-woo-oficial') . 'No response from the server', 'error' );
            $redirect_url = wc_get_checkout_url();
        }

        return wp_safe_redirect($redirect_url);
    }



    public function check_ipn_response_fail() {
        ob_clean();
        global $woocommerce;

        $zeleri_nonce = isset( $_GET['zeleri_nonce'] ) ? sanitize_text_field( wp_unslash( $_GET['zeleri_nonce'] ) ) : '';

        if (isset($_POST) && wp_verify_nonce($zeleri_nonce, 'zeleri_nonce_action')) {
            header('HTTP/1.1 200 OK');
            $data = (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'GET') ? map_deep( $_GET, 'sanitize_text_field' ) : map_deep( $_POST, 'sanitize_text_field' );
            $title_split = explode(':', $data['title']);
            $order_id = intval(trim($title_split[1]));
            $order = wc_get_order( $order_id );

            $data['code'] = ( isset($data['code']) ) ? $data['code'] : '1999999999';
            $data['msg'] = ( isset($data['msg']) ) ? $data['msg'] : 'Error Zeleri Response';
            $order->update_meta_data( 'zeleri_status', 'rechazado' );
            $order->update_meta_data( 'zeleri_error', $data['code'] );
            $order->update_meta_data( 'zeleri_details_error', $data['msg'] );
            $order->update_status( 'failed', $data['msg'] );

            wc_add_notice( __('Zeleri Payment Error: ', 'zeleri-woo-oficial') . $data['msg'], 'error' );

            $order->add_order_note( wp_json_encode($data) );
            $order->save();

        } else {
            wc_add_notice( __('Zeleri Payment Error: ', 'zeleri-woo-oficial') . 'No response from the server', 'error' );
        }

        $redirect_url = wc_get_checkout_url();
        return wp_safe_redirect($redirect_url);
    }

    /**
     * Procesar pago y retornar resultado.
     **/
    public function process_payment($order_id) {
        try {
            $secret = $this->get_option('zeleri_payment_gateway_secret');
            $token = $this->get_option('zeleri_payment_gateway_token');
            $order = wc_get_order( $order_id );
            $apiZeleri = new Zeleri_pay_API();
            $signatureZeleri = new Zeleri_pay_Signature($secret);
            $payload = array(
                "amount" => (int) number_format($order->get_total(), 0, ',', ''),
                "gateway_id"  => $this->gateway_id,
                "title"       => "Order: ".$order->get_id(),
                "description" => "Pago checkout woocommerce",
                "currency_id" => 1,
                "customer"    => [
                    "email" => $order->get_billing_email(), // Use order billing email
                    "name"  => $order->get_billing_first_name() . ' ' . $order->get_billing_last_name(), // Use order billing name
                ],
                "success_url" => esc_url( home_url('/index.php/wc-api/zeleri_pay_payment_gateways_success/?zeleri_nonce=' . wp_create_nonce( 'zeleri_nonce_action' )) ),
                "failure_url" => esc_url( home_url('/index.php/wc-api/zeleri_pay_payment_gateways_fail/?zeleri_nonce=' . wp_create_nonce( 'zeleri_nonce_action' )) )
            );
            
            $signature = $signatureZeleri->generate($payload);
            $payload["signature"] = $signature;

    
            $createResponse = $apiZeleri->crear_orden_zeleri($payload, $token);
            if( is_wp_error($createResponse) ) {
                throw new Exception($createResponse->get_error_code().' '.$createResponse->get_error_message());
            }

            return [
                'result' => 'success',
                'redirect' => $createResponse->data->url,
            ];
    
        } catch (Exception  $e) {
            throw new Exception( esc_html($e->getMessage()) );
        }
    }
    
    /**
     * Opciones panel de administración.
     **/
    public function admin_options() {
        include_once zeleri_root_dir(). 'admin/partials/zeleri-admin-display.php';
    }

    /**
     * Comprueba configuración de moneda (Peso Chileno).
     **/
    public static function is_valid_for_use() {
        return in_array(get_woocommerce_currency(), ['CLP']);
    }

}

#[AllowDynamicProperties]
class Zeleri_Pay_Payment_Gateways_TB extends Zeleri_Pay_Payment_Gateways {

    const ID = 'zeleri_pay_payment_gateways_tb';
    const PAYMENT_GW_DESCRIPTION = 'Permite el pago de productos y/o servicios, con transferencias bancarias a través de Zeleri';

    public function __construct() {
        $this->id = self::ID;
        $this->icon = zeleri_root_url() . 'admin/images/logo-zeleri-transferencia.png';
        $this->method_title = __('Zeleri Transferencia', 'zeleri-woo-oficial');
        $this->title = '';
        $this->method_description  = $this->get_option('zeleri_payment_gateway_description', self::PAYMENT_GW_DESCRIPTION);
        $this->description  = $this->get_option('zeleri_payment_gateway_description', self::PAYMENT_GW_DESCRIPTION);


        /**
         * Carga configuración y variables de inicio.
         **/
        $this->init_form_fields();
        $this->init_settings();

        add_action('woocommerce_update_options_payment_gateways_' . $this->id, [$this, 'process_admin_options']);

        $this->gateway_id = 2; //Transferencias bancarias
    }
}