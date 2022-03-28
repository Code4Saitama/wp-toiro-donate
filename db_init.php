<?php

class DonateTable {
		//プラグインのテーブル名
	var $donate_db_version = '1.1';
	var $table_name;

	public function __construct()
	{
		global $wpdb;
		// 接頭辞（wp_）を付けてテーブル名を設定
		$this->table_name1 = 'wp_donate_project';
		$this->table_name2 = 'wp_donate';
		$this->table_name3 = 'wp_payment_type';
		// プラグイン有効かしたとき実行
		register_activation_hook(__FILE__, array($this, 'donate_activate'));
		register_deactivation_hook( __FILE__, array($this, 'donate_deactivate'));
	}

	function donate_activate() {
		global $wpdb;
		//DBのバージョン
		//現在のDBバージョン取得
		$installed_ver = get_option( 'donate_meta_version' );
		// DBバージョンが違ったら作成
		if( $installed_ver !=  $this->donate_db_version ) {
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
			update_option('donate_meta_version', $this->donate_db_version);

			$queries = explode(";", $sql);
			foreach ($queries as $qry){
				if (!empty($qry){
					$wpdb->query($qry);
				}
			}
		}
	}

	function donate_deactivate() {
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
			if (!empty($qry){
				$wpdb->query($qry);
			}
		}

	}

}
$donate = new DonateTable;