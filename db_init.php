<?php

 class DonateTable {
    //プラグインのテーブル名
	var $donate_db_version = '1.0';
	var $table_name;

    public function __construct()
    {
        global $wpdb;
        // 接頭辞（wp_）を付けてテーブル名を設定
        $this->table_name1 = 'wp_donate_project';
        $this->table_name2 = 'wp_donate';
        // プラグイン有効かしたとき実行
        register_activation_hook (__FILE__, array($this, 'donate_activate'));
    }

	function donate_activate() {
		global $wpdb;
		//DBのバージョン
		//現在のDBバージョン取得
		$installed_ver = get_option( 'donate_meta_version' );
		// DBバージョンが違ったら作成
		if( $installed_ver !=  $this->donate_db_version ) {
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

			$sql = "CREATE TABLE " . $this->table_name1 . " (
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
			CHARACTER SET 'utf8';";
			
			dbDelta($sql);
			$sql = "CREATE TABLE " . $this->table_name2 . " (
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
				payment_date datetime,
				del_flag boolean default 0 not null,
				creator varchar(20), 
				create_date datetime  DEFAULT CURRENT_TIMESTAMP,
				moderator varchar(20),
				update_date datetime  DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP   
			);
			CHARACTER SET 'utf8';";
		
			dbDelta($sql);


			//オプションにDBバージョン保存
			update_option('donate_meta_version', $this->donate_db_version);
		}
	}


}
$donate = new DonateTable;