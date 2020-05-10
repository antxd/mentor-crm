<?php
add_action( 'init', 'josem_plugin_rewrites' );
function josem_plugin_rewrites(){
    add_rewrite_rule(
        '^mentor-crm-wompi-events-dev',
        'index.php?mentor-crm-wompi-events=true',
        'top'
    );
    add_rewrite_rule(
        '^mentor-crm-wompi-events',
        'index.php?mentor-crm-wompi-events=true',
        'top'
    );
    add_rewrite_rule(
        '^mentor-crm-payu-events',
        'index.php?mentor-crm-payu-events=true',
        'top'
    );
    add_rewrite_rule(
        '^mentor-crm-payment/?([^/]*)/?',
        'index.php?mentor-crm-payment=true&mentor-crm-payment-data=$matches[1]',
        'top'
    );
    add_rewrite_rule(
        '^thanks-for-your-purchase',
        'index.php?thanks-for-your-purchase=true',
        'top'
    );
}
add_filter('query_vars', 'josem_plugin_query_vars');
function josem_plugin_query_vars($vars) {
  $vars[] = 'mentor-crm-wompi-events';
  $vars[] = 'mentor-crm-payu-events';
  $vars[] = 'mentor-crm-payment';
  $vars[] = 'mentor-crm-payment-data';
  $vars[] = 'thanks-for-your-purchase';
  return $vars;
}

add_filter( 'request', 'josem_rewrite_filter_request' );
function josem_rewrite_filter_request($vars){
    if( isset( $vars['mentor-crm-wompi-events'] ) ) {
        validate_wompi_transaction();exit;
    }
    if( isset( $vars['mentor-crm-payu-events'] ) ) {
        validate_payu_transaction();exit;
    }
    if( isset( $vars['mentor-crm-payment'] ) ) {
        make_payment_checkout();exit;
    }
    if( isset( $vars['thanks-for-your-purchase'] ) ) {
        make_payment_thanks();exit;
    }
    /*if ( isset($vars['mentor-crm-payu-events'] ) ) {
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
    } */
    return $vars;
}


//email_test();
//add_action( 'plugins_loaded', 'email_test' );