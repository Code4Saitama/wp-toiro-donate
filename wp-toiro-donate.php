<?php
/**
 * Plugin Name:     Social Project Donation Management
 * Plugin URI:      https://github.com/Code4Saitama/wp-toiro-donate
 * Description:     Toiro Donation
 * Author:          NPO-toiro-commune-designing
 * Author URI:      https://github.com/NPO-toiro-commune-designing
 * Text Domain:     social-project-donation-management
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         Wp_Toiro_Donate
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// 依存チェック
register_activation_hook(__FILE__, function() {
  // 依存プラグインが有効化されていない時は、エラーで処理を中断する
  if( is_bool(array_search('social-project-donation-with-payjp/simple-payjp-payment.php', get_option('active_plugins'))) ) {
    echo '寄付プラグインの動作には、Social Project Donation with PAY.JP プラグインが必要です。';
    exit();
  }
});

//DB Activate / DeActivate
global $donate_db_version;
$donate_db_version = 1.1;

if (is_file(plugin_dir_path( __FILE__ ) . '../social-project-donation-with-payjp/simple-payjp-payment.php')){
	require_once(plugin_dir_path( __FILE__ ) . "donor-view.php");
	require_once(plugin_dir_path( __FILE__ ) . "regist-paydata.php");
}

// Your code starts here.
include(plugin_dir_path( __FILE__ ) . 'donate-api.php');


// 管理メニューにフックを登録.
add_action( 'admin_menu', 'donate_add_pages' );

/**
 * メニューを追加する.
 * kan
 **/
function donate_add_pages() {
	$my_plugin_slug = plugin_basename( __FILE__ );

	/*
	add_menu_page(
		$page_title　：　ページタイトル（title）,
		$menu_title　：　メニュータイトル,
		$capability　：　メニュー表示するユーザーの権限,
		$menu_slug,　：　メニューのスラッグ,
		$function,　：　メニュー表示時に使われる関数,
		$icon_url,　：　メニューのテキスト左のアイコン,
		$position 　：　メニューを表示する位置;
	);
	*/
	// トップレベルにオリジナルのメニューを追加.
	add_menu_page(
		'寄付管理',
		'寄付管理',
		'read',
		$my_plugin_slug,
		'donate_menu_index',
		plugins_url( '/images/donate.png', __FILE__ )
	);

	/*
	add_submenu_page(
		$parent_slug,
		$page_title,
		$menu_title,
		$capability,
		$menu_slug,
		$function
	);
	*/

	add_submenu_page(
		$my_plugin_slug,
		'寄付プロジェクト登録',
		'寄付プロジェクト登録',
		'read',
		'project',
		'donate_menu_index'
	);

	add_submenu_page(
		$my_plugin_slug,
		'寄付確認',
		'寄付確認',
		'read',
		'donate',
		'donate_submenu_page1'
	);

	add_submenu_page(
		$my_plugin_slug,
		'寄付エクスポート',
		'寄付エクスポート',
		'read',
		'donate_export',
		'donate_submenu_page2'
	);


	add_submenu_page(
		$my_plugin_slug,
		'各種設定',
		'各種設定',
		'read',
		'option',
		'donate_options_page'
	);
}

/**
 * メニュー本体
 */
