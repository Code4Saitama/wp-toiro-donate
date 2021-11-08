/*
1	id 	プロジェクトID	number <<generated>>	主キー(自動採番）																					
2	project_code	プロジェクトコード	varchar(20)		任意の管理コード																				
3	project_name	プロジェクト名	varchar(200)				音羽追記																		
4	description	説明文	text		プロジェクトの説明文																				
5	url01	URL1	varchar(200)																						
6	url02	URL2	varchar(200)																						
7	thumbnail	サムネイル画像	varchar(200)		画像のパス名を記載	シェアした時に使うメイン画像																			
8	logo	プロジェクトロゴ画像	varchar(200)		画像のパス名を記載	プロジェクトロゴ画像																			
9	del_flag	削除フラグ	boolean		true: 削除済み																				
10	creator	作成者	varchar(20)			ワードプレスのユーザID																			
11	crate_date	作成日	datetime		作成日	データ生成時の現在時刻																			
12	moderator	更新者	varchar(20)			ワードプレスのユーザID																			
13	update_date	更新日	datetime		更新日	データ更新時の現在時刻（トリガー）																			
*/						
DROP TABLE IF EXISTS wp_donate_proect;

CREATE TABLE  wp_donate_proect (
    id  bigint  unsigned auto_increment not null primary key,  
    project_code varchar(20),
    project_name varchar(200),
    description text,
    url01 varchar(200),
    url02 varchar(200),
    thumbnail varchar(200),
    logo varchar(200),
    del_flag boolean
    creator varchar(20), 
    crate_date datetime  DEFAULT CURRENT_TIMESTAMP,
    moderator varchar(20),
    update_date datetime  DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

/*
テーブル名	wp_donate_price	マスターテーブル	プロジェクト金額テーブル			
no	field name	名称	データ型	キー	説明	備考
1	id	主キー	number <<generated>>	主キー		
2	donate_project_id	プロジェクトID	number <<FK>>	外部キー		
3	price	金額	number			
4	description	説明文	text			
5	del_flag	削除フラグ	boolean		true: 削除済み	
6	crate_date	作成日	datetime		作成日	データ生成時の現在時刻
7	update_date	更新日	datetime		更新日	データ更新時の現在時刻（トリガー）
*/

DROP TABLE IF EXISTS wp_donate_price;

CREATE TABLE  wp_donate_price (
    id  bigint  unsigned auto_increment not null primary key,
    donate_project_id  bigint,
    price integer,
    description text,
    del_flag boolean default 0 not null,
    crate_date datetime,
    update_date datetime
);

/*
テーブル名	wp_donate_relation	マスターテーブル	記事とプロジェクトの関連表			
no	field name	名称	データ型	キー	説明	備考
1	id	リレーションID	number <<generated>>	主キー		
2	donate_project_id	プロジェクトID	number <<FK>>	外部キー		
3	post_id	記事ID	number <<FK>>	外部キー		
4	del_flag	削除フラグ	boolean		true: 削除済み	
5	crate_date	作成日	datetime		作成日	データ生成時の現在時刻
6	update_date	更新日	datetime		更新日	データ更新時の現在時刻（トリガー）
*/

DROP TABLE IF EXISTS wp_donate_relation;

CREATE TABLE  wp_donate_relation (
    id  bigint  unsigned auto_increment not null primary key,
    donate_project_id  bigint,
    post_id  bigint,
    del_flag boolean default 0 not null,
    crate_date datetime,
    update_date datetime
);


/*						
テーブル名	wp_donate	トランザクションテーブル	寄付テーブル			
no	field name	名称	データ型	キー	説明	備考
1	id	寄付ID	number <<generated>>	主キー		
2	donate_project_id	プロジェクトID	number <<FK>>	外部キー		
3	payment_id	決済ID	tinteger<<FK>>	外部キー		
4	donor_id 寄付者ID					
5	price	金額	number			
6	tax	税	number			
7	charge	手数料	number			
8	payment_date	決済日	datetime			
9	del_flag	削除フラグ	boolean		true: 削除済み	
10	crate_date	作成日	datetime		作成日	データ生成時の現在時刻
11	update_date	更新日	datetime		更新日	データ更新時の現在時刻（トリガー）
*/
DROP TABLE IF EXISTS wp_donate;

CREATE TABLE wp_donate (
    id bigint  unsigned auto_increment not null primary key,
    donate_project_id  bigint,
    payment_id  bigint,
    donor_id  bigint,
    price integer,
    tax integer,
    charge integer,
    payment_date datetime,
    del_flag boolean default 0 not null,
    crate_date datetime,
    update_date datetime
);

/*
テーブル名	wp_donor		寄付者テーブル			
no	field name	名称	データ型	キー	説明	備考
1	id	寄付者ID	number <<generated>>	主キー		
2	donor_name	寄付者氏名	text			
3	donor_zip	寄付者郵便番号	text			
4	donor_address	寄付者住所	text			
5	donor_tel	寄付者電話番号	text			
6	donor_email	寄付者メールアドレス	text			
7	del_flag	削除フラグ	boolean		true: 削除済み	
8	crate_date	作成日	datetime		作成日	データ生成時の現在時刻
9	update_date	更新日	datetime		更新日	データ更新時の現在時刻（トリガー）
*/

DROP TABLE IF EXISTS wp_donor;

CREATE TABLE wp_donor (
    id bigint  unsigned auto_increment not null primary key,
    donor_name varchar(20),
    donor_zip varchar(20),
    donor_address varchar(200),
    donor_tel varchar(20),
    donor_email varchar(100),
    del_flag boolean default 0 not null,
    crate_date datetime,
    update_date datetime
);
