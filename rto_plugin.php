<?php 

/* 
Plugin Name: Rto_Plugin
Description: Plugin para RTO - al borrarlo, se borra el plugin completo. debera instalarlo nuevamente
Version: 2.0
Author:Franco Estrella
*/
if(!defined('ABSPATH')) die();

// FORMULARIO LOGIN (INPUTS Y LABELS)

function mostrar_personas(){

  $urlauth = 'https://rto.mendoza.gov.ar/api/v1/auth/login/';
  global $wpdb;
  $taller = $wpdb->prefix . 'taller';
  $personas = $wpdb->get_results("SELECT *  FROM $taller WHERE id = 1");
  $persona = array (
    'email' => $personas[0]->email,
    'password' => $personas[0]->contrasenia  
  );
  $taller = json_encode($persona);
  $body = array(
  'method' => 'POST',
  'body' => $taller,
  'headers'     => array(
        'Content-Type' => 'application/json',
  ),
	);
  $response = wp_remote_post($urlauth, $body);
  $token = json_decode(wp_remote_retrieve_body($response), true);
  return $token['access_token'];
}

// function buscar_turno(){

  
  
// }

function inputs_labels(){
  ?>
  <label class="center m-b-10" for="txtemail">Email:</label>
      <input type="email" id="email" name="email">

      <label class="center m-t-10 m-b-10" for="txtmensaje">Contraseña:</label>
      <input type="password" id="password" name="password">
      <br>
      <button class="center br-10" type="submit">Login</button>
  <?php
}
/**
 * FORMULARIO DE LOGIN
 */

function p_entry_form(){
?>
  <div  class="wrap" >
    <form class="flex" method="post" id="login_form">
      <?php inputs_labels(); ?>
    </form>
    <p id="response"></p>
  </div>
<?php
}



function entry_form_shortcode(){
  ob_start();
  p_entry_form();
  return ob_get_clean();
}
add_shortcode('entry_form', 'entry_form_shortcode');

// SCRIPTS PARA TURNOS AUTH


// FUNCION DE NUMERO DE TURNO
 function tabla_contenido(){
   ?>
       <table class="tabla" >
       <tr>
         <td>nombre </td>
         <td id="nombre"></td>
       </tr>
       <tr>
         <td>Apellido</td>
         <td id="apellido"></td>
       </tr>
       <tr>
         <td>Email</td>
         <td id="email"></td>
       </tr>
       <tr>
         <td>telefono</td>
         <td id="telefono"></td>
       </tr>
       <tr>
         <td>Departamento</td>
         <td id="departamento"></td>
       </tr>
       <tr>
         <td>Estado</td>
         <td id="estado"></td>
       </tr>
       <tr>
         <td>Fecha de creacion</td>
         <td id="fecha_creacion"></td>
       </tr>
       <tr>
         <td>Numero de Turno</td>
         <td id="nro_turno"></td>
       </tr>
       <tr>
         <td>Patente</td>
         <td id="patente"></td>
       </tr>
       <tr>
         <td>Tipo de Vehiculo</td>
         <td id="tipo_de_vehiculo"></td>
       </tr>
       <tr>
         <td>Marca</td>
         <td id="marca"></td>
       </tr>
       <tr>
         <td>Año</td>
         <td id="anio"></td>
       </tr>
       <tr>
         <td>Combustible</td>
         <td id="combustible"></td>
       </tr>
       <tr>
         <td>Inscripto en Mendoza</td>
         <td id="inscripto_en_mendoza"></td>
       </tr>
       <tr>
         <td>Taller</td>
         <td id="taller"></td>
       </tr>
     </table>
 <?php
 }