function donate_menu_index() {
	global $wpdb;
	$dir = plugin_dir_path( __FILE__ );
	$form_file = $dir . "form.html";
	$tbody_file = $dir . "tbody.html";
	$tbody_tpl = file_get_contents($tbody_file);
	$main = file_get_contents($form_file);
	$tbody = "";

	try {
		$query = "SELECT * FROM  wp_donate_project WHERE del_flag = 0";
		$results = $wpdb->get_results( $query );
	} catch (Exception $e) {
		echo '捕捉した例外: ',  $e->getMessage(), "\n";
	}

	if  (!is_null( $results ))  {
		$i=0;
		foreach($results as $row) {
			$id = $row->id;
			$project_code = $row->project_code;
			$project_name = $row->project_name;
			$description = $row->description;
			$url01 = $row->url01;
			$url02 = $row->url02;
			$thumbnail = $row->thumbnail;
			$logo = $row->logo;
			$create_date = $row->create_date;
			$update_date = $row->update_date;

			$row_data = $tbody_tpl;
			$row_data = str_replace('%I%', ++$i, $row_data);
			$row_data = str_replace('%ID%', $id, $row_data);
			$row_data = str_replace('%PROJECT_CODE%', $project_code, $row_data);
			$row_data = str_replace('%PROJECT_NAME%', $project_name, $row_data);
			$row_data = str_replace('%DESCRIPTION%', $description, $row_data);
			$row_data = str_replace('%URL01%', $url01, $row_data);
			$row_data = str_replace('%URL02%', $url02, $row_data);
			$row_data = str_replace('%THUMBNAIL%', $thumbnail, $row_data);
			$row_data = str_replace('%LOGO%', $logo, $row_data);
			$row_data = str_replace('%CREATE_DATE%', $create_date, $row_data);
			$row_data = str_replace('%UPDATE_DATE%', $update_date, $row_data);
			$tbody .= $row_data;

		}
	}
	$main = str_replace("%TBODY%", $tbody, $main);
	echo $main;
}

add_action( 'admin_init', 'donate_sumbit' );

/**
 *  Aaaa
 */
function donate_sumbit() {
	// TODO: 入力データの妥当性の判断
	// TODO: DBへの登録.
	global $wpdb;
	if (isset($_POST["row"]) && $_POST["row"] == "1"){
		$id = $_POST["id"];
		$wpdb->update(
			"wp_donate_project",
			array(
				"del_flag" => 1,
				'moderator' => get_current_user_id(),
				'update_date' => current_time('mysql'),
			),
			array( 'ID' =>  $id ),
		);


	} elseif (isset($_POST["form"]) && (isset($_POST['project_code']) || isset($_POST['project_name']))){
		$project_code = ($_POST['project_code']);
		$project_name = ($_POST['project_name']);
		$description = ($_POST['description']);
		$url01 = ($_POST['url01']);
		$url02 = ($_POST['url02']);
		$thumbnail = ($_POST['thumbnail']);
		$creator = get_current_user_id();
		$create_date = current_time('mysql');
		$moderator = get_current_user_id();
		$update_date = current_time('mysql');


		if (!empty($_POST["id"])){
			$id = $_POST["id"];
			$wpdb->update(
				"wp_donate_project",
				array(
					'project_code' => $project_code,
					'project_name' => $project_name,
					'description' => $description,
					'url01' => $url01,
					'url02' => $url02,
					'thumbnail' => $thumbnail,
					'moderator' => get_current_user_id(),
					'update_date' => current_time('mysql'),
				),
				array( 'ID' =>  $id ),
			);
		}else{
			$wpdb->insert(
				"wp_donate_project",
				array(
					'project_code' => $project_code,
					'project_name' => $project_name,
					'description' => $description,
					'url01' => $url01,
					'url02' => $url02,
					'thumbnail' => $thumbnail,
					'creator' => get_current_user_id(),
					'create_date' => current_time('mysql'),
					'moderator' => get_current_user_id(),
					'update_date' => current_time('mysql'),
				)
			);
		}
	} else{
		if (isset($_POST["csv_type"]) && $_POST["csv_type"] == 1){
			$from_date =  empty($_POST["date-from"]) ? date('Y-m-d', mktime(0, 0, 0, date("m")-1, date("d"), date("Y"))) : $_POST["date-from"];
			$to_date =  empty($_POST["date-to"]) ? date('Y-m-d', mktime(0, 0, 0, date("m"), date("d"), date("Y"))) : $_POST["date-to"];
			donate_export_csv($from_date, $to_date);
			exit;
		}
	}
}

/**
 * Aaa
 */
