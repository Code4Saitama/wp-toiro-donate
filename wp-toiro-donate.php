<?php
/**
 * Plugin Name:     Wp Toiro Donate
 * Plugin URI:      https://wwww.github.com/
 * Description:     Torio Donataion
 * Author:          MapQuest Solutions LLC.
 * Author URI:      http://www.github.com/mq-sol/
 * Text Domain:     wp-toiro-donate
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         Wp_Toiro_Donate
 */

// Your code starts here.


// 管理メニューにフックを登録.
add_action( 'admin_menu', 'donate_add_pages' );

/**
 * メニューを追加する.
 * kan
 **/
function donate_add_pages() {
	$my_plugin_slug = plugin_basename( __FILE__ );

	// トップレベルにオリジナルのメニューを追加.
	add_menu_page(
		'寄付管理',
		'寄付管理',
		'read',
		$my_plugin_slug,
		'donate_menu_index',
		plugins_url( '/images/donate.png', __FILE__ )
	);

	add_submenu_page(
		$my_plugin_slug,
		'寄付管理',
		'寄付プロジェクト登録',
		'read',
		$my_plugin_slug,
		'donate_menu_index'
	);

	add_submenu_page(
		$my_plugin_slug,
		'寄付確認',
		'寄付確認',
		'moderate_comments',
		'submenu-1',
		'donate_submenu_page1'
	);

	add_submenu_page(
		$my_plugin_slug,
		'寄付エクスポート',
		'寄付エクスポート',
		'manage_options',
		'submenu-2',
		'donate_submenu_page2'
	);

	add_options_page(
		'マイオプション',
		'マイオプション',
		'manage_options',
		'donate-options-submenu',
		'donate_options_page'
	);
}

/**
 * メニュー本体
 */
function donate_menu_index() {
	global $wpdb;
	$dir = plugin_dir_path( __FILE__ );
	$form_file = $dir . "/form.html"; 
	$tbody_file = $dir . "/tbody.html";
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
		$project_code = $wpdb->esc_sql($_POST['project_code']);
		$project_name = $wpdb->esc_sql($_POST['project_name']);
		$description = $wpdb->esc_sql($_POST['description']);
		$url01 = $wpdb->esc_sql($_POST['url01']);
		$url02 = $wpdb->esc_sql($_POST['url02']);
		$thumbnail = $wpdb->esc_sql($_POST['thumbnail']);
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
	}
}

/**
 * Aaa
 */
function donate_submenu_page1() {
	echo '<h2>寄付確認</h2>';
}

/**
 * Baa
 */
function donate_submenu_page2() {
	echo '<h2>寄付データエクスポート</h2>';
}

/**
 * Caa
 */
function donate_options_page() {
	echo '<h2>「設定」内の「マイオプション」をクリックした時に表示される内容</h2>';
}
