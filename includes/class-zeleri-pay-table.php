<?php 

	if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
	
	class Zeleri_Pay_Table extends WP_List_Table
	{
	
	    /**
	     * Prepare the items for the table to process
	     *
	     * @return Void
	     */
	    public function prepare_items() {
	        $columns = $this->get_columns();
	        $hidden = $this->get_hidden_columns();
	        $sortable = $this->get_sortable_columns();

	        $data = $this->table_data();
	        usort( $data, array( &$this, 'sort_data' ) );

	        $i = 0;
          foreach ($data AS $key) {
              if (isset($key['fecha'])) {
                  $data[$i]['fecha'] = gmdate("d M, Y", strtotime($key['fecha']));
              }
              $i++;
          }

	        $perPage = 10;
	        $currentPage = $this->get_pagenum();
	        $totalItems = count($data);

	        $this->set_pagination_args( array(
	            'total_items' => $totalItems,
	            'per_page'    => $perPage
	        ) );

	        $data = array_slice($data,(($currentPage-1)*$perPage),$perPage);

	        $this->_column_headers = array($columns, $hidden, $sortable);
	        $this->items = $data;
	    }

	    /**
	     * Override the parent columns method. Defines the columns to use in your listing table
	     *
	     * @return Array
	     */
	    public function get_columns() {
	        $columns = array(
	            'trx_id'         			=> 'ID',
							'metodo_pago'         => 'Método de pago',
	            'order_woo'      			=> 'Orden WooCommerce',
	            'estado_woo'     			=> 'Estado interno',
	            'estado_zeleri'  			=> 'Estado Transaccion',
	            'orden_zeleri'   			=> 'Orden Compra Zeleri',
              'autorizacion_zeleri' => 'Codigo de Autorizacion Zeleri',
	            'monto'          		  => 'Monto',
              'fecha'          			=> 'Fecha creación',
              'fecha_zeleri'   			=> 'Fecha Transacción Zeleri',
              'error'          			=> 'Error',
              'detalle_error'  			=> 'Detalle de Error'
	        );

	        return $columns;
	    }

	    /**
	     * Define which columns are hidden
	     *
	     * @return Array
	     */
	    public function get_hidden_columns() {
	        return array("ID");
	    }

	    /**
	     * Define the sortable columns
	     *
	     * @return Array
	     */
	    public function get_sortable_columns() {
	    	$columns = array(
	    		'trx_id'       => array('trx_id', true),
          'order_woo'    => array('order_woo', true),
          'orden_zeleri' => array('orden_zeleri', true),
          'fecha'        => array('fecha', true),
					'fecha_zeleri' => array('fecha_zeleri', true)
	    	);
	        return $columns;
	    }

	    /**
	     * Get the table data
	     *
	     * @return Array
	     */
	    private function table_data() {

				$zeleri_nonce = isset( $_GET['zeleri_nonce'] ) ? sanitize_text_field( wp_unslash( $_GET['zeleri_nonce'] ) ) : '';

				$str = ( isset($_POST['s']) && wp_verify_nonce( $zeleri_nonce, 'zeleri_nonce_action' ) ) ? sanitize_text_field( wp_unslash($_POST['s']) ) : '';

				$args = array(
					'order'          => 'DESC',
					'orderby'        => 'date',
					'limit' 				 => -1, // Para obtener todos los registros
					'field_query' => array(
            'relation' => 'OR',
						array(
							'field'   => 'id',
							'value'   => $str,
							'compare' => 'LIKE',
						),
            array(
              'field'   => 'status',
              'value'   => $str,
              'compare' => 'LIKE',
            ),
						array(
							'field'   => 'transaction_id',
							'value'   => $str,
							'compare' => 'LIKE',
						),
						array(
							'field'   => 'date_created',
							'value'   => gmdate('Y-m-d', strtotime($str)),// Para formatear fecha a mysql
							'compare' => 'LIKE',
						),
						array(
							'field'   => 'total',
							'value'   => intval($str),
							'compare' => 'LIKE',
						),
        	)
				);

				$orders = wc_get_orders( $args );

			  $data = array();

			  foreach ($orders as $key => $order) {

					$payment_method = $order->get_payment_method();
				  
					if( $payment_method !== 'zeleri_pay_payment_gateways' &&  $payment_method !== 'zeleri_pay_payment_gateways_tb'){
						continue;
					}

					$metodo_pago = ($payment_method === 'zeleri_pay_payment_gateways') ? 'Webpay (Tarjetas)' : 'Fintoc (Transferencia)';

					$fecha = new DateTime( $order->get_date_created() );
					$fecha_zeleri = new DateTime( $order->get_meta('zeleri_payment_date') );

					$data[] = array(
            'trx_id'         			=> $order->get_id(),
						'metodo_pago'         => $metodo_pago,
            'order_woo'      			=> '<a href="'.admin_url('admin.php?page=wc-orders&action=edit&id='.$order->get_id()).'">Pedido #'.$order->get_order_number().'</a>',
            'estado_woo'     			=> $order->get_status(),
            'estado_zeleri'  			=> $order->get_meta('zeleri_status'),
            'orden_zeleri'   			=> $order->get_transaction_id(),
            'autorizacion_zeleri' => $order->get_meta('zeleri_authorization_code'),
            'monto'          			=> wc_price($order->get_total()),
            'fecha'          			=> $fecha->format('d M, Y'),
            'fecha_zeleri'   			=> $fecha_zeleri->format('d M, Y'),
            'error'          			=> $order->get_meta('zeleri_error'),
            'detalle_error'  			=> $order->get_meta('zeleri_details_error')
          );
			  }

	      return $data;
	    }

	    /**
	     * Define what data to show on each column of the table
	     *
	     * @param  Array $item        Data
	     * @param  String $column_name - Current column name
	     *
	     * @return Mixed
	     */
	    public function column_default( $item, $column_name ) {
	        switch( $column_name ) {
	            case 'trx_id':
							case 'metodo_pago':
	            case 'order_woo':
	            case 'estado_woo':
	            case 'estado_zeleri':
	            case 'orden_zeleri':
	            case 'autorizacion_zeleri':
	            case 'monto':
              case 'fecha':
              case 'fecha_zeleri':
              case 'error':
              case 'detalle_error':
	                return $item[ $column_name ];

	            default:
	                return $item[ $column_name ];
	        }
	    }

	    /**
	     * Allows you to sort the data by the variables set in the $_GET
	     *
	     * @return Mixed
	     */
	    private function sort_data( $a, $b ) {
	        // Set defaults
	        $orderby = 'trx_id';
	        $order = 'desc';

	        // If orderby is set, use this as the sort column
					$zeleri_nonce = isset( $_GET['zeleri_nonce'] ) ? sanitize_text_field( wp_unslash( $_GET['zeleri_nonce'] ) ) : '';

	        if(!empty($_GET['orderby']) && wp_verify_nonce( $zeleri_nonce, 'zeleri_nonce_action' )) {
	            $orderby = sanitize_text_field( wp_unslash($_GET['orderby']) );
	        }

	        // If order is set use this as the order
	        if(!empty($_GET['order'])) {
	            $order = sanitize_text_field( wp_unslash($_GET['order']) );
	        }

	        if($orderby == 'trx_id') {
	        	$_orderID1 = intval( $a[$orderby] );
	        	$_orderID2 = intval( $b[$orderby] );
	        	$result = ($_orderID1 > $_orderID2) ? +1 : -1;
	        }

	        if($orderby == 'fecha') {
	        	$_fecha1 = strtotime( $a[$orderby] );
	        	$_fecha2 = strtotime( $b[$orderby] );
	        	$result = ($_fecha1 > $_fecha2) ? +1 : -1;
	        }

          if($orderby == 'order_woo') {
            $_orderID1 = intval( $this->get_order_id( $a[$orderby]) );
	        	$_orderID2 = intval( $this->get_order_id( $b[$orderby]) );
	        	$result = ($_orderID1 > $_orderID2) ? +1 : -1;
          }

					if($orderby == 'orden_zeleri') {
            $_orderID1 = intval( $a[$orderby] );
	        	$_orderID2 = intval( $b[$orderby] );
	        	$result = ($_orderID1 > $_orderID2) ? +1 : -1;
          }

					if($orderby == 'fecha_zeleri') {
	        	$_fecha1 = strtotime( $a[$orderby] );
	        	$_fecha2 = strtotime( $b[$orderby] );
	        	$result = ($_fecha1 > $_fecha2) ? +1 : -1;
	        }

	        if($order === 'asc') {
	            return $result;
	        }

	        return -$result;
	    }

			public function get_order_id( $str ){
				$_str = wp_strip_all_tags($str);
				$_str = trim($_str);
				$order_id = substr($_str, 8);
				return intval($order_id);
			}

	    /*public function get_bulk_actions() {
			$actions = array(
		    	'generar_multiples_ot' => 'Generar OT'
		  	);
		  	return $actions;
		}*/


		/*public function column_cb($item) {
			return sprintf(
		    	'<input type="checkbox" name="pedidos[]" value="%s" />', $item['ID']
		    );    
		}*/

	}