function donate_submenu_page1() {
	echo '<h2>寄付確認</h2>';
	global $wpdb;
	$dir = plugin_dir_path( __FILE__ );
	$form_file = $dir . "donate_table.html";
	$tbody_file = $dir . "donate_list.html";

	$tbody_tpl = file_get_contents($tbody_file);
	$main = file_get_contents($form_file);
	echo "<pre";
	echo htmlspecialchars($tbody_tpl);
	echo "</pre>";
	$tbody = "";
	try {
		$query = "SELECT d.*, p.project_name, p.project_code, t.payment_type as payment_name
		FROM wp_donate AS d
		LEFT JOIN wp_donate_project as p on (p.id = d.donate_project_id)
		LEFT JOIN wp_payment_type as t on (t.id = d.payment_type_id)
		WHERE d.del_flag = 0";
		$results = $wpdb->get_results( $query );

	} catch (Exception $e) {
		echo '捕捉した例外: ',  $e->getMessage(), "\n";
	}
	if  (!is_null( $results ))  {
		$i=0;
		foreach($results as $row) {

			$id = $row->id;
			$donate_project_id = $row->donate_project_id;
			$payment_id = $row->payment_id;
			$donor_email = $row->donor_email;
			$donor_name = $row->donor_name;
			$donor_zip = $row->donor_zip;
			$donor_address = $row->donor_address;
			$donor_tel = $row->donor_tel;
			$token = $row->token;
			$price = $row->price;
			$tax = $row->tax;
			$charge = $row->charge;
			$payment_type_id = $row->payment_type_id;
			$payment_date = $row->payment_date;
			$del_flag = $row->del_flag;
			$creator = $row->creator;
			$create_date = $row->create_date;
			$moderator = $row->moderator;
			$update_date = $row->update_date;
			$project_name = $row->project_name;
			$project_code = $row->project_code;

			$row_data = $tbody_tpl;
			$row_data = str_replace('%I%', ++$i, $row_data);
			$row_data = str_replace('%ID%', $id, $row_data);
			$row_data = str_replace('%PROJECT_CODE%', $project_code, $row_data);
			$row_data = str_replace('%PROJECT_NAME%', $project_name, $row_data);
			$row_data = str_replace('%PAYMENT_ID%', $payment_id, $row_data);
			$row_data = str_replace('%DONOR_NAME%', $donor_name, $row_data);
			$row_data = str_replace('%DONOR_ZIP%', $donor_zip, $row_data);
			$row_data = str_replace('%DONOR_ADDRESS%', $donor_address, $row_data);
			$row_data = str_replace('%DONOR_EMAIL%', $donor_email, $row_data);
			$row_data = str_replace('%DONOR_TEL%', $donor_tel, $row_data);
			$row_data = str_replace('%TOKEN%', $token, $row_data);
			$row_data = str_replace('%PRICE%', $price, $row_data);
			$row_data = str_replace('%CHARGE%', $charge, $row_data);
			$row_data = str_replace('%TAX%', $tax, $row_data);
			$row_data = str_replace('%PAYMENT_TYPE%', $payment_type, $row_data);
			$tbody .= $row_data;

		}
	}
	$main = str_replace("%TBODY%", $tbody, $main);
	echo $main;

}

/**
 * Baa
 */
