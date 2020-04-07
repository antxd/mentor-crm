<?php
/*
Plugin Name: Mentor-CRM - ClinicaDahara
Plugin URI: https://bementor.co/
Description: CRM Adaptable, provisto por https://bementor.co/
Author: Jose M
Version: 1.0-310320
Author URI: https://jdmm.xyz
*/
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
include_once 'inc/helper.php';
include_once 'inc/shortcodes.php';
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
    $table_name3 = $wpdb->prefix . "mentor_dhara_lead_images"; 
    $sql3 = "
          CREATE TABLE IF NOT EXISTS `{$table_name3}` (
              `LIMGID` mediumint(9) NOT NULL AUTO_INCREMENT,
              `LID` mediumint(9) NOT NULL,
              `image` longtext NOT NULL,
              `created` datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (`LIMGID`)
          ) $charset_collate;";
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );
    dbDelta( $sql1 );
    dbDelta( $sql2 );
    dbDelta( $sql3 ); 
    add_role( 'crm_manager', 'CRM Manager', array( 'read' => true,'edit_posts' => false,'delete_posts' => false,'level_0' => true, 'manage_options' => true) );
    if(empty(get_option( 'mentor_crm_logo' ))){
        update_option( 'mentor_crm_logo', plugins_url('/mentor-crm/assets/logo-mentor-s.png') );
    }
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
function mentor_screen_options() {
  global $wpdb,$crmcountries;
  $notices = '';
  wp_enqueue_media();
  wp_enqueue_style( 'wp-color-picker' );
  wp_enqueue_script( 'wp-color-picker' );
  wp_enqueue_style( 'mentor-crm', plugins_url('/mentor-crm/assets/admin.css',false,'1.0.0') );
  if (!empty($_GET['delete-sid'])) {
      $wpdb->delete($wpdb->prefix.'mentor_steps', array( 'SID' => $_GET['delete-sid'] ) );
      $notices .='<div class="notice notice-error is-dismissible">
                      <p>Registro #'.$_GET['delete-sid'].' Eliminado!</p>
                  </div>';
  }
  if (!empty($_POST)) {
    if (!empty($_POST['mentor_nonce_options'])) {
        if( !isset( $_POST['mentor_nonce_options'] ) || !wp_verify_nonce( $_POST['mentor_nonce_options'], 'mentor_form_options' ) ) return;
        update_option( 'mentor_crm_logo', $_POST['mentor_crm_logo_url'] );
        update_option( 'mentor_crm_cliente_name', $_POST['mentor_crm_cliente_name'] );
        if (!empty($_POST['mentor_crm_security_password'])) {
            update_option( 'mentor_crm_security_password', md5($_POST['mentor_crm_security_password']) );
        }  
        $notices .='<div class="notice notice-success is-dismissible">
                        <p>Opciones Actualizadas</p>
                    </div>';
    }
    if (!empty($_POST['mentor_nonce_steps'])) {
      if( !isset( $_POST['mentor_nonce_steps'] ) || !wp_verify_nonce( $_POST['mentor_nonce_steps'], 'mentor_form_steps' ) ) return;
      if ($_POST['sid'] == 0) {
          $wpdb->insert( 
            $wpdb->prefix.'mentor_steps', 
            array( 
              'title' => strtoupper($_POST['title']), 
              'color' => $_POST['color'],
              'order_val' => $_POST['order_val'] 
            )
          );
          $new_sid = $wpdb->insert_id;
          $notices .='<div class="notice notice-success is-dismissible">
                          <p>Registro #'.$new_sid.' creado</p>
                      </div>';
      }else{
          $wpdb->update( 
            $wpdb->prefix.'mentor_steps', 
            array( 
              'title' => strtoupper($_POST['title']), 
              'color' => $_POST['color'],
              'order_val' => $_POST['order_val']
            ), 
            array( 'SID' => $_POST['sid'] )
          );
        $notices .='<div class="notice notice-success is-dismissible">
                          <p>Registro #'.$_POST['sid'].' Actualizado</p>
                      </div>';  
      }
    }
  }
  //if ( !current_user_can( 'manage_options' ) )  {
    //wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
  //}
  $sid = (!empty($_GET['sid']))?$_GET['sid']:0;
  $order_val = 0;
  $title = $color = '';
  $save_button = (!empty($_GET['sid']))?'Guardar':'Crear';
  if ($sid > 0) {
    $sid_data = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}mentor_steps WHERE SID={$sid}");
    $title = $sid_data->title;
    $color = $sid_data->color;
    $order_val = $sid_data->order_val;
  }
  echo $notices;
  include_once 'inc/loader.php';
  ?>
  <div class="wrap mentor-crm-wrap">
    <div class="mentor-steps-wraps">
      <h1>Mentor CRM Pasos</h1>
      <div class="mentor-crm-box">
        <form method="post" class="mentor-form">
            <?php wp_nonce_field( 'mentor_form_steps','mentor_nonce_steps'); ?>
            <input type="hidden" name="sid" value="<?php echo $sid; ?>">
            <div class="form-group">
                <label><?php _e('Título'); ?></label>
                <input type="text" name="title" value="<?php echo $title; ?>" required>
            </div>
            <div class="form-group">
                <label><?php _e('Orden'); ?></label>
                <input type="number" name="order_val" value="<?php echo $order_val; ?>" min="0" required>
            </div>
            <div class="form-group color-picker">
                <label><?php _e('Color'); ?></label>
                <input type="text" name="color" value="<?php echo $color; ?>" class="color-field" required>
            </div>
            <div class="form-group">
                <label>&nbsp;</label>
                <button type="submit" class="button button-primary">
                    <?php echo $save_button; ?>
                </button>
            </div>
        </form>
        <table class="mentor-table-basic">
          <tr>
            <th>ID</th>
            <th>Título</th>
            <th>Color</th>
            <th>Orden</th>
            <th style="width: 150px;"></th>
          </tr>
          <?php 
            $steps = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}mentor_steps ORDER BY SID ASC" ); 
            foreach ($steps as $key => $step) {
                echo '<tr>
                        <td>#'.$step->SID.'</td>
                        <td>'.$step->title.'</td>
                        <td>
                        <span style="background-color:'.$step->color.';width:20px;height:20px;border-radius:50%;display:inline-block;vertical-align:middle;"></span> 
                        '.$step->color.'
                        </td>
                        <td>'.$step->order_val.'</td>
                        <td>
                          <a href="'.admin_url('admin.php?page=mentor-crm-options').'&sid='.$step->SID.'" class="button button-primary">
                              Editar
                          </a>
                          <a href="'.admin_url('admin.php?page=mentor-crm-options').'&delete-sid='.$step->SID.'" class="button button-secondary" onclick="return confirm(\'¿Quieres eliminar el regitro #'.$step->SID.'?\')">
                              Eliminar
                          </a>
                        </td>
                      </tr>';
            }
          ?>
        </table>
      </div>
    </div>
    <div class="mentor-logo-wraps">
      <h1>Mentor CRM Opciones</h1>
      <div class="mentor-crm-box">
        <form method="post" class="mentor-form">
            <?php wp_nonce_field( 'mentor_form_options','mentor_nonce_options'); ?>
            <img src="<?php echo get_option( 'mentor_crm_logo' ); ?>" id="mentor_crm_logo_helper">
            <input type="hidden" name="mentor_crm_logo_url" id="mentor_crm_logo_url" value="<?php echo get_option( 'mentor_crm_logo' ); ?>">
            <button type='button' class="button-secondary" id="mentor_crm_logo_select">Seleccionar</button><br><br>
            <label>Título</label><br>
            <input type="text" name="mentor_crm_cliente_name" value="<?php echo get_option('mentor_crm_cliente_name'); ?>"><br><br>
            <label>Clave de Seguridad</label><br>
            <input type="text" name="mentor_crm_security_password" value="">
            <?php if (empty(get_option('mentor_crm_security_password'))) { echo "<small>Clave no seteada</small>"; } ?>
            <br><br>
            <button type='submit' class="button-primary" id="mentor_crm_logo_select">Guardar</button>
        </form>
      </div>
    </div>
  </div>
  <script type="text/javascript">
    (function( $ ) {
        $(function() {
            $('.color-field').wpColorPicker();
            // Uploading files
            var file_frame;
            //var wp_media_post_id = wp.media.model.settings.post.id; // Store the old id
            //var set_to_post_id = <?php echo $my_saved_attachment_post_id; ?>; // Set this
            $('#mentor_crm_logo_select').on('click', function( event ){
              event.preventDefault();
              // If the media frame already exists, reopen it.
              if ( file_frame ) {
                // Set the post ID to what we want
                //file_frame.uploader.uploader.param( 'post_id', set_to_post_id );
                // Open frame
                file_frame.open();
                return;
              } 
              //else {
                // Set the wp.media post id so the uploader grabs the ID we want when initialised
                //wp.media.model.settings.post.id = set_to_post_id;
              //}

              // Create the media frame.
              file_frame = wp.media.frames.file_frame = wp.media({
                title: 'Select a image to upload',
                button: {
                  text: 'Use this image',
                },
                multiple: false // Set to true to allow multiple files to be selected
              });

              // When an image is selected, run a callback.
              file_frame.on( 'select', function() {
                // We set multiple to false so only get one image from the uploader
                attachment = file_frame.state().get('selection').first().toJSON();
                // Do something with attachment.id and/or attachment.url here
                $( '#mentor_crm_logo_helper' ).attr( 'src', attachment.url );
                $( '#mentor_crm_logo_url' ).val( attachment.url );

                  // Restore the main post ID
                  //wp.media.model.settings.post.id = wp_media_post_id;
              });
                // Finally, open the modal
                file_frame.open();
            });

            // Restore the main ID when the add media button is pressed
            //$( 'a.add_media' ).on( 'click', function() {
            //  wp.media.model.settings.post.id = wp_media_post_id;
            //});
        });
    })( jQuery );
  </script>
  <?php
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