function p_nro_turno(){
	$args = array(
			    'post_type' => 'product',
			    'posts_per_page' => -1,
			    'tax_query' => array(
		    		array(
		    			'taxonomy' => 'product_type',
		    			'field'    => 'slug',
		    			'terms'    => 'booking',
		    		),
		    	),
			);
	$products = get_posts($args);
	// Query all products for display them in the select in the backoffice
	?>
	<div class="wrap">
				<h1 class="wp-heading-inline"><?php esc_html_e( 'Confirmacion en masa' , 'rto_plugin' ); ?></h1>
				<div class="mc-wcb-export-box postbox">
					<form method="post" name="csv_exporter_form" action="" enctype="multipart/form-data">
						<?php wp_nonce_field( 'export-bookings-bookings_export', '_rto-nonce' ); ?>
						<h2>1. <?php esc_html_e( 'Selecciona el producto a revisar:', 'rto_plugin' ); ?></h2>
						
						<label for="mc-wcb-product-select"><?php esc_html_e( 'Producto : ', 'rto_plugin' ); ?></label>
						<select name="mc-wcb-product-select" id="mc-wcb-product-select">
							<option value=""><?php esc_html_e( 'Selecciona un producto.', 'rto_plugin' ); ?></option>
							<?php foreach($products as $product) {?>
								<option value="<?php echo $product->ID;?>" name="event"><?php echo $product->post_title; ?></option>
							<?php }?>
						</select>
						<div class="mc-wcb-dates">
							<label for="mc-wcb-dates"><?php esc_html_e( 'Filtra reservas por fecha : ', 'rto_plugin' ); ?></label>
							<input type="checkbox" name="mc-wcb-dates" id="mc-wcb-dates">
							<div class="mc-wcb-date-picker">
								<label for="mc_wcv_start_date"><?php esc_html_e( 'Fecha de inicio', 'rto_plugin' ); ?> :</label>
								    <input type="date" id="mc_wcv_start_date" name="mc_wcv_start_date" value="<?php echo date('Y-m-d') ; ?>" />
								<label for="mc_wcv_end_date"><?php esc_html_e( 'Fecha de fin', 'rto_plugin' ); ?> :</label>
								    <input type="date" id="mc_wcv_end_date" name="mc_wcv_end_date" value="<?php echo date('Y-m-d') ; ?>" />
							</div>
						</div>
						<input type="submit" name="mc-wcb-fetch" id="mc-wcb-fetch" class="button button-secondary" value="<?php esc_html_e( 'Buscar reservas', 'rto_plugin' ); ?>" />
						<div class="mc-wcb-response">
							<img src="<?php echo MC_WCB_CSV ?>img/loader.svg" class="mc-wcb-loader"/>
							<div class="mc-wcb-result">
								<form action="">
								  <table>
									  <thead>
										<tr>
									  		<th>Aprobado?</th>
									  		<th>número de turno</th>
                        <th>Patente</th> 
									  		<th>Nombre y Apellido</th>
                        <th>Dia Realizado</th>
										</tr>  
									  </thead>
									  <tbody id="response_booking">
									  
									  </tbody>
									</table>
								  <button type="submit" id="confimaEnMasa">confirmar</button>
								</form>
              				</div>
						</form>
					</div>
				<?php
		?>
    <div class="wrap">
      <form class="flex" method="post" id="nro_turno_form">
        <label class="center f-x-large m-b-10" for="turno">Consulta de confirmacion de número de Turno:</label>
        <input type="text" id="turno" name="turno">
        <br>
        <button class="center br-10 m-t-10" type="submit">Consultar</button>
      </form>
    </div>
    <?php tabla_contenido();?>
  <?php
}
function nro_turno_form_shortcode(){
    ob_start();
    p_nro_turno();
    return ob_get_clean();
}
	/**
		* Get bookings by product id
		* @param $product_id int
		* @return $bookinds_ids array
		*/
function rto_get_bookings( $data_search ) {
			if ( $data_search ) {

				$booking_data = new WC_Booking_Data_Store();

				$args = array(
					'object_id'   => $data_search['product_id'],
					'object_type' => 'product',
					'order_by' => 'start_date',
					'status'      => array( 'confirmed', 'paid', 'complete' ),
					'limit'        => -1,
				);

				if ( isset( $data_search['date_start'] ) && !empty( $data_search['date_start'] ) ) {
					$args['date_after'] = strtotime( $data_search['date_start'] );
				}

				if ( isset( $data_search['date_end'] ) && !empty( $data_search['date_end'] ) ) {
					$args['date_before'] = strtotime(  $data_search['date_end'] );
				}

				$bookings_ids = $booking_data->get_booking_ids_by( $args );

				return $bookings_ids;
			}

			return false;
}

add_shortcode('turno_form', 'nro_turno_form_shortcode');

