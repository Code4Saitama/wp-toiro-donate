<?php
// SQL
function sql_donate_project(){

    $table = "wp_donate_proect";
    $sql_drop  = "DROP TABLE IF EXISTS wp_donate_proect;";
    $sql_create = "
    CREATE TABLE  wp_donate_proect (
        id  bigint  unsigned auto_increment not null primary key,
        project_code varchar(20),
        project_name varchar(200),
        description text,
        del_flag boolean default 0 not null,
        crate_date datetime,
        update_date datetime
    );
    ";

    return array("table" => $table, "drop" => $sql_drop, "create" => $sql_create);
}

function sql_donate_price(){

    $table = "wp_donate_price";
    $sql_drop  = "DROP TABLE IF EXISTS wp_donate_price;";
    $sql_create = "
    CREATE TABLE  wp_donate_price (
        id  bigint  unsigned auto_increment not null primary key,
        donate_project_id  bigint,
        price integer,
        description text,
        del_flag boolean default 0 not null,
        crate_date datetime,
        update_date datetime
    );
    ";

    return array("table" => $table, "drop" => $sql_drop, "create" => $sql_create);
}

function sql_donate_relation(){

    $sql_drop  = "DROP TABLE IF EXISTS wp_donate_relation;";
    $sql_create = "
    CREATE TABLE  wp_donate_relation (
        id  bigint  unsigned auto_increment not null primary key,
        donate_project_id  bigint,
        post_id  bigint,
        del_flag boolean default 0 not null,
        crate_date datetime,
        update_date datetime
    );
    ";

    return array("drop" => $sql_drop, "create" => $sql_create);
}

function sql_donate(){

    $sql_drop  = "DROP TABLE IF EXISTS wp_donate;";
    $sql_create = "
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
    ";

    return array("drop" => $sql_drop, "create" => $sql_create);
}

function sql_donor(){

    $sql_drop  = "DROP TABLE IF EXISTS wp_donor;";
    $sql_create = "
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
    ";

    return array("drop" => $sql_drop, "create" => $sql_create);
}
