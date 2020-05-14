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
        $json = file_get_contents('php://input');
        validate_wompi_transaction($json);exit;
    }
    if( isset( $vars['mentor-crm-payu-events'] ) ) {
        validate_payu_transaction();exit;
    }
    if( isset( $vars['mentor-crm-payment'] ) ) {
        make_payment_checkout($vars['mentor-crm-payment-data']);exit;
    }
    if( isset( $vars['thanks-for-your-purchase'] ) ) {
        make_payment_thanks();exit;
    }
    return $vars;
}