function donate_submenu_page2() {
	$dir = plugin_dir_path( __FILE__ );
	$js_file = $dir . "date-ja.js";
	echo "<script>";
	echo file_get_contents($js_file);
	echo "</script>";
	echo '<h2>寄付データエクスポート</h2>';
	echo '<form method="post">';
	echo '<span>決済日付</span><input class="dt" type="text" name="date-from" id="date-from">～<input class="dt" type="text" name="date-to" id="date-to">';
	echo '<input type="submit" value="エクスポート" >';
	echo '<input type="hidden" name="csv_type" value="1">';
	echo '</form>';
	echo '<script>window.onload=function(){ jQuery(".dt").datepicker(
		{
			showButtonPanel: true,
			changeMonth: true,
			changeYear: true,
			firstDay: 0,
		}
	);}</script>';
	?>
	<style>
		.dt{
			width: 100px;
		}
		.ui-datepicker{
			background: #F3F3F3 !important;
		}

		.ui-state-active
		, .ui-widget-content .ui-state-active
		, .ui-widget-header .ui-state-active
		, a.ui-button:active
		, .ui-button:active
		, .ui-button.ui-state-active:hover {
			border: 1px solid #003eff !important;
			background: #007fff !important;
			font-weight: normal !important;
			color: #ffffff !important;
		}
		.ui-datepicker-holiday a.ui-state-default{
			border: 1px solid #ecc0c0;
			background-color: #ffecec !important;
			color: #ff0000 !important;
		}
		.ui-datepicker-holiday a.ui-state-active{
			border: 1px solid #003eff !important;
			background: #007fff !important;
			font-weight: normal !important;
			color: #ffffff !important;
		}
		/* 日曜日のカラー設定 */
		.ui-datepicker-week-end:first-child a{
			background-color: #ffecec;
			color: #ff0000;
		}
		/* 土曜日のカラー設定 */
		.ui-datepicker-week-end:last-child a{
			background-color: #eaeaff;
			color: #0000ff;
		}
	</style>

	<?php
}

function donate_export_csv($from_date, $to_date) {
	global $wpdb;

	$fields = array(
		"id", "donate_project_id","project_name", "project_code".
		"payment_id", "donor_email", "donor_name",
		"donor_zip", "donor_address", "donor_tel", "token", "price", "tax",
		"charge", "payment_type_id", "payment_type",
		"payment_date", "del_flag", "creator",
		"create_date", "moderator", "update_date");

		$fp = fopen('php://temp','r+');
	fputcsv($fp, $fields, ',', '"');

	$sql = sprintf("SELECT d.*, p.project_name, p.project_code, t.payment_type as payment_name FROM wp_donate AS d "
		. "LEFT JOIN wp_donate_project as p on (p.id = d.donate_project_id) "
		. "LEFT JOIN wp_payment_type as t on (t.id = d.payment_type_id) "
		. "WHERE d.del_flag = 0 AND payment_date between '%s' AND '%s';", $from_date, $to_date );

	$results = $wpdb->get_results( $sql );

	foreach($results as $row) {
		$data = array();
		$rows = (array)$row;
		foreach($fields as $field){
			$data[] = $rows[$field];
		}
		fputcsv($fp, $data, ',', '"');
	}

	header('Content-Type: text/csv');
	header('Content-Disposition: attachment; filename=donate_export.csv');
	rewind($fp);

	while (($buf = fgets($fp)) !== false) {
		echo mb_convert_encoding($buf,'SJIS-win',mb_internal_encoding());
	}

	fclose($fp);
}



/**
 * Caa
 */
function donate_options_page() {
	$api_key = get_option("donate_apikey", "");
	$default = "";
	$pay_jp_private_key = get_option("pay_jp_private_key", $default);
	$pay_jp_public_key = get_option("pay_jp_public_key", $default);
	$pay_jp_token = get_option("pay_jp_token", $default);

	if (isset($_POST["option"])){
		$api_key = empty($api_key) ?  md5(mt_rand().time()) : $api_key;
		add_option("donate_apikey", $api_key);

		if (!empty($_POST["pay_jp_private_key"])){
			$pay_jp_private_key = $_POST["pay_jp_private_key"];
			add_option("pay_jp_private_key", $_POST["pay_jp_private_key"]);
		}

		if (!empty($_POST["pay_jp_public_key"])){
			$pay_jp_public_key = $_POST["pay_jp_public_key"];
			add_option("pay_jp_public_key", $_POST["pay_jp_public_key"]);
		}

		if (!empty($_POST["pay_jp_token"])){
			$pay_jp_token = $_POST["pay_jp_token"];
			add_option("pay_jp_token", $_POST["pay_jp_token"]);
		}

	}

	echo '<h2>APIキーの発行</h2>';
	echo "
	<div class='wrap'>

	<form method='post' class='api'>
	<p><span> APIキー </span><input type='text' name='api_key' value='$api_key' readonly></p>
	<p><span> Pay.jp 秘密鍵 </span><input type='text' name='pay_jp_private_key' value='$pay_jp_private_key'></p>
	<p><span> Pay.jp 公開鍵 </span><input type='text' name='pay_jp_public_key' value='$pay_jp_public_key'></p>
	<p><span> Pay.jp トークン </span><input type='text' name='pay_jp_token' value='$pay_jp_token'></p>
	<br>
	<input type='submit' value='保存'>
	<input type='hidden' name='option' value='1'>
	</form>
	<style>
	.api input {
		width: 300px;
	}
	</style>
	</div>
	";

	echo "<hr>";

	echo "APIを呼び出すときには、下記のヘッダを追加してください<br>";
	echo "'X-API-KEY: $api_key'<br>";
}


