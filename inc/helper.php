<?php
/**
 * Generate a Unique String for Orders.
 *
 * this function generate a string, like hash for use as unique reference
 *
 * @since 1.0.0
 *
 * @param int $ID_from_DB order id from DB to generate a unique reference
 * @return string
 */
if (!function_exists('GenerateOrderHash')) {
    function GenerateOrderHash($ID_from_DB = 0){
        $length = 10;
        $characters = 'abcdefghijklmnopqrstuvwxyz0123456789';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        } 
        $hash = 'T'.$ID_from_DB.'-'.$randomString;
        return $hash;
    }
}

/**
 * Generate Order.
 *
 * this function generate a order with automatic unique reference
 *
 * @since 1.0.0
 *
 * @param int $LID lead ID relation
 * @param int | float $amount amount of order
 * @param bool $return_url bool to return url or Order ID
 * @return string
 */
if (!function_exists('CreateOrder')) {
    function CreateOrder($LID,$amount = 200000,$return_url= true){
        global $wpdb;
        $ORID = false;
        $wpdb->insert( 
            "{$wpdb->prefix}mentor_orders", 
            array(
                'LID' => $LID,
                'amount' => $amount
            ), 
            array( 
                '%d', 
                '%f', 
            ) 
        );
        $ORID = $wpdb->insert_id;
        $reference_hash = GenerateOrderHash($ORID);
        $wpdb->update( 
            "{$wpdb->prefix}mentor_orders", 
            array( 'reference' => $reference_hash ), 
            array( 'ORID' => $ORID ), 
            array( '%s' ), 
            array( '%d' ) 
        );
        if ($return_url) {
            return trailingslashit(home_url()).'mentor-crm-payment/'.$reference_hash;
        }else{
            return $ORID;
        }
        
    }
}
/**
 * Divide value to 100 for cents. and retrive to tens
 *
 * @since 1.0.0
 *
 * @param int $amount amount to divide or murtiply
 * @return int
 */
if (!function_exists('get_amount_in_cents')) {
    function get_amount_in_cents( $amount ) {
        return (int) ( $amount * 100 );
    }
}
if (!function_exists('get_amount_in_tens')) {
    function get_amount_in_tens( $amount ) {
        return (int) ( $amount / 100 );
    }
}
/**
 * validate_changes_log.
 *
 * dummy text
 *
 * @since 1.0.0
 *
 * @param int $amount amount to divide
 * @return int
 */
if (!function_exists('validate_changes_log')) {
    function validate_changes_log($validate_field_logs,$lid){
    	global $wpdb,$_POST;
    	$row_to_validate = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}mentor_leads WHERE LID = {$lid}");
    	foreach ($validate_field_logs as $key => $log) {
    	    if ($row_to_validate->{$key} != $_POST[$key]) {
    	        $wpdb->insert($wpdb->prefix."mentor_logs",
    				array(
    					'lead_ID'=>$_POST['lid'],
    					'message'=>$log.shortcode_text_crm($key,$row_to_validate->{$key},$_POST[$key]),
    					'asesor' => wp_get_current_user()->ID,
    					'last_update' => current_time('mysql')
    				)
    	        );
    	    }
    	}	
    }
}
/**
 * shortcode_text_crm.
 *
 * dummy text
 *
 * @since 1.0.0
 *
 * @param int $amount amount to divide
 * @return int
 */
