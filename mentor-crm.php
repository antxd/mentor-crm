<?php
/*
Plugin Name: Mentor-CRM - ClinicaDahara
Plugin URI: https://bementor.co/
Description: CRM Adaptable, provisto por https://bementor.co/
Author: Jose M
Version: 1.0-140520
Author URI: https://jdmm.xyz

={zOCvi@GF?7

*/
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
define( 'MENTOR_CRM_FOLDER',basename(dirname(__FILE__)));
define( 'MENTOR_CRM_SANBOX',get_option('mentor_crm_payment_sanbox'));
define( 'MENTOR_CRM_PAYMENT_METHOD',get_option('mentor_crm_payment_method'));
//$mentor_crm_wompi_sanbox = (empty(get_option('mentor_crm_payment_sanbox')))?false:get_option('mentor_crm_payment_sanbox');
//$mentor_crm_payment_method = (empty(get_option('mentor_crm_payment_method')))?1:get_option('mentor_crm_payment_method');
if (MENTOR_CRM_SANBOX) {
  if (MENTOR_CRM_PAYMENT_METHOD == 1) {
    define('PUB_KEY_WOMPI', get_option('mentor_crm_pub_key_wompi_test'));
    define('PRV_KEY_WOMPI', get_option('mentor_crm_prv_key_wompi_test'));
    define('ENDPOINT_WOMPI', 'https://sandbox.wompi.co/v1/');
  }
  if (MENTOR_CRM_PAYMENT_METHOD == 2) {
    define('ENDPOINT_PAYU', 'https://sandbox.checkout.payulatam.com/ppp-web-gateway-payu/"');
  }
}else{
  if (MENTOR_CRM_PAYMENT_METHOD == 1) {
    define('PUB_KEY_WOMPI', get_option('mentor_crm_pub_key_wompi'));
    define('PRV_KEY_WOMPI', get_option('mentor_crm_prv_key_wompi'));
    define('ENDPOINT_WOMPI', 'https://production.wompi.co/v1/');
  }
  if (MENTOR_CRM_PAYMENT_METHOD == 2) {
    define('ENDPOINT_PAYU', 'https://checkout.payulatam.com/ppp-web-gateway-payu/"');
  }
}
include_once 'inc/helper.php';
include_once 'inc/shortcodes.php';
include_once 'inc/routes.php';
include_once 'inc/options.php';
include_once 'inc/payments_gateway.php';
//`payment_state` tinyint(4) DEFAULT '0',
//`cost` decimal(13,2) DEFAULT NULL,
//`mobile_phone` varchar(30) DEFAULT NULL,
function install_mentor_crm(){
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();
    $table_name = $wpdb->prefix . "mentor_leads"; 
    $sql = "
          CREATE TABLE IF NOT EXISTS `{$table_name}` (
            `LID` mediumint(9) NOT NULL AUTO_INCREMENT,
            `fullname` varchar(200) DEFAULT NULL,
            `birthdate` date DEFAULT NULL,
            `email` varchar(100) DEFAULT NULL,
            `phone` varchar(30) DEFAULT NULL,
            `country` varchar(6) DEFAULT NULL,
            `city` varchar(100) DEFAULT NULL,
            `step_ID` tinyint(4) DEFAULT '0',
            `reason` text DEFAULT NULL,
            `manage` tinyint(4) DEFAULT '0',
            `process` tinyint(4) DEFAULT '0',
            `lead_comment` VARCHAR(255) NULL,
            `date` date DEFAULT NULL,
            `time` time DEFAULT NULL,
            `confirm_date` tinyint(4) NULL DEFAULT '1',
            `last_update` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            `created` datetime DEFAULT CURRENT_TIMESTAMP,
            `state` tinyint(4) DEFAULT '1',
            PRIMARY KEY  (`LID`)
          ) $charset_collate;";
    $table_name1 = $wpdb->prefix . "mentor_steps"; 
    $sql1 = "
          CREATE TABLE IF NOT EXISTS `{$table_name1}` (
            `SID` tinyint(4) NOT NULL AUTO_INCREMENT,
            `title` varchar(200) DEFAULT NULL,
            `color` varchar(20) DEFAULT NULL,
            `order_val` tinyint(4) DEFAULT '0',
            `created` datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (`SID`)
          ) $charset_collate;";
    $table_name2 = $wpdb->prefix . "mentor_logs"; 
    $sql2 = "
          CREATE TABLE IF NOT EXISTS `{$table_name2}` (
            `LOID` BIGINT(9) NOT NULL AUTO_INCREMENT,
            `lead_ID` mediumint(9) NOT NULL,
            `message` text DEFAULT NULL,
            `asesor` tinyint(4) DEFAULT '1',
            `last_update` datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (`LOID`)
          ) $charset_collate;";
    $table_name3 = $wpdb->prefix . "mentor_leads_images"; 
    $sql3 = "
          CREATE TABLE IF NOT EXISTS `{$table_name3}` (
              `LIMGID` mediumint(9) NOT NULL AUTO_INCREMENT,
              `LID` mediumint(9) NOT NULL,
              `image` longtext NOT NULL,
              `created` datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (`LIMGID`)
          ) $charset_collate;";
    $table_name4 = $wpdb->prefix . "mentor_orders"; 
    $sql4 = "
          CREATE TABLE IF NOT EXISTS `{$table_name4}` (
              `ORID` mediumint(9) NOT NULL AUTO_INCREMENT,
              `LID` mediumint(9) NOT NULL,
              `reference` varchar(255) NOT NULL,
              `amount` decimal(13,2) DEFAULT '0',
              `method_master` tinyint(4) DEFAULT '1',
              `extra_info` varchar(255) DEFAULT NULL,
              `created` datetime DEFAULT CURRENT_TIMESTAMP,
              `updated` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
              `state` tinyint(4) DEFAULT '1',
            PRIMARY KEY  (`ORID`)
          ) $charset_collate;";
    $table_name5 = $wpdb->prefix . "mentor_managers"; 
    $sql5 = "
          CREATE TABLE IF NOT EXISTS `{$table_name5}` (
              `MID` mediumint(9) NOT NULL AUTO_INCREMENT,
              `name` varchar(255) NOT NULL,
              `email` varchar(255) NOT NULL,
              `phone` varchar(255) NOT NULL,
              `updated` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (`MID`)
          ) $charset_collate;";
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );
    $wpdb->query($wpdb->prepare("ALTER TABLE `{$table_name}` AUTO_INCREMENT = 100;"));
    dbDelta( $sql1 );
    dbDelta( $sql2 );
    dbDelta( $sql3 );
    dbDelta( $sql4 );
    $wpdb->query($wpdb->prepare("ALTER TABLE `{$table_name4}` AUTO_INCREMENT = 1000;"));
    dbDelta( $sql5 );
    add_role( 'crm_manager', 'CRM Manager', array( 'read' => true,'edit_posts' => false,'delete_posts' => false,'level_0' => true, 'manage_options' => true) );
    if(empty(get_option( 'mentor_crm_logo' ))){
        update_option( 'mentor_crm_logo', plugins_url('/'.MENTOR_CRM_FOLDER.'/assets/logo-mentor-s.png') );
    }
    if(empty(get_option( 'mentor_crm_payment_sanbox' ))){
        update_option( 'mentor_crm_payment_sanbox', true );
    }
    if(empty(get_option( 'mentor_crm_payment_method' ))){
        update_option( 'mentor_crm_payment_method', 1 );
    }

    josem_plugin_rewrites();
    flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'install_mentor_crm' );

