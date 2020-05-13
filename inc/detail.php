<?php
$lead = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}mentor_leads WHERE LID = ".$_GET['lid']);
$step = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}mentor_steps WHERE SID = ".$lead->step_ID);
$steps = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}mentor_steps ORDER BY order_val,SID ASC");
$managers = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}mentor_managers ORDER BY MID ASC" ); 
$sid = $step->SID;
$tag_color = $step->color;
?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/autonumeric/4.1.0/autoNumeric.min.js"></script>
<style type="text/css">
h1.lead-title {
    color: <?php echo $tag_color; ?> !important;
}
.mentor-crm-wrap .form-wrap input:focus,
.mentor-crm-wrap .form-wrap select:focus,
.mentor-crm-wrap .form-wrap textarea:focus {
    border-color: <?php echo $tag_color; ?> !important;
    color: <?php echo $tag_color; ?> !important;
    box-shadow: 0 0 0 1px <?php echo $tag_color; ?> !important;
}
.mentor-crm-wrap .form-wrap button {
    border: 1px solid <?php echo $tag_color; ?> !important;
    background: <?php echo $tag_color; ?> !important;
}
.mentor-crm-wrap .form-wrap button:hover,
.mentor-crm-wrap .form-wrap button:focus {
    color: <?php echo $tag_color; ?> !important;
    box-shadow: 0 0 0 1px <?php echo $tag_color; ?> !important;
}
.mentor-crm-wrap .slider:before {
    color: <?php echo $tag_color; ?>!important ;
    border: 2px solid <?php echo $tag_color; ?> !important;
}
.mentor-crm-wrap .switch input:checked + .slider:before {
  background-color: <?php echo $tag_color; ?> !important;
  color: #fff !important;
}
.toggle-form {
    background: <?php echo $tag_color; ?> !important;
}
.payment_state_label {
    background: <?php echo $tag_color; ?> !important;;
}
</style>
<div class="wrap mentor-crm-wrap">
      <?php include 'crm-header.php'; ?>
      <div class="mentor-crm-box-lead mentor-crm-box crm-row">
          <h1 class="lead-title"><?php echo ucwords($lead_title); ?></h1>
          <div class="crm-col-30">
            <div class="mentor-crm-detail">
              <h2>INFORMACIÓN PERSONAL <span class="dashicons dashicons-edit toggle-form"></span></h2>
              <div class="mentor-crm-detail-content">
                <form class="mentor-crm-ajax toggle-form-wrap toggle-form-true">
                  <input type="hidden" name="action" value="mentor_lead_personadetail">
                  <input type="hidden" name="lid" value="<?php echo $_GET['lid']; ?>">
                  <p>NOMBRE:<input type="text" name="fullname" value="<?php echo $lead->fullname; ?>"></p>
                  <p>E-MAIL:<input type="text" name="email" value="<?php echo $lead->email; ?>"></p>
                  <!-- <p>TELÉFONO:<input type="text" name="phone" value="<?php echo $lead->phone; ?>"></p> -->
                  <p>TELÉFONO MOVIL:<input type="text" name="mobile_phone" value="<?php echo $lead->mobile_phone; ?>"></p>
                  <p>FECHA DE NACIMIENTO:<input type="text" name="birthdate" value="<?php echo $lead->birthdate; ?>"></p>
                  <p>PAÍS:
                      <select name="country">
                        <?php
                        foreach ($crmcountries as $key => $country) {
                          echo "<option value='{$key}' ".selected($key,$lead->country,false).">{$country}</option>";
                        }
                        ?>
                      </select>
                  </p>
                  <p>CIUDAD:<input type="text" name="city" value="<?php echo $lead->city; ?>"></p>
                  <p>MENSAJE: <?php echo $lead->lead_comment; ?></p>
                  <p class="form-wrap text-center">
                      <button type="submit" class="mentor-crm-submit">ACTUALIZAR</button>
                  </p>
                </form>
              </div>
            </div>
          </div>
          <div class="crm-col-70">
            <div class="mentor-crm-detail">
              <h2>CONTROL DE CITAS</h2>
              <form class="mentor-crm-ajax">
                <input type="hidden" name="action" value="mentor_lead_detail">
                <input type="hidden" name="lid" value="<?php echo $_GET['lid']; ?>">
                  <div class="mentor-crm-detail-content crm-row">
                    <div class="crm-col-50 form-wrap">
                      <label><?php echo $booking_date_label; ?></label>
                      <input type="date" name="date" value="<?php echo $lead->date; ?>">
                    </div>
                    <div class="crm-col-50 form-wrap">
                      <label><?php echo $booking_time_label; ?></label>
                      <select name="time">
                          <?php
                            $start = "09:00";
                            $end = "18:30";
                            $tStart = strtotime($start);
                            $tEnd = strtotime($end);
                            $tNow = $tStart;
                            while($tNow <= $tEnd){
                                  echo '<option value="'.date("H:i:s",$tNow).'" '.selected(date("H:i:s",$tNow),$lead->time).'>'.date("H:i A",$tNow).'</option>';
                                  $tNow = strtotime('+30 minutes',$tNow);
                            }
                          ?>
                      </select>
                    </div>
                    <div class="crm-col-50 form-wrap">
                      <label><?php echo $step_label; ?></label>
                      <select name="step_ID">
                        <?php
                          foreach ($steps as $key => $step) {

                              echo "<option value='{$step->SID}' ".selected($step->SID,$lead->step_ID).">".($key+1)." - {$step->title}</option>";
                          }
                        ?>
                      </select>
                    </div>
                     <div class="crm-col-50 form-wrap">
                      <label><?php echo $manage_label; ?></label>
                      <select name="manage">
                        <?php foreach ($managers as $key => $manage) { ?>
                              <option value="<?php echo $manage->MID; ?>" <?php selected($manage->MID,$lead->manage); ?>><?php echo $manage->name; ?></option>
                        <?php } ?>
                      </select>
                    </div>
                    <div class="crm-col form-wrap">
                      <label><?php echo $reason_label; ?></label>
                      <input type="text" name="reason" value="<?php echo $lead->reason; ?>">
                    </div>
                    <div class="crm-col-50 form-wrap">
                      <label><?php echo $state_label; ?></label>
                      <label class="switch">
                        <input type="checkbox" name="state" value="true" <?php checked(1,$lead->state); ?>>
                        <span class="slider round"></span>
                      </label>
                    </div>
                     <div class="crm-col-50 form-wrap">
                      <label><?php echo $confirm_date_label; ?></label>
                      <label class="switch">
                        <input type="checkbox" name="confirm_date" value="true" <?php checked(1,$lead->confirm_date); ?>>
                        <span class="slider round"></span>
                      </label>
                    </div>
                    <div class="crm-col-50 form-wrap">
                      <label><?php echo $send_payment_button_label; ?></label>
                      <label class="switch">
                        <input type="checkbox" name="payment_button" value="true">
                        <span class="slider round"></span>
                      </label>
                    </div>
                    <div class="crm-col-50 form-wrap">
                      <label><?php echo $send_email_button_label; ?></label>
                      <label class="switch">
                        <input type="checkbox" name="email_button" value="true">
                        <span class="slider round"></span>
                      </label>
                    </div>
                    <div class="crm-col form-wrap text-right">
                        <button type="submit" class="mentor-crm-submit">ACTUALIZAR</button>
                    </div>
                </div>
            </form>
          </div>
      </div>
      <div class="crm-col">
        <div class="mentor-crm-detail">
          <h2 class="mentor-crm-detail-orders-title">
          <div class="crm-row">
            <div class="crm-col-50 orders-title-wrap">
                Botones de Pago Generados
            </div>
            <div class="crm-col-50 form-wrap text-right">
                <button type="button" class="gen-btn-payment">GENERAR BOTÓN DE PAGO</button>
            </div>
          </div>
          </h2>
          <div class="mentor-payment-editor">
            <form class="mentor-crm-ajax">
              <input type="hidden" name="action" value="mentor_order_detail">
              <input type="hidden" name="orid" value="0" id="orid">
              <input type="hidden" name="lid" value="<?php echo $_GET['lid']; ?>">
              <div class="crm-row">
                  <div class="crm-col-20 form-wrap cost-wrap">
                     <label>Referencia</label>
                     <input type="text" id="reference-payment" value="" readonly="true">
                   </div>
                  <div class="crm-col-30 form-wrap cost-wrap">
                    <label><?php echo $cost_label; ?></label>
                     <input type="hidden" name="cost" value="0" id="order-cost-raw">
                    <input type="text" name="" placeholder='0' value="0" id="order-cost">
                    <span><?php echo $cost_coin; ?></span>
                  </div>
                  <div class="crm-col-30 form-wrap">
                    <label><?php echo $payment_label; ?></label>
                    <select name="state" id="state-payment">
                        <option value="1"><?php echo $payment_state_text[1]; ?></option>
                        <option value="2"><?php echo $payment_state_text[2]; ?></option>
                        <option value="3"><?php echo $payment_state_text[3]; ?></option>
                    </select>
                  </div>
                  <div class="crm-col-20 form-wrap">
                      <button type="submit" class="mentor-crm-submit-order"><span class="payment-save-text">CREAR</span></button>
                  </div>
              </div>
            </form>
          </div>
          <div class="mentor-crm-detail-content">
            <table class="mentor-table-basic text-center">
                <tr>
                    <th>ID</th>
                    <th>Referencia</th>
                    <th>Valor</th>
                    <th>Fecha</th>
                    <th>Estado</th>
                    <th></th>
                </tr>
                <?php
                  $lead_orders = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}mentor_orders WHERE LID=".$_GET['lid']." ORDER BY ORID DESC" );
                  foreach ($lead_orders as $key => $lead_order) {
                      echo "<tr>
                                <td>{$lead_order->ORID}</td>
                                <td>{$lead_order->reference}</td>
                                <td>COP $ ".number_format($lead_order->amount,0,'.','.')."</td>
                                <td>".date('d/m/Y H:i A',strtotime($lead_order->created))."</td>
                                <td>".print_state_order($lead_order->state)."</td>
                                <td>
                                  <div class='form-wrap text-right'>
                                      <button type='button' class='mentor-crm-order-edit' data-orid='{$lead_order->ORID}'>EDITAR</button>
                                  </div>
                                </td>
                            </tr>";
                  }
                ?>
            </table>
          </div>
        </div>
      </div>
      <div class="crm-col">
        <div class="mentor-crm-detail">
          <h2>Historial de cambios</h2>
          <div class="mentor-crm-detail-content lead-log-detail">
            <table class="mentor-table-basic text-center">
                <tr>
                    <th>Fecha</th>
                    <th>Cambio</th>
                    <th>Asesor</th>
                </tr>
                <?php
                  $lead_logs = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}mentor_logs WHERE lead_ID=".$_GET['lid']." ORDER BY LOID DESC" );
                  foreach ($lead_logs as $key => $log) {
                    $user_info = get_userdata($log->asesor);
                        $asesor = $user_info->user_login.' - '.$user_info->first_name.' '.$user_info->last_name;
                        echo "<tr><td>".date('d/m/Y H:i A',strtotime($log->last_update))."</td><td>{$log->message}</td><td>{$asesor}</td></tr>";
                  }
                ?>
            </table>
          </div>
        </div>
      </div>
