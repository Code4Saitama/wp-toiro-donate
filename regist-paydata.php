<?php

function regist_pay_data($param)
{
	global $wpdb;
	$result = '';
	$wpdb->insert( 
	    "wp_donate",
	    array(
		'id' => '0',
		'donate_project_id' => $param['donate_project_id'],
		'payment_id' => $param['payment_id'],
		'donor_email' => $param['donor_email'],
		'donor_name' => $param['donor_name'],
		'donor_zip' => $param['donor_zip'],
		'donor_address' => $param['donor_address'],
		'donor_tel' => $param['donor_tel'],
		'token' => $param['token'],
		'price' => $param['price'],
		'tax' => $param['tax'],
		'charge' => $param['charge'],
		'payment_type_id' => '1',
		'payment_date' => current_time('mysql'),
		'del_flag' => '0',
		'creator' => get_current_user_id(),
		'create_date' => current_time('mysql'),
		'moderator' => get_current_user_id(),
		'update_date' => current_time('mysql'),
	    )
	);


	// エラーを判定できる
	if( ! $rows || $wpdb->insert_id === 0 ){
		$result .=  $wpdb->get_results( $sql );
		$result .=  $wpdb->get_insert;
	}

	// エラーを判定できない
	if( $wpdb->last_error ){
		$result .= $wpdb->last_error;
	}

	return $result;

}

add_action('simplepayjppayment_result_ok', 'regist_pay_data');


?>