add_action( 'admin_menu', 'admin_screens_mentor_crm' );
function admin_screens_mentor_crm() {
    add_menu_page('Mentor CRM', 'CONTROL DE<br> CITAS', 'manage_options', 'mentor-crm-admin', 'mentor_screen_initial',null,1);
    add_submenu_page( 'mentor-crm-admin', 'Mentor CRM - Opciones', __('Opciones'), 'manage_options', 'mentor-crm-options', 'mentor_screen_options');
}
function mentor_screen_initial() {
  global $wpdb,$crmcountries,$payment_state_text;
  wp_enqueue_style( 'mentor-crm', plugins_url('/'.MENTOR_CRM_FOLDER.'/assets/admin.css',false,'1.0.0') );
  $cliente_name = get_option('mentor_crm_cliente_name');
  $lead_title = 'FICHA DEL PACIENTE';
  $lead_name = 'NOMBRE PACIENTE';
  $booking_date_label = 'FECHA SOLICITADA DE CONSULTA';
  $booking_time_label = 'HORA';
  $step_label = 'TIPO DE CITA';
  $cost_label = 'VALOR';
  $send_payment_button_label = 'ENVIAR BOTÓN DE PAGO';
  $send_email_button_label = 'ENVIAR EMAIL AL MEDICO';
  $reason_label = 'CIRUGÍA DE INTERÉS';
  $manage_label = 'NOMBRE DEL MÉDICO ';
  $process_label = 'PROCESO';
  $created_label = 'CREADO EL';
  $updated_label = 'ACTUALIZADO EL';
  $payment_label = 'ESTADO DEL PAGO';
  $state_label = 'CLIENTE ACTIVO';
  $cost_coin = 'COP $';
  $confirm_date_label ='POR CONFIRMAR';
  include_once 'inc/loader.php';
  if (!empty($_GET['lid'])) {
      include_once 'inc/detail.php';
  }else{
      include_once 'inc/initial.php';
  }
}