</div>
<div class="mentor-toasty"></div>
<script type="text/javascript">
jQuery(document).ready(function($){
    $('.mentor-crm-ajax').submit(function(){
      var $this = $(this)
      $this.addClass('mentor-sending')
      $.post(ajaxurl,$(this).serialize(),function(data,status){
        console.log(data)
          $('.mentor-toasty').html(data.message).addClass('mentor-toasty-open')
          $this.removeClass('mentor-sending')
          setTimeout("jQuery('.mentor-toasty').removeClass('mentor-toasty-open').html('');"+data.action,2000)
      })
      return false;
    })
    $('.toggle-form').click(function(e){
        $('.toggle-form-wrap').toggleClass('toggle-form-true')
    })
    $('.mentor-crm-order-edit').click(function(){
        $('.mentor-payment-editor').slideUp()
        $.post(ajaxurl,{action:'mentor_get_order_detail',orid:$(this).data('orid')},function(data,status){
          $('#orid').val(data.ORID)
          $('#reference-payment').val(data.reference)
          $('#state-payment').val(data.state)
          order_cost.set(parseInt(data.amount))
          $('.mentor-payment-editor').slideDown()
          $('html,body').animate({
                  scrollTop: $('.mentor-payment-editor').offset().top
              }, 'slow');
        })
        return false;
    })
    $('.gen-btn-payment').click(function(e){
        $('.mentor-payment-editor').slideToggle()
    })
    $('#order-cost').change(function(){
      $('#order-cost-raw').val(order_cost.getNumber())
    })
    var order_cost = new AutoNumeric('#order-cost',{
          allowDecimalPadding: false,
          decimalCharacter: ",",
          digitGroupSeparator: "."
    });
    
})
</script>