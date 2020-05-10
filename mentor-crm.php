<?php
/*
Plugin Name: Mentor-CRM - ClinicaDahara
Plugin URI: https://bementor.co/
Description: CRM Adaptable, provisto por https://bementor.co/
Author: Jose M
Version: 1.0-310320
Author URI: https://jdmm.xyz

={zOCvi@GF?7

*/
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
$mentor_crm_wompi_sanbox = (empty(get_option('mentor_crm_payment_sanbox')))?false:get_option('mentor_crm_payment_sanbox');
$mentor_crm_payment_method = (empty(get_option('mentor_crm_payment_method')))?1:get_option('mentor_crm_payment_method');
if ($mentor_crm_wompi_sanbox) {
  if ($mentor_crm_payment_method == 1) {
    define('PUB_KEY_WOMPI', get_option('mentor_crm_pub_key_wompi_test'));
    define('PRV_KEY_WOMPI', get_option('mentor_crm_prv_key_wompi_test'));
    define('ENDPOINT_WOMPI', 'https://sandbox.wompi.co/v1/');
  }
  if ($mentor_crm_payment_method == 2) {
    $payu_test = '<input name="test" type="hidden" value="1" >';
    define('ENDPOINT_PAYU', 'https://sandbox.checkout.payulatam.com/ppp-web-gateway-payu/"');
  }
}else{
  if ($mentor_crm_payment_method == 1) {
    define('PUB_KEY_WOMPI', get_option('mentor_crm_pub_key_wompi'));
    define('PRV_KEY_WOMPI', get_option('mentor_crm_prv_key_wompi'));
    define('ENDPOINT_WOMPI', 'https://production.wompi.co/v1/');
  }
  if ($mentor_crm_payment_method == 2) {
    $payu_test = '';
    define('ENDPOINT_PAYU', 'https://checkout.payulatam.com/ppp-web-gateway-payu/"');
  }
}
include_once 'inc/helper.php';
include_once 'inc/shortcodes.php';
include_once 'inc/routes.php';
include_once 'inc/options.php';
include_once 'inc/payments_gateway.php';
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
            `mobile_phone` varchar(30) DEFAULT NULL,
            `country` varchar(6) DEFAULT NULL,
            `city` varchar(100) DEFAULT NULL,
            `step_ID` tinyint(4) DEFAULT '0',
            `reason` text DEFAULT NULL,
            `manage` tinyint(4) DEFAULT '0',
            `process` tinyint(4) DEFAULT '0',
            `lead_comment` VARCHAR(255) NULL,
            `date` date DEFAULT NULL,
            `time` time DEFAULT NULL,
            `confirm_date` tinyint(4) NULL DEFAULT '0',
            `cost` decimal(13,2) DEFAULT NULL,
            `last_update` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            `created` datetime DEFAULT CURRENT_TIMESTAMP,
            `payment_state` tinyint(4) DEFAULT '0',
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
    dbDelta( $sql1 );
    dbDelta( $sql2 );
    dbDelta( $sql3 );
    dbDelta( $sql4 );
    dbDelta( $sql5 );
    add_role( 'crm_manager', 'CRM Manager', array( 'read' => true,'edit_posts' => false,'delete_posts' => false,'level_0' => true, 'manage_options' => true) );
    if(empty(get_option( 'mentor_crm_logo' ))){
        update_option( 'mentor_crm_logo', plugins_url('/mentor-crm/assets/logo-mentor-s.png') );
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
  global $wpdb,$crmcountries,$managers,$payment_state_text;
  wp_enqueue_style( 'mentor-crm', plugins_url('/mentor-crm/assets/admin.css',false,'1.0.0') );
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
  if (!empty($_POST['email_button'])) {
      //logic send email
  }
  $validate_changes = array(
      'step_ID'=>'Cambio de Categoria',
      'reason' => 'Cambio en Procedimiento',
      'manage' => 'Cambio de Nombre del Médico',
      'date' => 'Cambio de Fecha',
      'time' => 'Cambio de Hora',
      'confirm_date' => 'Se confirma la Cita',
      'cost' => 'Se ajusta el costo',
      'payment_state' => 'Cambio de estatus del Pago',
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
    'cost' => $_POST['cost'],
    'payment_state' => $_POST['payment_state'],
    'state' => $state

  ),array('LID'=>$_POST['lid']));
  if (!empty($_POST['payment_button'])) {
      // send email logic
  }
  $return = array('message'  => 'Información Actualizada!','action' => 'location.reload();');
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
    //'phone' => $_POST['phone'],
    'time' => $_POST['time'],
    'mobile_phone' => $_POST['mobile_phone'],
    'country' => $_POST['country'],
    'city' => $_POST['city']
  ),array('LID'=>$_POST['lid']));
  // update log logic
  $return = array('message'  => 'Información Actualizada!','action' => "jQuery('.toggle-form-wrap').toggleClass('toggle-form-true')");
  wp_send_json($return);
}
add_action( 'wp_ajax_mentor_lead_capture', 'mentor_lead_capture' );
add_action( 'wp_ajax_nopriv_mentor_lead_capture', 'mentor_lead_capture' );
function mentor_lead_capture() {
  global $wpdb;
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
    $array_data = array( 
        'fullname' => strtoupper($_POST['firstname'].' '.$_POST['lastname']), 
        'email' => strtolower($_POST['email']),
        'step_ID' => $sid,
        'phone' => $_POST['phone'],
        'country' => $_POST['country'],
        'city' => $_POST['city'],
        'date' => $_POST['date'],
        'reason' => $_POST['reason'],
        'process' => $_POST['tag-consultatipo'],
        'lead_comment' =>$_POST['comments']
    );
    $wpdb->insert($wpdb->prefix.'mentor_leads',$array_data);
    $LID = $wpdb->insert_id;
  }
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
          $wpdb->prefix.'mentor_dhara_lead_images', 
          array( 
            'LID' => $LID, 
            'image' => $image,
          )
        );
      }
    }
  }
  $return = array('msg'=>'ok');
  wp_send_json($return);
}