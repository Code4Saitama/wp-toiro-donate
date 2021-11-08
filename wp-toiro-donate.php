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
	global $wpdb;
	?>
	<div class='wrap'>
		<h2>寄付プロジェクト登録</h2>
		<form id='donate-project-form' method="post" action=''>
			<?php wp_nonce_field( 'donate-nonce-key', 'donate-project' ); ?>
			<div class='form-left'>プロジェクトコード</div>
			<div class='form-right'>
				<input type='text' name='project_code'>
			</div>
			<div class='from-left'>プロジェクト名</div>
			<div class='form-right'>
				<input type='text' name='project_name'>
			</div>
			<div class='form-left'>プロジェクトの説明</div>
			<div class='form-right'>
				<textarea name='description'></textarea>
			</div>
			<div class='from-left'>URL1</div>
			<div class='form-right'>
				<input type='text' name='url1'>
			</div>
			<div class='from-left'>URL2</div>
			<div class='form-right'>
				<input type='text' name='url2'>
			</div>
			<div class='from-left'>サムネイル画像</div>
			<div class='form-right'>
				<input type='text' name='thumbnail'>
			</div>
			<div class='from-left'>プロジェクトロゴ画像</div>
			<div class='form-right'>
				<input type='text' name='logo'>
			</div>
			<input type='submit' text='Save'>
		</form>
	</div>
	<style>
		.projects table, .projects th, .projects td {
			border: 1px solid black;
			border-collapse: collapse;
			border-spacing: 0;
		}
	</style>
	<div class='wrap'>		
		<form id='' action="">
			<table class='projects'>
				<tr>
					<th><br></th>
					<th>no</th>
					<th>プロジェクトコード</th>
					<th>プロジェクト名</th>
					<th>プロジェクトの説明</th>
					<th class='from-left'>URL1</th>
					<th class='from-left'>URL2</th>
					<th class='from-left'>サムネイル画像</th>
					<th class='from-left'>プロジェクトロゴ画像</th>
					<th>登録日</th>
					<th>最終更新日</th>
				</tr>

	<?php
	try {
		$query = "SELECT * FROM  wp_donate_project WHERE del_flag = 0";
		$results = $wpdb->get_results( $wpdb->prepare( $query, 1, $type ) );
	} catch (Exception $e) {
		echo '捕捉した例外: ',  $e->getMessage(), "\n";
	}



	if  (is_null( $results ))  {
		echo "<br>";
	}else{
		foreach($results as $row) {
			$id = $row->id;
			$project_code = $row->project_code;
			$project_name = $row->project_name;
			$description = $row->description;
			$create_date = $row->create_date;
			$update_date = $row->update_date;

			
			echo '<tr>';
			echo '<td>';
			echo '<input type="checkbox" name="del_id_' .  ($id) . '"  id="'  .   ($id) .  '"/>';
			echo '</td>';
			echo '<td>';
			echo ( ++$i );
			echo '</td>';
			echo '<td>';
				echo ( $project_code );
			echo '</td>';
			echo '<td>';
				echo ( $project_name );
			echo '</td>';
			echo '<td>';
				echo ( $description );
			echo '</td>';
			echo '<td>';
				echo ( $url1 );
			echo '</td>';
			echo '<td>';
				echo ( $url2 );
			echo '</td>';
			echo '<td>';
				echo esc_url( $thumbnail );
			echo '</td>';
			echo '<td>';
				echo esc_url( $logo );
			echo '</td>';
			echo '<td>';
				echo esc_url( $create_date );
			echo '</td>';
			echo '<td>';
				echo esc_url( $update_date );
			echo '</td>';
			echo '</tr>';
		}
	}
	echo '</table></form></div>';
	
}

add_action( 'admin_init', 'donate_sumbit' );

/**
 *  Aaaa
 */
function donate_sumbit() {
	// TODO: 入力データの妥当性の判断
	// TODO: DBへの登録.
	global $wpdb;
	if (isset($_POST['project_code']) || isset($_POST['project_name'])){
		$project_code = $wpdb->escape($_POST['project_code']);
		$project_name = $wpdb->escape($_POST['project_name']);
		$description = $wpdb->escape($_POST['description']);
		$url01 = $wpdb->escape($_POST['url01']);
		$url02 = $wpdb->escape($_POST['url02']);
		$thumbnail = $wpdb->escape($_POST['thumbnail']);
		$creator = get_current_user_id();
		$crate_date = current_time('mysql');
		$moderator = get_current_user_id();
		$update_date = current_time('mysql');
		$wpdb->insert( "wp_donate_project",
			array(
				'project_code' => $project_code,
				'project_name' => $project_name,
				'description' => $description,
				'url01' => $url01,
				'url02' => $url02,
				'thumbnail' => $thumbnail,
				'creator' => get_current_user_id(),
				'crate_date' => current_time('mysql'),
				'moderator' => get_current_user_id(),
				'update_date' => current_time('mysql'),
			)
		);
	}
	//donate_menu_index();
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