if (!function_exists('shortcode_text_crm')) {
    function shortcode_text_crm($type,$from,$to){
    	global $wpdb,$managers,$payment_state_text;
    	switch ($type) {
    		case 'step_ID':
    			$title1 = $wpdb->get_var( "SELECT title FROM {$wpdb->prefix}mentor_steps WHERE SID = {$from}");
    			$title2 = $wpdb->get_var( "SELECT title FROM {$wpdb->prefix}mentor_steps WHERE SID = {$to}");
    			$return = ' de: '.$title1.' a: '.$title2;
    			break;
    		case 'reason':
    			$return = ' de: '.$from.' a: '.$to;
    			break;
    		case 'manage':
                $manage_from = $wpdb->get_var( "SELECT name FROM {$wpdb->prefix}mentor_managers WHERE MID = {$from}");
                $manage_to = $wpdb->get_var( "SELECT name FROM {$wpdb->prefix}mentor_managers WHERE MID = {$to}");
    			$return = ' de: '.$manage_from.' a: '.$manage_to;
    			break;
    		case 'date':
    			$return = ' de: '.date('Y/m/d',strtotime($from)).' a: '.date('Y/m/d',strtotime($to));
    			break;
    		case 'time':
    			$return = ' de: '.date('H:i A',strtotime($from)).' a: '.date('H:i A',strtotime($to));
    			break;
    		case 'confirm_date':
    			$notyes = array('NO','SI');
    			$return = ' de: '.$notyes[$from].' a: '.$notyes[$to];
    			break;
    		case 'cost':
    			$return = ' de: '.$from.' a: '.$to;
    			break;
    		case 'payment_state':
    			$return = ' de: '.$payment_state_text[$from].' a: '.$payment_state_text[$to];
    			break;
    		case 'state':
    			$notyes = array('NO','SI');
    			$return = ' de: '.$notyes[$from].' a: '.$notyes[$to];
    			break;
    		default:
    			$return = ' no text.';
    			break;
    	}
    	return $return;
    }
}
/**
 * mentor_email_calendar.
 *
 * dummy text
 *
 * @since 1.0.0
 *
 * @param int $amount amount to divide
 * @return int
 */
if (!function_exists('mentor_email_calendar')) {
    function mentor_email_calendar($data = array()){
        $start_timestamp = strtotime('31-01-2019');
        $end_timestamp = strtotime('31-02-2019');
        $rest_name = 'test locatio';
        $cust_email = $customer_name = $from_email = 'admin@mediacore.com.ar';
        $restaurant_city = 'Buenos Aires';
        $ics_reservation_id = $start_timestamp.'-'.$from_email;

        $i_calendar="BEGIN:VCALENDAR
        PRODID:-//Microsoft Corporation//Outlook 10.0 MIMEDIR//EN
        VERSION:2.0
        CALSCALE:GREGORIAN
        METHOD:REQUEST
        BEGIN:VEVENT
        DTSTART:".$start_timestamp."
        DTEND:".$end_timestamp."
        DTSTAMP:".$start_timestamp."
        ORGANIZER;CN=".$rest_name.":mailto:".$from_email."
        UID:".$ics_reservation_id."
        ATTENDEE;PARTSTAT=NEEDS-ACTION;RSVP=TRUE;CN=".$customer_name.":mailto:".$cust_email."
        DESCRIPTION:test
        LOCATION:".$restaurant_city."
        SEQUENCE:0
        STATUS:CONFIRMED
        SUMMARY:SI SE PUEDE FABIAN XD
        TRANSP:OPAQUE
        END:VEVENT
        END:VCALENDAR";
    }
}
/**
 * mentor_email.
 *
 * dummy text
 *
 * @since 1.0.0
 *
 * @param int $amount amount to divide
 * @return int
 */
