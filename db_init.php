<?php
/*
	プラグイン読み込み時にDBを生成
*/
require_once dirname(__FILE__).DIRECTORY_SEPARATOR."db_sql.php";
global $db_version;
$db_version = '1.0';

function db_install() {
	global $wpdb;
	global $db_version;

	$sqls = array();
	array_push( $sqls, sql_donate_project());
	array_push( $sqls, sql_donate_price());
	array_push( $sqls, sql_donate_relation());
	array_push( $sqls, sql_donate());
	array_push( $sqls, sql_donor());
	
	foreach ($sqls as $value){
		$create_sql = $value["create"];
		dbDelta( $create_sql );
	}




	add_option( 'db_version', $db_version );
}

function install_data() {
	global $wpdb;

	$welcome_name = 'Wordpress さん';
	$welcome_text = 'おめでとうございます、インストールに成功しました！';

	$table_name = $wpdb->prefix . 'liveshoutbox';
	$wpdb->insert( $table_name,
		array(
			"project_code" => "donate_prj_001",
			"project_name" => "寄付プロジェクト001",
			"description" => "寄付プロジェクトテスト",
			"create_time" => current_time( 'mysql' ),
			"update_time" => current_time( 'mysql' )
		)
	);
}
