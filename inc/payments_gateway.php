<?php
function validate_wompi_transaction(){
  global $wpdb;
  $json = file_get_contents('php://input');
  $json = json_decode($json);
  if (!empty($json->event) && $json->event == 'transaction.updated') {
    $transaction_reference = $json->data->transaction->reference;
    $transaction_id = $json->data->transaction->id;
    $wompi_request = wp_remote_get( ENDPOINT_WOMPI.'transactions/'.$transaction_id );
    $result = json_decode( wp_remote_retrieve_body($wompi_request) );
    $extra_info = $wpdb->get_var("SELECT extra_info FROM {$wpdb->prefix}mentor_orders WHERE reference = '{$transaction_reference}'");
    if ( is_object( $result ) && ! is_wp_error( $result ) ) {
      $wompi_estado = array(
            'APPROVED'=>2,
            'DECLINED'=>3,
            'VOIDED'=>3,
            'ERROR'=>3
      );
      $ORID = $wpdb->get_var("SELECT ORID FROM {$wpdb->prefix}mentor_orders WHERE reference = '{$transaction_reference}'");
      $update = $wpdb->update($wpdb->prefix."mentor_orders",
        array('state'=>$wompi_estado[$result->data->status],'external_id'=>$transaction_id,'extra_info'=>serialize($result)),
        array('ORID'=>$ORID)
      );
    }
  }
  status_header(200);
}
function validate_payu_transaction(){
  global $wpdb;
  $ApiKey = get_option('mentor_crm_apikey_payu');
  $merchant_id = $_REQUEST['merchant_id'];
  $transaction_reference = $_REQUEST['reference_sale'];
  $TX_VALUE = $_REQUEST['value'];
  $New_value = number_format($TX_VALUE, 1, '.', '');
  $currency = $_REQUEST['currency'];
  $transactionState = $_REQUEST['state_pol'];
  $firma_cadena =  $ApiKey."~".$merchant_id."~".$transaction_reference."~".$New_value."~".$currency."~".$transactionState;
  $firmacreada = md5($firma_cadena);
  $firma = $_REQUEST['sign'];
  $reference_pol = $_REQUEST['reference_pol'];
  $transaction_id = $_REQUEST['transaction_id'];
  $tid = $_REQUEST['extra1'];
  $order_status = 'PENDING';
  switch ($_REQUEST['state_pol']) {
    case '4':
      $order_status = 'APPROVED';
      $status = 200;
      break;
    case '6':
      $order_status = 'DECLINED';
      $status = 403;
      break;
    case '5':
      $order_status = 'VOIDED';
      $status = 403;
      break;
    default:
      $order_status = 'PENDING';
      $status = 403;
      break;
  }
  $payu_estado = array(
        'APPROVED'=>2,
        'DECLINED'=>3,
        'VOIDED'=>3,
        'ERROR'=>3,
        'PENDING'=>1
  );
  $payment_info = serialize($_REQUEST);
  $ORID = $wpdb->get_var("SELECT ORID FROM {$wpdb->prefix}mentor_orders WHERE reference = '{$transaction_reference}'");
  $update = $wpdb->update($wpdb->prefix."mentor_orders",
    array('state'=>$payu_estado[$order_status],'external_id'=>$transaction_id,'extra_info'=>serialize($payment_info)),
    array('ORID'=>$ORID)
  );
  status_header($status);
}