if (!function_exists('mentor_email')) {
    function mentor_email($to,$subject,$body,$include = array(),$lid = null,$headers = null){
        global $wpdb,$phpmailer;
        $logo = get_option('mentor_crm_logo');
        $site_url = home_url();
        $first_admin = explode(',', get_option('mentor_crm_admin_notify'));
        $client_name = get_option('mentor_crm_cliente_name');
        $year = date('Y');
        $body_email = "<div style='margin:0;font-family: Arial, Helvetica, sans-serif;background: #f7f5ff;width: 100%;height: 100%;box-sizing:border-box;'>
                <div style='max-width: 600px;display: block;margin: 0 auto;padding: 30px;box-sizing:border-box;'>
                    <div style='width: 100%;height: 80px;background: #fff;border-bottom:2px solid #e0dede;box-sizing:border-box;'>
                        <a href='{$site_url}' style='display: block;margin: 0 auto;width:200px;padding:10px;box-sizing:border-box;' title='{$client_name}'>
                        <img src='{$logo}' style='width: 100%;box-sizing:border-box;' alt='{$client_name}'/>
                        </a>
                    </div>
                    <div style='background:#fff;padding:30px 50px;width:100%;display:block;max-width: 100%;box-sizing: border-box;'>
                    {$body}
                    </div>
                    <div style='width: 100%;padding:30px;background: #e0dede;text-align:center;box-sizing: border-box;'>
                        <p><b>{$client_name} &copy; {$year}</b></p>
                        <p style='font-size: 80%;color: #636363;box-sizing:border-box;'>Este correo y cualquier archivo transmitidos con él son confidenciales y previsto solamente para el uso del individuo o de la entidad a quienes se tratan. Si UD. ha recibido este correo por error por favor notificar a {$first_admin} Por favor considere que cualquier opinión presentada en este correo es solamente la del autor y no representa necesariamente la opinión de {$client_name} Finalmente, el receptor debe comprobar este correo y cualquier anexo del mismo para identificar la presencia de virus. La compañía no acepta ninguna responsabilidad por ningún daño causado por algún virus transmitido en este correo.</p>
                    </div>
                </div>
                </div>";
        if (in_array(1, $include)) {
            if (!empty($lid)) {
                $imgs_src = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}mentor_leads_images WHERE LID = {$lid}");
                add_action( 'phpmailer_init', function(&$phpmailer)use($imgs_src){
                    $phpmailer->SMTPKeepAlive = true;
                    $phpmailer->IsHTML(true);
                    foreach ($imgs_src as $key => $img) {            
                        $file = $img->image;
                        $uid = 'SecureFile'.$img->LIMGID; //will map it to this UID
                        $finfo = new finfo(FILEINFO_MIME);
                        $extension = explode('/', mime_content_type($file))[1];
                        $name = 'SecureFile'.$img->LIMGID.'.'.$extension;
                        switch ($extension) {
                            case 'gif':
                                $img = str_replace('data:image/gif;base64,', '', $file);
                                break;
                            case 'jpeg':
                                $img = str_replace('data:image/jpeg;base64,', '', $file);
                                break;
                            case 'jpg':
                                $img = str_replace('data:image/jpg;base64,', '', $file);
                                break;
                            default:
                                $img = str_replace('data:image/png;base64,', '', $file);
                                break;
                        }
                        $img = str_replace(' ', '+', $img);
                        $data = base64_decode($img);
                        //$phpmailer->addStringEmbeddedImage($data,$uid,$name);
                        $phpmailer->addStringAttachment($data,$name);     
                    }
                });
            }
        }
        if (in_array(2, $include)) {
            if (!empty($lid)) {
                $calendar_file = mentor_email_calendar($lid);
                add_action( 'phpmailer_init', function(&$phpmailer)use($calendar_file){
                    $phpmailer->SMTPKeepAlive = true;
                    $phpmailer->IsHTML(true);
                    $phpmailer->addStringAttachment($data,$name);
                    $mail->addStringAttachment($calendar_file, 'calendar_file.ics');
                });
            }
        }
        if (empty($headers)) {
           $headers = array('Content-Type: text/html; charset=UTF-8');
           //,'From: Dev Mentor <dev@bementor.co>'
        }
        wp_mail($to,$subject,$body_email,$headers);
    }
}
/**
 * print_state_order.
 *
 * dummy text
 *
 * @since 1.0.0
 *
 * @param int $amount amount to divide
 * @return int
 */