add_action( 'admin_init', 'mentor_crm_remove_menu_pages' );
function mentor_crm_remove_menu_pages() {
  if ( wp_get_current_user()->roles[0] == 'crm_manager' ){
    foreach ($GLOBALS['menu'] as $key => $value) {
      if ($value[2] != 'mentor-crm-admin') {
          remove_menu_page($value[2]);
      }
    }
  }
}

add_action( 'wp_ajax_mentor_lead_detail', 'mentor_lead_detail' );
function mentor_lead_detail() {
  global $wpdb;
  $state = (!empty($_POST['state']))?1:0;
  $confirm_date = (!empty($_POST['confirm_date']))?1:0;
  // update log logic
  $_POST['state'] = $state;
  $_POST['confirm_date'] = $confirm_date;
  $validate_changes = array(
      'step_ID'=>'Cambio de Categoria',
      'reason' => 'Cambio en Procedimiento',
      'manage' => 'Cambio de Nombre del Médico',
      'date' => 'Cambio de Fecha',
      'time' => 'Cambio de Hora',
      'confirm_date' => 'Se confirma la Cita',
      'state' =>  'Cambio de estatus del cliente'
  ); 
  validate_changes_log($validate_changes,$_POST['lid']);
  $wpdb->update($wpdb->prefix."mentor_leads",array(
    'step_ID' => $_POST['step_ID'],
    'reason' => $_POST['reason'],
    'manage' => $_POST['manage'],
    'date' => $_POST['date'],
    'time' => $_POST['time'],
    'confirm_date' => $confirm_date,
    'state' => $state

  ),array('LID'=>$_POST['lid']));
  if (!empty($_POST['payment_button'])) {
      //send last payment button to lead
      $reference_hash = $wpdb->get_var("SELECT reference FROM {$wpdb->prefix}mentor_orders WHERE LID=".$_POST['lid']);
      $lead = $wpdb->get_row("SELECT email,fullname FROM {$wpdb->prefix}mentor_leads WHERE LID=".$_POST['lid']);
      $fullname = $lead->fullname;
      $email = $lead->email;
      $payment_url = trailingslashit(home_url()).'mentor-crm-payment/'.$reference_hash;
      $body = "<b>Hola {$fullname}</b>
      <p>Fusce a hendrerit ipsum. Vestibulum at enim urna. Suspendisse potenti. Nunc scelerisque non magna eget venenatis. Donec vitae tincidunt mauris, mollis bibendum arcu. Donec eget dui in risus lobortis pellentesque id pretium nibh. Proin et massa non metus condimentum tempor vel in ante. Praesent turpis nisi, pulvinar in laoreet sed, congue eu nunc.<p>
        <a href='{$payment_url}' style='
        margin: 20px auto;
        display: block;
        height: 40px;
        line-height: 40px;
        background-color: rgb(26, 69, 148);
        font-family: -apple-system, BlinkMacSystemFont, Roboto, Helvetica, Arial, sans-serif;
        font-weight: 400;
        font-size: 14px;
        color: #fff;
        cursor: pointer;
        border:none;
        border-radius: 4px;
        padding: 0px 16px;
        text-decoration:none !important;
        width: 115px;
        text-align: center;' target='blank'>Paga con <strong>Wompi</strong></a>
        <p>Fusce a hendrerit ipsum. Vestibulum at enim urna. Suspendisse potenti. Nunc scelerisque non magna eget venenatis. Donec vitae tincidunt mauris, mollis bibendum arcu. Donec eget dui in risus lobortis pellentesque id pretium nibh. Proin et massa non metus condimentum tempor vel in ante. Praesent turpis nisi, pulvinar in laoreet sed, congue eu nunc.</p>
      ";
      mentor_email($email,get_option('mentor_crm_cliente_name').' - Pago #'.$reference_hash,$body);
      echo true;die;
  }
  if (!empty($_POST['email_button'])) {
      //logic send email to manager
      $manager = $wpdb->get_row("SELECT email,name FROM {$wpdb->prefix}mentor_managers WHERE MID=".$_POST['manage']);
      $manager_email = $manager->email;
      $manager_name = $manager->name;
      $fullname = $wpdb->get_var("SELECT fullname FROM {$wpdb->prefix}mentor_leads WHERE LID=".$_POST['lid']);
      $body = "<b>Hola {$manager_name}</b><br>
        <p>Fusce a hendrerit ipsum. Vestibulum at enim urna. Suspendisse potenti. Nunc scelerisque non magna eget venenatis. Donec vitae tincidunt mauris, mollis bibendum arcu. Donec eget dui in risus lobortis pellentesque id pretium nibh. Proin et massa non metus condimentum tempor vel in ante. Praesent turpis nisi, pulvinar in laoreet sed, congue eu nunc.<p>
      ";
      mentor_email($manager_email,get_option('mentor_crm_cliente_name').' CRM - Información Confidencial de: '.$fullname,$body,array(1),$_POST['lid']);
  }
  $return = array('message' => 'Información Actualizada!','action' => 'location.reload();');
  wp_send_json($return);
}
add_action( 'wp_ajax_mentor_lead_personadetail', 'mentor_lead_personadetail' );
function mentor_lead_personadetail() {
  global $wpdb;
  //$lead = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}mentor_leads WHERE LID = ".$_POST['lid']);
  $wpdb->update($wpdb->prefix."mentor_leads",array(
    'fullname' => $_POST['fullname'],
    'birthdate' => $_POST['birthdate'],
    'email' => $_POST['email'],
    'phone' => $_POST['phone'],
    //'time' => $_POST['time'],
    'mobile_phone' => $_POST['mobile_phone'],
    'country' => $_POST['country'],
    'city' => $_POST['city']
  ),array('LID'=>$_POST['lid']));
  // update log logic
  $return = array('message' => 'Información Actualizada!','action' => "jQuery('.toggle-form-wrap').toggleClass('toggle-form-true')");
  wp_send_json($return);
}