function josem_plugin_query_vars($vars) {
  $vars[] = 'crm-mentor-mode';
  $vars[] = 'data-lead';
  return $vars;
}

add_filter( 'template_include', 'portfolio_page_template', 99 );
function portfolio_page_template( $template ) {
  global $wpdb;
    if ( get_query_var( 'crm-mentor-mode' )  == 'view-file-secure' ) {
      $data = unserialize(base64_decode(get_query_var( 'data-lead' )));
      $datetime1 = new DateTime(date('Y-m-d',$data['created']));//start time
      $datetime2 = new DateTime(date('Y-m-d'));//end time
      $interval = $datetime1->diff($datetime2);
      if (!empty($data)) {
          if (get_option('mentor_crm_security_password') == $data['key'] && $interval->d < 10) {
            echo "<body style='padding:0;margin:0;background:#000;'>";
            $img_src = $wpdb->get_row("SELECT image FROM {$wpdb->prefix}mentor_dhara_lead_images WHERE LIMGID = ".$data['LIMGID'])->image;
            echo "<img src='{$img_src}' style='margin:auto;display:block;'>";
            echo "</body>";
          }else{
            die('Key dont match! or URL was Expired');
          }
      }
      exit;
    }
    return $template;
}
add_filter('query_vars', 'josem_plugin_query_vars');