/**
* rto_find_booking
* Find booking when select a product
* @since 1.0.2
**/
function rto_find_booking() {
      $query_data = $_GET;

			$data = array();

			// verify nonce
			// if ( ! wp_verify_nonce( $_GET['security'], 'rto-nonce' ) ) {
			//     $error = -1;
			//     wp_send_json_error( $error );
			//     exit;
			// }

			if ( isset( $_GET['selected_product_id'] ) && !empty( $_GET['selected_product_id'] ) ) {

				$data_search = array();

				$product_id = $_GET['selected_product_id'];

				$data_search['product_id'] = $product_id;



				if ( isset( $_GET['date_start'] ) && !empty( $_GET['date_start'] ) ) {
					$data_search['date_start'] = $_GET['date_start'];
				}

				if ( isset( $_GET['date_end'] ) && !empty( $_GET['date_end'] ) ) {
					$data_search['date_end'] = $_GET['date_end'];
				}

				$bookings_ids = rto_get_bookings( $data_search );
				
				if ( $bookings_ids ) {
					$json = array();

					$data = array();

					foreach ( $bookings_ids as $booking_id ) {

						$booking = new WC_Booking( $booking_id );

						$start_date_timestamp = $booking->get_start();
						if ( $start_date_timestamp ) {
							$start_date = date( 'd-m-Y H:i', $start_date_timestamp );
						} else {
							$start_date = 'N/A';
						}

						$end_date_timestamp = $booking->get_end();
						if ( $end_date_timestamp ) {
							$end_date = date( 'd-m-Y H:i', $end_date_timestamp );
						} else {
							$end_date = 'N/A';
						}
						

						$order = $booking->get_order();
						if ( $order ) {
							$apellido_nombre = (''!== $order->get_billing_company() ? $order->get_billing_company() : 'N/A');
							$nro_turno = ( '' !== $order->get_billing_first_name() ? $order->get_billing_first_name() : 'N/A' );
							$patente = ( $order->get_billing_last_name() ? $order->get_billing_last_name() : 'N/A' );
							$customer_mail = ( $order->get_billing_email() ? $order->get_billing_email() : 'N/A' );
							$customer_phone = ( $order->get_billing_phone() ? $order->get_billing_phone() : 'N/A' );
						} else {
							$nro_turno = $patente = $customer_mail = $customer_phone = 'N/A';
						}
            if ( $start_date && $end_date ) { // check if there are a start date and end date
							$data[] = array(
								"id"=>$booking_id, 
								"turno"=>$nro_turno, 
								"inicio"=>$start_date, 
								"fin"=>$end_date, 
								"patente"=>$patente,
								"apellidoNombre"=>$apellido_nombre,
								"email"=>$customer_mail, 
								"telefono"=>$customer_phone);
							// here we construct the array to pass informations to export CSV
						}
					}
					wp_send_json_success( $data );
				} else {
					$data['message'] =  __( 'No booking(s) found for this product.', 'rto_plugin' );
					wp_send_json_error( $data );
				}
			} else {
				$error['code'] = 1;
				$error['message'] =  __( 'Please select product.', 'rto_plugin' );
				wp_send_json_error( $error );
				exit;
			}

			wp_die();
}
	add_action( 'wp_ajax_rto_find_booking', 'rto_find_booking' );

// FORM PARA CLIENTES