function make_payment_thanks(){
  $current_theme = get_stylesheet_directory();
  $mentor_crm_payment_method = (empty(get_option('mentor_crm_payment_method')))?1:get_option('mentor_crm_payment_method');
  $estadoTransaction = $referenceCode = $transactionId = '---';
  $transactionValue = 0;
  if (!empty($_GET['id'])) {
      $wompi_request = wp_remote_get( ENDPOINT_WOMPI.'transactions/'.$_GET['id'] );
      $result = json_decode( wp_remote_retrieve_body($wompi_request) );
      if ( is_object( $result ) && ! is_wp_error( $result ) ) {
          $wompi_estado_text = array(
            'APPROVED'=>'Transacción aprobada ✅',
            'DECLINED'=>'Transacción rechazada ⛔️',
            'VOIDED'=>'Transacción anulada 🕓',
            'ERROR'=>'Error interno ❌'
          );
          $result->data->status = 'ERROR';
          $estadoTransaction = $wompi_estado_text[$result->data->status];
          $transactionId = $result->data->id;
          $referenceCode = $result->data->reference;
          $transactionValue = floatval($result->data->amount_in_cents/1000);
      }
  }
  if (!empty($_REQUEST['transactionState'])) {
    $payu_estado_text = array(4=>'Transacción aprobada ✅',6=>'Transacción rechazada ⛔️',7=>'Transacción Pendiente 🕓',104=>'Error interno ❌');
    $estadoTransaction = $payu_estado_text[$_REQUEST['transactionState']];
  }
  if (!empty($_REQUEST['transactionId'])) {
    $transactionId = $_REQUEST['transactionId'];
  }
  if (!empty($_REQUEST['referenceCode'])) {
    $referenceCode = $_REQUEST['referenceCode'];
  }
  if (!empty($_REQUEST['TX_VALUE'])) {
    $transactionValue = $_REQUEST['TX_VALUE'];
  }
  $sub_header_color = array(1=>'1a4594',2=>'A6C307');
  include $current_theme.'/header.php'; ?>
  <style type="text/css">
  #thanks-page-mentor-crm * {
    box-sizing: border-box;
  }
  .mentor-crm-table {
    border-collapse: collapse;
    width: 100%;
    margin:1.5rem 0 3rem;
  }
  .mentor-crm-table td,.mentor-crm-table  th {
    border: 1px solid #dddddd;
    text-align: left;
    padding: 8px;
    width: 50%;
  }
  .mentor-crm-table tr:nth-child(even) {
    background-color: #dddddd;
  }
  .mentor-crm-container {
    max-width: 1100px;
    width: 100%;
    margin:auto;
    padding: 0 15px;
    min-height: calc(100vh - 290px);
  }
  .mentor-crm-sub-header{
    padding: 50px 0;
    background: #<?php echo $sub_header_color[$mentor_crm_payment_method]; ?>;
    text-align: center;
    margin-bottom: 1.5rem;
  }
  .mentor-crm-sub-header h1{
    color: #fff;
  }
  .mentor-crm-sub-header h1 img{
    display: block;
    margin: 1rem auto;
    max-width: 250px;
  }
  .mentor-crm-btn{
    cursor: pointer;
    -webkit-appearance: none;
    border: 1px solid #<?php echo $sub_header_color[$mentor_crm_payment_method]; ?>;
    background: #<?php echo $sub_header_color[$mentor_crm_payment_method]; ?>;
    color: #fff !important;
    padding: 10px 25px;
    border-radius: 8px;
    transition: all .3s ease-in-out;
    outline: none;
    display: inline-block;
  }
  .mentor-crm-btn:hover, .mentor-crm-btn:focus {
      color: #<?php echo $sub_header_color[$mentor_crm_payment_method]; ?> !important;
      background: #fff !important;
      box-shadow: 0 0 0 1px #<?php echo $sub_header_color[$mentor_crm_payment_method]; ?>;
  }
  .out-to-store{
    text-align: center;
  }
  .mentor-crm-note{
    padding: 20px 0 5px;
  }
  </style>
  <div id="thanks-page-mentor-crm">
      <div class="mentor-crm-sub-header">
          <h1>
            <img src="<?php echo plugins_url('/mentor-crm/assets/method-'.$mentor_crm_payment_method.'.svg'); ?>" alt="Gracias por su compra!" />
            <?php echo __('Gracias por su compra!','jdmmlang'); ?>
          </h1>
      </div>
      <div class="mentor-crm-container">
          <div class="mentor-crm-note out-to-store">
            <h2><?php _e('Resumen Transacción','jdmmlang'); ?></h2>
            <p><?php _e('Nos contactaremos a la brevedad, para validar la información de tu cita.','jdmmlang'); ?></p>
          </div>
              <table class="mentor-crm-table">
                <tr>
                  <td><?php _e('Estado de la transaccion','jdmmlang'); ?></td>
                  <td><?php echo $estadoTransaction; ?></td>
                </tr>
                <tr>
                  <td><?php _e('ID de la transaccion','jdmmlang'); ?></td>
                  <td><?php echo $transactionId; ?></td>
                </tr>
                <tr>
                  <td><?php _e('Referencia de la venta','jdmmlang'); ?></td>
                  <td><?php echo $referenceCode; ?></td>
                </tr>
                <tr>
                  <td><?php _e('Monto','jdmmlang'); ?></td>
                  <td>$ <?php echo number_format($transactionValue,0,'','.'); ?></td>
                </tr>
            </table>
            <div class="out-to-store">
              <a href="<?php echo home_url(); ?>" class="mentor-crm-btn">Ir al Inicio</a>
            </div>
      </div>
  </div>
  <?php
  include_once $current_theme.'/footer.php';
}