function donate_activate() {
	error_log("DonateTable activate", 3 ,"/tmp/don.log");
	global $wpdb;
	global $donate_db_version;
	//DBのバージョン
	//現在のDBバージョン取得
	$installed_ver = get_option( 'donate_meta_version' );
	// DBバージョンが違ったら作成
	if( $installed_ver !=  $donate_db_version ) {
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		//SQL文をここに直接張ってください。
		$sql = <<<_SQL_
CREATE TABLE IF NOT EXISTS wp_donate_project (
	id  bigint  unsigned auto_increment not null primary key,
	project_code varchar(20),
	project_name varchar(200),
	description text,
	url01 varchar(200),
	url02 varchar(200),
	thumbnail varchar(200),
	logo varchar(200),
	del_flag boolean default 0 not null,
	creator varchar(20),
	create_date datetime  DEFAULT CURRENT_TIMESTAMP,
	moderator varchar(20),
	update_date datetime  DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS wp_donate (
	id bigint  unsigned auto_increment not null primary key,
	donate_project_id  bigint,
	payment_id  bigint,
	donor_email varchar(100),
	donor_name varchar(20),
	donor_zip varchar(20),
	donor_address varchar(200),
	donor_tel varchar(20),
	token varchar(200),
	price integer,
	tax integer,
	charge integer,
	payment_type_id integer,
	payment_date bigint,
	del_flag boolean default 0 not null,
	creator varchar(20),
	create_date datetime  DEFAULT CURRENT_TIMESTAMP,
	moderator varchar(20),
	update_date datetime  DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS wp_payment_type (
	id bigint  unsigned auto_increment not null primary key,
	payment_type varchar(20),
	del_flag boolean default 0 not null,
	create_date datetime,
	update_date datetime
);

replace into wp_payment_type values
(1,"クレジット", 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(2,"現金", 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(3,"口座振込　", 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(4,"Paypay", 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00');
_SQL_;
		//オプションにDBバージョン保存
		update_option('donate_meta_version', $donate_db_version);

		$queries = explode(";", $sql);
		foreach ($queries as $qry){
			if (!empty($qry)){
				$wpdb->query($qry);
			}
		}
	}
}

function donate_deactivate() {
	error_log("DonateTable deactivate", 3 ,"/tmp/don.log");
	// プラグインが無効となったときの処理
	global $wpdb;
	//DBのバージョン
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	//SQL文をここに直接張ってください。
	$sql = <<<_SQL_
DROP TABLE IF EXISTS wp_donate_project;
DROP TABLE IF EXISTS wp_donate;
DROP TABLE IF EXISTS wp_payment_type;
_SQL_;
	//オプションにDBバージョン削除
	delete_option('donate_meta_version');
	$queries = explode(";", $sql);
	foreach ($queries as $qry){
		if (!empty($qry)){
			$wpdb->query($qry);
		}
	}

}

register_activation_hook(__FILE__, 'donate_activate');
register_deactivation_hook( __FILE__,'donate_deactivate');