function p_client_turno(){
  
    ?>
    <div class="wrap">
      <form class="flex"  method="post" id="client_nro_turno_form" action="<?= get_the_permalink();?>">
        <label class="center f-x-large m-b-10"  for="turno">Número de Turno:</label>
        <input class="" type="text" id="turno" name="turno">
        <button class="center br-10 m-t-10" type="submit">Consultar</button>
		  <p class="center br-10 m-t-10">
			  ¿No tenes turno? 
		  </p>
		<button class="center br-10 m-t-10" id="client-button-get-turn"  type="submit" ><a class="c-white" href="https://rto.mendoza.gov.ar/" target="_blank">Obtener Turno</a> </button>
      </form>
      <div >
        <p class="" id="error"></p>
      </div>
    </div>
    <br>
    <br>
    
  <?php
  $urlTurno = 'https://rto.mendoza.gov.ar/api/v1/auth/turno/';
  if (!empty($_POST) && $_POST['turno'] != '') {
    $token = mostrar_personas();
    $turno = array(
        'turno' => $_POST['turno'],
    );
    $taller = json_encode($turno);
    $body = array(
        'method' => 'POST',
        'body' => $taller,
        'headers' => array(
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $token,
        ),
    );
    $response = wp_remote_post($urlTurno, $body);
    $res = json_decode(wp_remote_retrieve_body($response), true);
    $clientes = $res['turno'];
    if($clientes){
      $tipo_de_vehiculo = "{$clientes['tipo_de_vehiculo']}";
      $nro_turno_booking = "{$clientes['nro_turno']}";
      $nombre_booking = "{$clientes['nombre']}";
      $apellido_booking = "{$clientes['apellido']}";
      $email_booking = "{$clientes['email']}";
      $patente_booking = "{$clientes['patente']}";
      $telefono_booking = "{$clientes['telefono']}";
      ?>
      <table class="tabla" >
      <tr>
        <td>Nombre </td>
        <td id="nombre"><?=$clientes['nombre']?></td>
      </tr>
      <tr>
        <td>Apellido</td>
        <td id="apellido"><?=$clientes['apellido']?></td>
      </tr>
      <tr>
        <td>Email</td>
        <td id="email"><?=$clientes['email']?></td>
      </tr>
      <tr>
        <td>telefono</td>
        <td id="telefono"><?=$clientes['telefono']?></td>
      </tr>
      <tr>
        <td>Departamento</td>
        <td id="departamento"><?=$clientes['departamento']?></td>
      </tr>
      <tr>
        <td>Estado</td>
        <td id="estado"><?=$clientes['estado']?></td>
      </tr>
      <tr>
        <td>Fecha de creacion</td>
        <td id="fecha_creacion"><?=$clientes['fecha_creacion']?></td>
      </tr>
      <tr>
        <td>Numero de Turno</td>
        <td id="nro_turno"><?=$clientes['nro_turno']?></td>
      </tr>
      <tr>
        <td>Patente</td>
        <td id="patente"><?=$clientes['patente']?></td>
      </tr>
      <tr>
        <td>Tipo de Vehiculo</td>
        <td id="tipo_de_vehiculo"><?=$clientes['tipo_de_vehiculo']?></td>
      </tr>
      <tr>
        <td>Marca</td>
        <td id="marca"><?=$clientes['marca']?></td>
      </tr>
      <tr>
        <td>Año</td>
        <td id="anio"><?=$clientes['anio']?></td>
      </tr>
      <tr>
        <td>Combustible</td>
        <td id="combustible"><?=$clientes['combustible']?></td>
      </tr>
      <tr>
        <td>Inscripto en Mendoza</td>
        <td id="inscripto_en_mendoza"><?=$clientes['inscripto_en_mendoza']?></td>
      </tr>
      <tr>
        <td>Taller</td>
        <td id="taller"><?=$clientes['taller']?></td>
      </tr>
      </table>
      <form action="" class="flex" id="reservar_turno_form" method="POST">
      <input type="hidden" id="auto_o_moto" value="<?=$tipo_de_vehiculo?>" name="reserva<?=$tipo_de_vehiculo?>">
      <button class="center br-10 m-t-10" type="submit">Reservar Turno</button>
    </form>
      <?php
    }else{
      ?>
      <div>
        <p>Numero de turno no encontrado ò equivocado</p>
      </div>
      <?php
    }
  };

}

function client_form_shortcode(){
    ob_start();
    p_client_turno();
    return ob_get_clean();
}
add_shortcode('client_form', 'client_form_shortcode');

add_filter( 'woocommerce_checkout_fields' , 'hjs_wc_checkout_fields' );
add_filter('woocommerce_checkout_get_value','__return_empty_string', 1, 1);
 
// This example changes the default placeholder text for the state drop downs to "Select A State"
function hjs_wc_checkout_fields( $fields ) {
	$fields['billing']['billing_first_name']['label'] = 'Nùmero de Turno';
    $fields['shipping']['shipping_first_name']['label'] = 'Nùmero de Turno';
    $fields['billing']['billing_last_name']['label'] = 'Patente';
    $fields['shipping']['shipping_last_name']['label'] = 'Patente';
    $fields['billing']['billing_company']['label'] = 'Apellido y Nombre';
    $fields['billing']['billing_company']['required'] = 'true';
    $fields['shipping']['shipping_company']['label'] = 'Apellido y Nombre';
    return $fields;
}
add_filter( 'woocommerce_default_address_fields' , 'wpse_120741_wc_def_state_label' );
function wpse_120741_wc_def_state_label( $address_fields ) {
     $address_fields['postcode']['label'] = 'DNI';
     return $address_fields;
}
add_filter( 'allowed_http_origins', 'add_allowed_origins' );
function add_allowed_origins( $origins ) {
    $origins[] = 'https://bubatec.com';
    return $origins;
}

