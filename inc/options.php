<?php
function mentor_screen_options() {
  global $wpdb,$crmcountries;
  $notices = '';
  wp_enqueue_media();
  wp_enqueue_style( 'wp-color-picker' );
  wp_enqueue_script( 'wp-color-picker' );
  wp_enqueue_style( 'mentor-crm', plugins_url('/'.MENTOR_CRM_FOLDER.'/assets/admin.css',false,'1.0.0') );
    // Actions
    if (!empty($_GET['delete-sid'])) {
        $wpdb->delete($wpdb->prefix.'mentor_steps', array( 'SID' => $_GET['delete-sid'] ) );
        $notices .='<div class="notice notice-error is-dismissible">
                        <p>Categoría #'.$_GET['delete-sid'].' Eliminada!</p>
                    </div>';
    }
    if (!empty($_GET['delete-mid'])) {
        $wpdb->delete($wpdb->prefix.'mentor_managers', array( 'MID' => $_GET['delete-mid'] ) );
        $notices .='<div class="notice notice-error is-dismissible">
                        <p>Especialista #'.$_GET['delete-mid'].' Eliminado!</p>
                    </div>';
    }
    if (!empty($_POST['mentor_nonce_options'])) {
        if( !isset( $_POST['mentor_nonce_options'] ) || !wp_verify_nonce( $_POST['mentor_nonce_options'], 'mentor_form_options' ) ) return;
        update_option( 'mentor_crm_logo', $_POST['mentor_crm_logo_url'] );
        update_option( 'mentor_crm_admin_notify', $_POST['mentor_crm_admin_notify'] );
        update_option( 'mentor_crm_terms_url', $_POST['mentor_crm_terms_url'] );
        update_option( 'mentor_crm_cliente_name', $_POST['mentor_crm_cliente_name'] );
        $mentor_crm_payment_sanbox = (isset($_POST['mentor_crm_payment_sanbox']))?true:false;
        update_option( 'mentor_crm_payment_sanbox', $mentor_crm_payment_sanbox );
        update_option( 'mentor_crm_payment_method', $_POST['mentor_crm_payment_method'] );
        // WOMPI
        update_option( 'mentor_crm_prv_key_wompi', $_POST['mentor_crm_prv_key_wompi'] );
        update_option( 'mentor_crm_pub_key_wompi', $_POST['mentor_crm_pub_key_wompi'] );
        update_option( 'mentor_crm_prv_key_wompi_test', $_POST['mentor_crm_prv_key_wompi_test'] );
        update_option( 'mentor_crm_pub_key_wompi_test', $_POST['mentor_crm_pub_key_wompi_test'] );
        // PAYU
        update_option( 'mentor_crm_merchantid_payu', $_POST['mentor_crm_merchantid_payu'] );
        update_option( 'mentor_crm_accountid_payu', $_POST['mentor_crm_accountid_payu'] );
        update_option( 'mentor_crm_apikey_payu', $_POST['mentor_crm_apikey_payu'] );
        update_option( 'mentor_crm_apilogin_payu', $_POST['mentor_crm_apilogin_payu'] );
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
                          <p>Categoría #'.$new_sid.' Creada</p>
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
                          <p>Categoría #'.$_POST['sid'].' Actualizada</p>
                      </div>';  
      }
    }
    if (!empty($_POST['mentor_nonce_managers'])) {
      if( !isset( $_POST['mentor_nonce_managers'] ) || !wp_verify_nonce( $_POST['mentor_nonce_managers'], 'mentor_form_managers' ) ) return;
      if ($_POST['mid'] == 0) {
          $wpdb->insert( 
            $wpdb->prefix.'mentor_managers', 
            array( 
              'name' => $_POST['name'], 
              'email' => $_POST['email'],
              'phone' => $_POST['phone'] 
            )
          );
          $new_sid = $wpdb->insert_id;
          $notices .='<div class="notice notice-success is-dismissible">
                          <p>Especialista #'.$new_sid.' Creado</p>
                      </div>';
      }else{
          $wpdb->update( 
            $wpdb->prefix.'mentor_managers', 
            array( 
              'name' => $_POST['name'], 
              'email' => $_POST['email'],
              'phone' => $_POST['phone']
            ), 
            array( 'MID' => $_POST['mid'] )
          );
        $notices .='<div class="notice notice-success is-dismissible">
                          <p>Especialista #'.$_POST['mid'].' Actualizado</p>
                      </div>';  
      }
    }

  $sid = (!empty($_GET['sid']))?$_GET['sid']:0;
  $mid = (!empty($_GET['mid']))?$_GET['mid']:0;
  $order_val = 0;
  $title = $color = $name = $email = $phone = '';
  $save_button = (!empty($_GET['sid']))?'Guardar':'Crear';
  $save_button1 = (!empty($_GET['mid']))?'Guardar':'Crear';
  if ($sid > 0) {
    $sid_data = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}mentor_steps WHERE SID={$sid}");
    $title = $sid_data->title;
    $color = $sid_data->color;
    $order_val = $sid_data->order_val;
  }
  if ($mid > 0) {
    $mid_data = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}mentor_managers WHERE MID={$mid}");
    $name = $mid_data->name;
    $email = $mid_data->email;
    $phone = $mid_data->phone;
  }
  $mentor_crm_payment_method = MENTOR_CRM_PAYMENT_METHOD;
  $mentor_crm_payment_sanbox = MENTOR_CRM_SANBOX;
  echo "<div class='wrap'>{$notices}</div>";
  include_once 'loader.php';
  ?>
  <div class="mentor-crm-wrap">
    <div class="mentor-steps-wraps">
      <h1>Categorias</h1>
      <div class="mentor-crm-box">
        <form method="post" class="mentor-form">
            <?php wp_nonce_field( 'mentor_form_steps','mentor_nonce_steps'); ?>
            <input type="hidden" name="sid" value="<?php echo $sid; ?>">
            <div class="crm-col-40 form-wrap">
                <label><?php _e('Título'); ?></label>
                <input type="text" name="title" value="<?php echo $title; ?>" required>
            </div>
            <div class="crm-col-20 form-wrap">
                <label><?php _e('Orden'); ?></label>
                <input type="number" name="order_val" value="<?php echo $order_val; ?>" min="0" required>
            </div>
            <div class="crm-col-20 form-group color-picker">
                <label style="margin-bottom: 1em;"><?php _e('Color'); ?></label>
                <input type="text" name="color" value="<?php echo $color; ?>" class="color-field" required>
            </div>
            <div class="crm-col form-wrap">
                <button type="submit" class="">
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
            $steps = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}mentor_steps ORDER BY order_val,SID ASC" ); 
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
                          <a href="'.admin_url('admin.php?page=mentor-crm-options').'&delete-sid='.$step->SID.'" class="button button-secondary" onclick="return confirm(\'¿Quieres eliminar Categoría #'.$step->SID.'?\')">
                              Eliminar
                          </a>
                        </td>
                      </tr>';
            }
          ?>
        </table>
      </div>
    </div>
    <div class="mentor-manager-wraps">
      <h1>Especialistas</h1>
      <div class="mentor-crm-box">
        <form method="post" class="mentor-form">
            <?php wp_nonce_field( 'mentor_form_managers','mentor_nonce_managers'); ?>
            <input type="hidden" name="mid" value="<?php echo $mid; ?>">
            <div class="crm-col-30 form-wrap">
                <label><?php _e('Nombre'); ?></label>
                <input type="text" name="name" value="<?php echo $name; ?>" required>
            </div>
            <div class="crm-col-30 form-wrap">
                <label><?php _e('Email'); ?></label>
                <input type="text" name="email" value="<?php echo $email; ?>" required>
            </div>
            <div class="crm-col-30 form-wrap">
                <label><?php _e('Teléfono'); ?></label>
                <input type="text" name="phone" value="<?php echo $phone; ?>" required>
            </div>
            <div class="crm-col form-wrap">
                <button type="submit" class="">
                    <?php echo $save_button1; ?>
                </button>
            </div>
        </form>
        <table class="mentor-table-basic">
          <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Email</th>
            <th>Teléfono</th>
            <th>Actualizado</th>
            <th style="width: 150px;"></th>
          </tr>
          <?php 
            $managers = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}mentor_managers ORDER BY MID ASC" ); 
            foreach ($managers as $key => $manager) {
                echo '<tr>
                        <td>#'.$manager->MID.'</td>
                        <td>'.$manager->name.'</td>
                        <td>'.$manager->email.'</td>
                        <td>'.$manager->phone.'</td>
                        <td>'.date('d/m/Y',strtotime($manager->updated)).'</td>
                        <td>
                          <a href="'.admin_url('admin.php?page=mentor-crm-options').'&mid='.$manager->MID.'" class="button button-primary">
                              Editar
                          </a>
                          <a href="'.admin_url('admin.php?page=mentor-crm-options').'&delete-mid='.$manager->MID.'" class="button button-secondary" onclick="return confirm(\'¿Quieres eliminar el regitro #'.$manager->MID.'?\')">
                              Eliminar
                          </a>
                        </td>
                      </tr>';
            }
          ?>
        </table>
      </div>
    </div>
  </div>
  <div class="mentor-crm-wrap">
    <div class="mentor-general-wraps">
      <h1>Opciones Generales</h1>
      <div class="mentor-crm-box">
        <form method="post" class="mentor-form">
            <?php wp_nonce_field( 'mentor_form_options','mentor_nonce_options'); ?>
            <div class="crm-row">
                <div class="crm-col-30">
                  <div class="form-wrap">
                     <label>Logo</label>
                     <img src="<?php echo get_option( 'mentor_crm_logo' ); ?>" id="mentor_crm_logo_helper">
                     <input type="hidden" name="mentor_crm_logo_url" id="mentor_crm_logo_url" value="<?php echo get_option( 'mentor_crm_logo' ); ?>">
                     <button type='button' class="button-secondary" id="mentor_crm_logo_select">Seleccionar</button>
                  </div>
                  <div class="form-wrap">
                      <label>Empresa</label>
                      <input type="text" name="mentor_crm_cliente_name" value="<?php echo get_option('mentor_crm_cliente_name'); ?>">
                  </div>
                  <div class="form-wrap">
                      <label>Notificar nuevos leads a:</label>
                      <input type="text" name="mentor_crm_admin_notify" value="<?php echo get_option('mentor_crm_admin_notify'); ?>" placeholder="info@ejemplo.com">
                      <small style="float: initial;">Nota: Para usar varios separar por coma.</small>
                  </div>
                  <div class="form-wrap">
                      <label>Enlace de Términos y Condiciones</label>
                      <input type="text" name="mentor_crm_terms_url" value="<?php echo get_option('mentor_crm_terms_url'); ?>">
                  </div>
                </div>
                <div class="crm-col-70">
                  <h1>Opciones de Pago</h1>
                  <label class="mentor_crm_payment_method_selector">
                    <input type="radio" name="mentor_crm_payment_method" value="1" <?php checked($mentor_crm_payment_method,1); ?>> Wompi
                  </label>
                  <label class="mentor_crm_payment_method_selector">
                    <input type="radio" name="mentor_crm_payment_method" value="2" <?php checked($mentor_crm_payment_method,2); ?>> Payu
                  </label>
                  <label><input type="checkbox" name="mentor_crm_payment_sanbox" <?php checked($mentor_crm_payment_sanbox,true); ?>> Activar Test Mode (Sandbox)</label>
                  <div class="mentor_crm_payment_options option1" style="display:<?php echo ($mentor_crm_payment_method == 1)?'block':'none'; ?>;">
                    <div class="crm-col form-wrap">
                      <label>Public Key</label>
                      <input type="text" name="mentor_crm_pub_key_wompi" value="<?php echo get_option('mentor_crm_pub_key_wompi'); ?>">
                    </div>
                    <div class="crm-col form-wrap">
                      <label>Private Key</label>
                      <input type="text" name="mentor_crm_prv_key_wompi" value="<?php echo get_option('mentor_crm_prv_key_wompi'); ?>">
                    </div>
                    <div class="crm-col form-wrap">
                      <label>Public Key Sandbox</label>
                      <input type="text" name="mentor_crm_pub_key_wompi_test" value="<?php echo get_option('mentor_crm_pub_key_wompi_test'); ?>">
                    </div>
                    <div class="crm-col form-wrap">
                      <label>Private Key Sandbox</label>
                      <input type="text" name="mentor_crm_prv_key_wompi_test" value="<?php echo get_option('mentor_crm_prv_key_wompi_test'); ?>">
                    </div>
                  </div>
                  <div class="mentor_crm_payment_options option2" style="display:<?php echo ($mentor_crm_payment_method == 2)?'block':'none'; ?>;">
                    <div class="crm-col form-wrap">
                      <label>ID de Comercio (merchantId)</label>
                      <input type="text" name="mentor_crm_merchantid_payu" value="<?php echo get_option('mentor_crm_merchantid_payu'); ?>">
                    </div>
                    <div class="crm-col form-wrap">
                      <label>ID de Cuenta (accountId)</label>
                      <input type="text" name="mentor_crm_accountid_payu" value="<?php echo get_option('mentor_crm_accountid_payu'); ?>">
                    </div>
                    <div class="crm-col form-wrap">
                      <label>API Key</label>
                      <input type="text" name="mentor_crm_apikey_payu" value="<?php echo get_option('mentor_crm_apikey_payu'); ?>">
                    </div>
                    <div class="crm-col form-wrap">
                      <label>API Login</label>
                      <input type="text" name="mentor_crm_apilogin_payu" value="<?php echo get_option('mentor_crm_apilogin_payu'); ?>">
                      <small>Nota: usar los mismos campos para Sandbox</small>
                    </div>
                  </div>
                </div>
            </div>
            <br>
            <br>
            <div class="crm-row text-center">
              <div class="crm-col form-wrap">
                <button type='submit' class="button-primary" id="mentor_crm_logo_select">Guardar Opciones</button>
              </div>
            </div>
        </form>
      </div>
    </div>
  </div>
  <script type="text/javascript">
    (function( $ ) {
        $(function() {
            $('.mentor_crm_payment_method_selector input').click(function(){
                $('.mentor_crm_payment_options').hide()
                $('.mentor_crm_payment_options.option'+$(this).val()).slideDown()
            })
            $('.color-field').wpColorPicker();
            var file_frame;
            $('#mentor_crm_logo_select').on('click', function( event ){
              event.preventDefault();
              if ( file_frame ) {
                file_frame.open();
                return;
              } 
              file_frame = wp.media.frames.file_frame = wp.media({
                title: 'Select a image to upload',
                button: {
                  text: 'Use this image',
                },
                multiple: false
              });
              file_frame.on( 'select', function() {
                attachment = file_frame.state().get('selection').first().toJSON();
                $( '#mentor_crm_logo_helper' ).attr( 'src', attachment.url );
                $( '#mentor_crm_logo_url' ).val( attachment.url );
              });
              file_frame.open();
            });
        });
    })( jQuery );
  </script>
  <?php
}