add_action( 'wp_ajax_mentor_order_detail', 'mentor_order_detail' );
function mentor_order_detail() {
  global $wpdb;
  if ($_POST['orid'] == 0) {
      $ORID_open = $wpdb->get_var("SELECT ORID FROM {$wpdb->prefix}mentor_orders WHERE state = 1 AND LID=".$_POST['lid']);
      if (!empty($ORID_open)) {
          wp_send_json(array('message' => 'Existe una Ordern pendiente, #'.$ORID_open.' Para crear una nueva, cancele dicha orden.','action' => ""));
      }
      $ORID = CreateOrder($_POST['lid'],$_POST['cost'],false);
      if (!empty($ORID)) {
          wp_send_json(array('message' => 'Botón creado exitosamente','action' => "location.reload();"));
      }
  }else{
      $wpdb->update( 
          "{$wpdb->prefix}mentor_orders", 
          array( 'amount' => floatval($_POST['cost']),'state'=>$_POST['state'] ), 
          array( 'ORID' => $_POST['orid']), 
          array( '%f','%d' ), 
          array( '%d' ) 
      );
      wp_send_json(array('message' => 'Botón creado exitosamente','action' => "location.reload();"));
  }
}

add_action( 'wp_ajax_mentor_get_order_detail', 'mentor_get_order_detail' );
function mentor_get_order_detail() {
  global $wpdb;
  wp_send_json($wpdb->get_row("SELECT * FROM {$wpdb->prefix}mentor_orders WHERE ORID = ".$_POST['orid']));
}

