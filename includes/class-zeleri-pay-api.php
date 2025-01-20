<?php 

if ( ! class_exists( 'Zeleri_Pay_API' ) ) {
	
	#[AllowDynamicProperties]
	class Zeleri_Pay_API {

		const RESPONSE_KEY = 'response';
		const CODE_KEY = 'code';
		/**
		 * Constructor for your shipping class 
		 *
		 * @access public
		 * @return void
		 */
		public function __construct() {
			$this->id = 'zeleri_pay_payment_gateways';
			$this->init();

			$this->api_production_base_url = 'https://zeleri-qa.dev.ionix.cl/integration-kit/v1';
		}

		public function init(){
			// Init is required by wordpress
		}

		public function crear_orden_zeleri($payload, $customer_token) {

			$url = $this->api_production_base_url.'/checkout/orders';
			$response = $this->do_remote_post($url, $customer_token, $payload );

			if ( is_wp_error( $response ) ) {

				$result = $response;

			} else {

				if ($response[self::RESPONSE_KEY][self::CODE_KEY] == 201) {
			   		
			   	$result = json_decode($response['body']);
					return $result;

				} else if ($response[self::RESPONSE_KEY][self::CODE_KEY] == 400) {

					$json_response = json_decode($response['body']);
					$result = new WP_Error('Zeleri API Error: [' . $json_response->code . '] ' . $json_response->message);

			  } else {

						$result = new WP_Error('Zeleri API Error: [' . $response[self::RESPONSE_KEY][self::CODE_KEY] . '] ' . $response[self::RESPONSE_KEY]['message']);
				}
			}
			return $result;
		}

		private function do_remote_post($url, $token, $signedPayload){
			
			// Convertir el signedPayload a JSON
			$signedPayload = wp_json_encode( $signedPayload );
			// Reemplazar las comillas dobles diferentes por comillas dobles estándar
			//$signedPayload = str_replace('"', '"', $signedPayload);
			//$signedPayload = str_replace('"', '"', $signedPayload);

			return wp_remote_post( $url, array(
					'method' 			=> 'POST',
					'timeout' 		=> 90,
					'redirection' => 5,
					'httpversion' => '1.0',
					'blocking'    => true,
					'headers'     => array(
						'Content-Type' => 'application/json',
						'Authorization' => 'Bearer '.$token
					),
					'body' 				=> $signedPayload,
					'cookies' 		=> array(),
					'sslverify' 	=> FALSE
			  )
			);
		}

		private function do_remote_get($url, $token){
			return wp_remote_get( $url, array(
					'method' 			=> 'GET',
					'timeout' 		=> 90,
					'redirection' => 5,
					'httpversion' => '1.0',
					'blocking'    => true,
					'headers'     => array(
						'Content-Type' 					 => 'application/json',
						'Authorization: Bearer ' => $token
					),
					'body' 				=> '',
					'cookies' 		=> array(),
					'sslverify' 	=> FALSE
			  )
			);
		}
		
	}
}
