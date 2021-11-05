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
		plugins_url( '/images/logo.png', __FILE__ )
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
	?>
	<div class='wrap'>
		<h2>寄付プロジェクト登録</h2>
		<form id='donate-project-form' action=''>
			<?php wp_nonce_field( 'donate-nonce-key', 'donate-project' ); ?>
			<div class='form-left'>プロジェクトコード</div>
			<div class='form-right'><input type='text' name='project_code'></div>
			<div class='from-left'>プロジェクト名</div>
			<dif class='form-right'><input type='text' name='project_name'></div>
			<div class='form-left'>プロジェクトの説明</div>
			<div class='form-right'><textarea name='description'></textarea></div>
			<input type='submit' text='Save'>
		</form>
	</div>
	<div class='wrap'>
	<form id='' acction="">
	<table class='projects' style='border: 1px solid black'>
		<tr>
			<th><br></th>
			<th>no</th>
			<th>プロジェクトコード</th>
			<th>プロジェクト名</th>
			<th>プロジェクトの説明</th>
			<th>登録日</th>
			<th>最終更新日</th>
		</tr>

	<?php
	// phpcs:disable Array double arrow not aligned correctly
	/*
	$arr = array(
		array(
			'id' => 1,
			'project_code' => 'prj001',
			'project_name' => 'プロジェクト001',
			'description' => 'プロジェクトの説明文',
			'create_date' => '2021-10-30',
			'update_date' => '2021-11-03',
		),
		array(
			'id' => 2,
			'project_code' => 'prj002',
			'project_name' => 'プロジェクト002',
			'description' => 'プロジェクトの説明文',
			'create_date' => '2021-10-31',
			'update_date' => '2021-11-02',
		),
		array(
			'id' => 3,
			'project_code' => 'prj003',
			'project_name' => 'プロジェクト003',
			'description' => 'プロジェクトの説明文',
			'create_date' => '2021-11-03',
			'update_date' => '2021-11-10',
		),
	);
	+/
	// phpcs:enable
	$query = "SELECT * FROM  wp_donate_project WHERE del_flag = 0;
	$results = $wpdb->get_results( $wpdb->prepare( $query, 1, $type ) );
	foreach($results as $row) {
	   $id = $row->id;
	   $project_code = $row->project_code;
	   $project_name = $row->project_name;
	   $description = $row->description;
	   $create_date = $row->create_date;
	   $update_date = $row->update_date;

 		echo '<tr>';
		echo '<td>';
		echo '<input type="checkbox" name='"del_id_ .  esc_url($id) . '"  id="'  .   esc_url($id) .  '"/>';
		echo '</td>';
		echo '<td>';
		echo esc_url( ++$i );
		echo '</td>';
		echo '<td>';
			echo esc_url( $project_code );
		echo '</td>';
		echo '<td>';
			echo esc_url( $project_name );
		echo '</td>';
		echo '<td>';
			echo esc_url( $description );
		echo '</td>';
		echo '<td>';
			echo esc_url( $create_date );
		echo '</td>';
		echo '<td>';
			echo esc_url( $update_date );
		echo '</td>';
		echo '</tr>';

	}
	echo '</table></form></div>';
}

add_action( 'admin_init', 'donate_sumbit' );

/**
 *  Aaaa
 */
function donate_sumit() {
	// TODO: 入力データの妥当性の判断
	// TODO: DBへの登録.
}

/**
 * Aaa
 */
function donate_submenu_page1() {
	echo '<h2>「サブメニュー1」をクリックした時に表示される内容</h2>';
}

/**
 * Baa
 */
function donate_submenu_page2() {
	echo '<h2>「サブメニュー2」をクリックした時に表示される内容</h2>';
}

/**
 * Caa
 */
function donate_options_page() {
	echo '<h2>「設定」内の「マイオプション」をクリックした時に表示される内容</h2>';
}