add_action( 'wp_ajax_mentor_lead_capture', 'mentor_lead_capture' );
add_action( 'wp_ajax_nopriv_mentor_lead_capture', 'mentor_lead_capture' );
function mentor_lead_capture() {
  global $wpdb,$crmcountries;
  if( !isset( $_POST['mentor-crm-front-nonce'] ) || !wp_verify_nonce( $_POST['mentor-crm-front-nonce'], 'mentor-form-lead-capture' ) ) return;
  $find_lead = $wpdb->get_row("SELECT LID FROM {$wpdb->prefix}mentor_leads WHERE email = '".$_POST['email']."'");
  if (!empty($find_lead)) {
    $LID = $find_lead->LID;
    $array_data = array( 
        'process' => $_POST['tag-consultatipo'],
        'lead_comment' =>$_POST['comments']
    );
    $wpdb->update($wpdb->prefix."mentor_leads",$array_data,array('LID'=>$LID));
  }else{
    $sid = $wpdb->get_row( "SELECT SID FROM {$wpdb->prefix}mentor_steps ORDER BY order_val ASC")->SID;
    $fullname = strtoupper($_POST['firstname'].' '.$_POST['lastname']);
    $email = strtolower($_POST['email']);
    $comments = $_POST['comments'];
    $reason = $_POST['reason'];
    $phone = $_POST['phone'];
    $country = $_POST['country'];
    $city = $_POST['city'];
    $date = $_POST['date'];
    $array_data = array( 
        'fullname' => $fullname, 
        'email' => $email,
        'step_ID' => $sid,
        'phone' => $phone,
        'country' => $country,
        'city' => $city,
        'date' => $date,
        'time' => '09:00:00',
        'reason' => $reason,
        'process' => $_POST['tag-consultatipo'],
        'lead_comment' => $comments
    );
    $wpdb->insert($wpdb->prefix.'mentor_leads',$array_data);
    $LID = $wpdb->insert_id;
  }
  $payment_url = CreateOrder($LID);
  if(isset($_FILES)){
    foreach ($_FILES['lead_image']['tmp_name'] as $key => $lead_image) {
      if( !empty( $_FILES['image']['error'][$key])) {
          $return = array('msg'=>'error');
          wp_send_json($return);
      }
      $name = $_FILES['lead_image']['name'][$key];
      $target_file = basename($name);
      // Select file type
      $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
      // Valid file extensions
      $extensions_arr = array("jpg","jpeg","png","gif");
      // Check extension
      if( in_array($imageFileType,$extensions_arr) ){
        // Convert to base64 
        $image_base64 = base64_encode(file_get_contents($lead_image) );
        $image = 'data:image/'.$imageFileType.';base64,'.$image_base64;
        // Insert record
        $wpdb->insert( 
          $wpdb->prefix.'mentor_leads_images', 
          array( 
            'LID' => $LID, 
            'image' => $image,
          )
        );
      }
    }
  }
  $register_date = date('d/m/Y h:i a');
  $date = date('d/m/Y',strtotime($date));
  // notify to admins emails
  $body = "Un nuevo lead se ha registrado a las: {$register_date}
    <table style='border:initial;'>
      <tr>
        <td>Nombre:</td><td>{$fullname}</td>
      </tr>
      <tr>
        <td>Email:</td><td>{$email}</td>
      </tr>
      <tr>
        <td>Teléfono:</td><td>{$phone}</td>
      </tr>
      <tr>
        <td>País:</td><td>{$crmcountries[$country]}</td>
      </tr>
      <tr>
        <td>Ciudad:</td><td>{$city}</td>
      </tr>
      <tr>
        <td>Cirugía de Interes:</td><td>{$reason}</td>
      </tr>
      <tr>
        <td>Fecha de Interes:</td><td>{$date}</td>
      </tr>
      <tr>
        <td>Comentario:</td><td>{$comments}</td>
      </tr>
    </table>
    <p>Fusce a hendrerit ipsum. Vestibulum at enim urna. Suspendisse potenti. Nunc scelerisque non magna eget venenatis. Donec vitae tincidunt mauris, mollis bibendum arcu. Donec eget dui in risus lobortis pellentesque id pretium nibh. Proin et massa non metus condimentum tempor vel in ante. Praesent turpis nisi, pulvinar in laoreet sed, congue eu nunc.<p>
  ";
  mentor_email(get_option('mentor_crm_admin_notify'),get_option('mentor_crm_cliente_name').' CRM - Nuevo Lead: '.$fullname.' - #'.$LID,$body);
  // notify to user email
  $body = "<b>Hola {$fullname}</b>
  <p>Fusce a hendrerit ipsum. Vestibulum at enim urna. Suspendisse potenti. Nunc scelerisque non magna eget venenatis. Donec vitae tincidunt mauris, mollis bibendum arcu. Donec eget dui in risus lobortis pellentesque id pretium nibh. Proin et massa non metus condimentum tempor vel in ante. Praesent turpis nisi, pulvinar in laoreet sed, congue eu nunc.<p>
    <a href='{$payment_url}' style='
    margin: 20px auto;
    display: block;
    height: 40px;
    line-height: 40px;
    background-color: rgb(26, 69, 148);
    font-family: -apple-system, BlinkMacSystemFont, Roboto, Helvetica, Arial, sans-serif;
    font-weight: 400;
    font-size: 14px;
    color: #fff;
    cursor: pointer;
    border:none;
    border-radius: 4px;
    padding: 0px 16px;
    text-decoration:none !important;
    width: 115px;
    text-align: center;' target='blank'>Paga con <strong>Wompi</strong></a>
    <p>Fusce a hendrerit ipsum. Vestibulum at enim urna. Suspendisse potenti. Nunc scelerisque non magna eget venenatis. Donec vitae tincidunt mauris, mollis bibendum arcu. Donec eget dui in risus lobortis pellentesque id pretium nibh. Proin et massa non metus condimentum tempor vel in ante. Praesent turpis nisi, pulvinar in laoreet sed, congue eu nunc.</p>
  ";
  mentor_email($email,get_option('mentor_crm_cliente_name').' - Solicitud #'.$LID,$body);
  $return = array('msg'=>'ok','payment_url'=>$payment_url);
  wp_send_json($return);
}
add_action('admin_head', 'my_custom_fonts');

function my_custom_fonts() {
  echo '<style>
    #toplevel_page_mentor-crm-admin *{
        color: #fff !important;
    }
    #toplevel_page_mentor-crm-admin a .wp-menu-name {
        background: linear-gradient(81deg, #de4686 0%, #ff8842 100%);
    }
  </style>';
}