if (!function_exists('print_state_order')) {
    function print_state_order($state=1,$echo = false){
        global $payment_state_text;
        $icons = array(1=>'warning',2=>'yes-alt',3=>'dismiss');
        $state_html =' <span class="payment_state_label state_'.$state.'">'.$payment_state_text[$state].' <span class="dashicons dashicons-'.$icons[$state].'"></span></span>';
        if ($echo) {
            echo $state_html;
        }else{
            return $state_html;
        }
    }
}
add_action('admin_head', 'mentor_crm_admin_style');
function mentor_crm_admin_style() {
  echo '<style>
    #toplevel_page_mentor-crm-admin .wp-submenu .wp-first-item {
        display: none;
    }
    #toplevel_page_mentor-crm-admin *{
        color: #fff !important;
    }
    #toplevel_page_mentor-crm-admin a .wp-menu-name {
        background: linear-gradient(81deg, #de4686 0%, #ff8842 100%);
    }
  </style>';
}
if (empty($payment_state_text)) {
    $payment_state_text = array(1=>'PENDIENTE',2=>'PAGADO',3=>'CANCELADO/EXP.');
}
if (empty($crmcountries)) {
    $crmcountries = array(
        "AL" => 'Albania',
        "DZ" => 'Algeria',
        "AS" => 'American Samoa',
        "AD" => 'Andorra',
        "AO" => 'Angola',
        "AI" => 'Anguilla',
        "AQ" => 'Antarctica',
        "AG" => 'Antigua and Barbuda',
        "AR" => 'Argentina',
        "AM" => 'Armenia',
        "AW" => 'Aruba',
        "AU" => 'Australia',
        "AT" => 'Austria',
        "AZ" => 'Azerbaijan',
        "BS" => 'Bahamas',
        "BH" => 'Bahrain',
        "BD" => 'Bangladesh',
        "BB" => 'Barbados',
        "BY" => 'Belarus',
        "BE" => 'Belgium',
        "BZ" => 'Belize',
        "BJ" => 'Benin',
        "BM" => 'Bermuda',
        "BT" => 'Bhutan',
        "BO" => 'Bolivia',
        "BA" => 'Bosnia and Herzegovina',
        "BW" => 'Botswana',
        "BV" => 'Bouvet Island',
        "BR" => 'Brazil',
        "BQ" => 'British Antarctic Territory',
        "IO" => 'British Indian Ocean Territory',
        "VG" => 'British Virgin Islands',
        "BN" => 'Brunei',
        "BG" => 'Bulgaria',
        "BF" => 'Burkina Faso',
        "BI" => 'Burundi',
        "KH" => 'Cambodia',
        "CM" => 'Cameroon',
        "CA" => 'Canada',
        "CT" => 'Canton and Enderbury Islands',
        "CV" => 'Cape Verde',
        "KY" => 'Cayman Islands',
        "CF" => 'Central African Republic',
        "TD" => 'Chad',
        "CL" => 'Chile',
        "CN" => 'China',
        "CX" => 'Christmas Island',
        "CC" => 'Cocos [Keeling] Islands',
        "CO" => 'Colombia',
        "KM" => 'Comoros',
        "CG" => 'Congo - Brazzaville',
        "CD" => 'Congo - Kinshasa',
        "CK" => 'Cook Islands',
        "CR" => 'Costa Rica',
        "HR" => 'Croatia',
        "CU" => 'Cuba',
        "CY" => 'Cyprus',
        "CZ" => 'Czech Republic',
        "CI" => 'Côte d’Ivoire',
        "DK" => 'Denmark',
        "DJ" => 'Djibouti',
        "DM" => 'Dominica',
        "DO" => 'Dominican Republic',
        "NQ" => 'Dronning Maud Land',
        "DD" => 'East Germany',
        "EC" => 'Ecuador',
        "EG" => 'Egypt',
        "SV" => 'El Salvador',
        "GQ" => 'Equatorial Guinea',
        "ER" => 'Eritrea',
        "EE" => 'Estonia',
        "ET" => 'Ethiopia',
        "FK" => 'Falkland Islands',
        "FO" => 'Faroe Islands',
        "FJ" => 'Fiji',
        "FI" => 'Finland',
        "FR" => 'France',
        "GF" => 'French Guiana',
        "PF" => 'French Polynesia',
        "TF" => 'French Southern Territories',
        "FQ" => 'French Southern and Antarctic Territories',
        "GA" => 'Gabon',
        "GM" => 'Gambia',
        "GE" => 'Georgia',
        "DE" => 'Germany',
        "GH" => 'Ghana',
        "GI" => 'Gibraltar',
        "GR" => 'Greece',
        "GL" => 'Greenland',
        "GD" => 'Grenada',
        "GP" => 'Guadeloupe',
        "GU" => 'Guam',
        "GT" => 'Guatemala',
        "GG" => 'Guernsey',
        "GN" => 'Guinea',
        "GW" => 'Guinea-Bissau',
        "GY" => 'Guyana',
        "HT" => 'Haiti',
        "HM" => 'Heard Island and McDonald Islands',
        "HN" => 'Honduras',
        "HK" => 'Hong Kong SAR China',
        "HU" => 'Hungary',
        "IS" => 'Iceland',
        "IN" => 'India',
        "ID" => 'Indonesia',
        "IR" => 'Iran',
        "IQ" => 'Iraq',
        "IE" => 'Ireland',
        "IM" => 'Isle of Man',
        "IL" => 'Israel',
        "IT" => 'Italy',
        "JM" => 'Jamaica',
        "JP" => 'Japan',
        "JE" => 'Jersey',
        "JT" => 'Johnston Island',
        "JO" => 'Jordan',
        "KZ" => 'Kazakhstan',
        "KE" => 'Kenya',
        "KI" => 'Kiribati',
        "KW" => 'Kuwait',
        "KG" => 'Kyrgyzstan',
        "LA" => 'Laos',
        "LV" => 'Latvia',
        "LB" => 'Lebanon',
        "LS" => 'Lesotho',
        "LR" => 'Liberia',
        "LY" => 'Libya',
        "LI" => 'Liechtenstein',
        "LT" => 'Lithuania',
        "LU" => 'Luxembourg',
        "MO" => 'Macau SAR China',
        "MK" => 'Macedonia',
        "MG" => 'Madagascar',
        "MW" => 'Malawi',
        "MY" => 'Malaysia',
        "MV" => 'Maldives',
        "ML" => 'Mali',
        "MT" => 'Malta',
        "MH" => 'Marshall Islands',
        "MQ" => 'Martinique',
        "MR" => 'Mauritania',
        "MU" => 'Mauritius',
        "YT" => 'Mayotte',
        "FX" => 'Metropolitan France',
        "MX" => 'Mexico',
        "FM" => 'Micronesia',
        "MI" => 'Midway Islands',
        "MD" => 'Moldova',
        "MC" => 'Monaco',
        "MN" => 'Mongolia',
        "ME" => 'Montenegro',
        "MS" => 'Montserrat',
        "MA" => 'Morocco',
        "MZ" => 'Mozambique',
        "MM" => 'Myanmar [Burma]',
        "NA" => 'Namibia',
        "NR" => 'Nauru',
        "NP" => 'Nepal',
        "NL" => 'Netherlands',
        "AN" => 'Netherlands Antilles',
        "NT" => 'Neutral Zone',
        "NC" => 'New Caledonia',
        "NZ" => 'New Zealand',
        "NI" => 'Nicaragua',
        "NE" => 'Niger',
        "NG" => 'Nigeria',
        "NU" => 'Niue',
        "NF" => 'Norfolk Island',
        "KP" => 'North Korea',
        "VD" => 'North Vietnam',
        "MP" => 'Northern Mariana Islands',
        "NO" => 'Norway',
        "OM" => 'Oman',
        "PC" => 'Pacific Islands Trust Territory',
        "PK" => 'Pakistan',
        "PW" => 'Palau',
        "PS" => 'Palestinian Territories',
        "PA" => 'Panama',
        "PZ" => 'Panama Canal Zone',
        "PG" => 'Papua New Guinea',
        "PY" => 'Paraguay',
        "YD" => 'People\'s Democratic Republic of Yemen',
        "PE" => 'Peru',
        "PH" => 'Philippines',
        "PN" => 'Pitcairn Islands',
        "PL" => 'Poland',
        "PT" => 'Portugal',
        "PR" => 'Puerto Rico',
        "QA" => 'Qatar',
        "RO" => 'Romania',
        "RU" => 'Russia',
        "RW" => 'Rwanda',
        "RE" => 'Réunion',
        "BL" => 'Saint Barthélemy',
        "SH" => 'Saint Helena',
        "KN" => 'Saint Kitts and Nevis',
        "LC" => 'Saint Lucia',
        "MF" => 'Saint Martin',
        "PM" => 'Saint Pierre and Miquelon',
        "VC" => 'Saint Vincent and the Grenadines',
        "WS" => 'Samoa',
        "SM" => 'San Marino',
        "SA" => 'Saudi Arabia',
        "SN" => 'Senegal',
        "RS" => 'Serbia',
        "CS" => 'Serbia and Montenegro',
        "SC" => 'Seychelles',
        "SL" => 'Sierra Leone',
        "SG" => 'Singapore',
        "SK" => 'Slovakia',
        "SI" => 'Slovenia',
        "SB" => 'Solomon Islands',
        "SO" => 'Somalia',
        "ZA" => 'South Africa',
        "GS" => 'South Georgia and the South Sandwich Islands',
        "KR" => 'South Korea',
        "ES" => 'Spain',
        "LK" => 'Sri Lanka',
        "SD" => 'Sudan',
        "SR" => 'Suriname',
        "SJ" => 'Svalbard and Jan Mayen',
        "SZ" => 'Swaziland',
        "SE" => 'Sweden',
        "CH" => 'Switzerland',
        "SY" => 'Syria',
        "ST" => 'São Tomé and Príncipe',
        "TW" => 'Taiwan',
        "TJ" => 'Tajikistan',
        "TZ" => 'Tanzania',
        "TH" => 'Thailand',
        "TL" => 'Timor-Leste',
        "TG" => 'Togo',
        "TK" => 'Tokelau',
        "TO" => 'Tonga',
        "TT" => 'Trinidad and Tobago',
        "TN" => 'Tunisia',
        "TR" => 'Turkey',
        "TM" => 'Turkmenistan',
        "TC" => 'Turks and Caicos Islands',
        "TV" => 'Tuvalu',
        "UM" => 'U.S. Minor Outlying Islands',
        "PU" => 'U.S. Miscellaneous Pacific Islands',
        "VI" => 'U.S. Virgin Islands',
        "UG" => 'Uganda',
        "UA" => 'Ukraine',
        "SU" => 'Union of Soviet Socialist Republics',
        "AE" => 'United Arab Emirates',
        "GB" => 'United Kingdom',
        "US" => 'United States',
        "ZZ" => 'Unknown or Invalid Region',
        "UY" => 'Uruguay',
        "UZ" => 'Uzbekistan',
        "VU" => 'Vanuatu',
        "VA" => 'Vatican City',
        "VE" => 'Venezuela',
        "VN" => 'Vietnam',
        "WK" => 'Wake Island',
        "WF" => 'Wallis and Futuna',
        "EH" => 'Western Sahara',
        "YE" => 'Yemen',
        "ZM" => 'Zambia',
        "ZW" => 'Zimbabwe',
        "AX" => 'Åland Islands',
      );
}