// Actualiza automáticamente el estado de los pedidos a COMPLETADO
add_action( 'woocommerce_order_status_processing', 'actualiza_estado_pedidos_a_completado' );
add_action( 'woocommerce_order_status_on-hold', 'actualiza_estado_pedidos_a_completado' );
function actualiza_estado_pedidos_a_completado( $order_id ) {
    global $woocommerce;
    
    //ID's de las pasarelas de pago a las que afecta
    $paymentMethods = array( 'bacs', 'cheque', 'cod', 'paypal' );
    
    if ( !$order_id ) return;
    $order = new WC_Order( $order_id );

    if ( !in_array( $order->payment_method, $paymentMethods ) ) return;
    $order->update_status( 'completed' );
}

  function Activar(){
    global $wpdb; // Este objeto global nos permite trabajar con la BD de WP
    // Crea la tabla si no existe
    $taller = $wpdb->prefix . 'taller';
    $charset_collate = $wpdb->get_charset_collate();
    $query = "CREATE TABLE IF NOT EXISTS $taller (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            email varchar(40) NOT NULL,
            contrasenia varchar(100) NOT NULL,
            UNIQUE (id)
            ) $charset_collate;";
    // La función dbDelta que nos permite crear tablas de manera segura se
    // define en el fichero upgrade.php que se incluye a continuación
    include_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($query);
  }
  
  function Desactivar(){}
  register_activation_hook(__FILE__, 'Activar');
  register_deactivation_hook(__FILE__, 'Desactivar');
  

 //MENU DEL ADMIN


  function CrearMenu(){
    add_menu_page(
      'RTO - Revision Tecnica Obligatoria', //Titulo de la pagina
      'RTO', //Titulo del Menu
      'manage_options', //Capability
      'sp_menu', //slug
      'MostrarContenido', //NOMBRE DE LA FUNCION 
      '', //icono por defecto
      '1', //posicion
    );
  }
  add_action('admin_menu', 'CrearMenu');
  
  

/**
 *  REGISTRO DE SCRIPTS, TANTO CSS COMO JS - JQUERY  
 * */ 
function my_enqueue() {

    wp_register_script('test', plugin_dir_url(__FILE__) . 'js/test.js',array('jquery'), '1.0', false);
  wp_enqueue_script('test');
	  wp_localize_script( 'test', 'mc_wcb_params', array( 'ajax_url' => admin_url( 'admin-ajax.php' ), 'security' => wp_create_nonce( 'rto-nonce' )) );
}
add_action( 'wp_enqueue_scripts', 'my_enqueue' );
// CSS
function custom_enqueue_styles() {
	wp_enqueue_style( 'custom-style', 
					  plugin_dir_url(__FILE__) . '/css/styles.css',
					);
}
add_action( 'wp_enqueue_scripts', 'custom_enqueue_styles');
function MostrarContenido(){
  global $wpdb; // Este objeto global nos permite trabajar con la BD de WP
  // Si viene del formulario  grabamos en la base de datos
  if (!empty($_POST)
      && $_POST['password'] != ''
      && is_email($_POST['email'])
  ) {
      $taller = $wpdb->prefix . 'taller';
      $password = sanitize_text_field($_POST['password']);
      $email = $_POST['email'];

      $wpdb->insert(
          $taller,
          array(
            'email' => $email,
            'contrasenia' => $password,

          )
      );
      echo "<p class='exito'><b>Tus datos han sido registrados</b>. Gracias
              por tu interés. En breve contactaré contigo.<p>";
  }

  ?>
    <h1>
      Bienvenido al Plugin de la RTO
    </h1>
    <p>
      El plugin consta que hay que agregar 3 shorcodes en diferentes paginas, una necesariamente debe llamarse turnos.
    </p>
    <div  class="wrap" >
    <form method="post" id="login_form_admin">
      <?php inputs_labels(); ?>
    </form>
    <p id="response">este formulario guardara el email y la contraseña en la base de datos, para que luego sea consultado por el cliente 
    cunado quiera saber su turno.</p>
    </div>
    <div class="container">
      <div class="center">
        <h2>Instructivo de shortcodes</h2>
        <p>En este instructivo, veremos como configurar los shortcodes en las paginas.</p>
        <p>hay una pagina en especial que debe respetarse con respecto a la direccion y es la de <strong>/turnos</strong></p>
        <p>ya que al momento de logearse el empleado dentro de la API de la RTO, lo redirige automaticamente a esa pagina.</p>
        <p>el shortcode para el formulario de turnos de empleados, que ademas tiene un boton para confirmar el turno. seria asi [turno_form]</p>
        <p>el shortocode para el ingreso de los empleados, no es obligatorio en ninguna pagina especifica, sino que puede ser puesto donde mas convenga, seria [entry_form]</p>
        <p>y el shortcode del lado del cliente que puede ser ubicado de igual manera que el de ingreso de empleados seria: [client_form]</p>
        <p></p>

      </div>
    </div>
<?php
}