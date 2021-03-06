<?php
add_action( 'rest_api_init', 'add_custom_endpoint' );

function add_custom_endpoint() {
	$api_version = "1";
	register_rest_route( 
		'donate/v' . $api_version, '/projects', 
		array(
			'methods' => 'GET',
			'callback' => 'get_projects'
		)
	);
	register_rest_route( 
		'donate/v' . $api_version, '/projects', 
		array(
			'methods' => 'POST',
			'callback' => 'post_projects'
		)
	);
	register_rest_route( 
		'donate/v' . $api_version, '/projects/(?P<id>\d+)', 
		array(
			'methods' => 'GET',
			'callback' => 'get_projects'
		)
	);

	register_rest_route( 
		'donate/v' . $api_version, '/projects/(?P<id>\d+)', 
		array(
			'methods' => 'POST',
			'callback' => 'post_projects'
		)
	);
	
	register_rest_route( 
		'donate/v' . $api_version, '/donates', 
		array(
			'methods' => 'GET',
			'callback' => 'get_donates'
		)
	);

	register_rest_route( 
		'donate/v' . $api_version, '/donates', 
		array(
			'methods' => 'POST',
			'callback' => 'post_donates'
		)
	);
	
	register_rest_route( 
		'donate/v' . $api_version, '/donates/(?P<id>\d+)', 
		array(
			'methods' => 'GET',
			'callback' => 'get_donates'
		)
	);

	register_rest_route( 
		'donate/v' . $api_version, '/donates/(?P<id>\d+)', 
		array(
			'methods' => 'POST',
			'callback' => 'post_donates'
		)
	);

}

function get_apikey(){
	$api_key = get_option("donate_apikey", "");
	return $api_key;
}

function header_check($data){
	$header = $data->get_headers();
	$api_key = get_apikey();
	
	if (isset($header["x_api_key"])){
		$post_api_key = $header["x_api_key"][0];
		if ($post_api_key == $api_key){
			return array(
				"result" => true,
				"message" => ""
			);
		}else{
			return array(
				"result" => false,
				"message" => "Wrong API Key."
			);
		}
	}else{
		return array(
			"result" => false,
			"message" => "No API Header."
		);
	}
}


function get_projects($data){
	global $wpdb;

	$header_check = header_check($data);
	if (!$header_check["result"]){
		$results = array(
			"message" => $header_check["message"]
		);
		$response = new WP_REST_Response($results);
		$response->set_status(400);
		$domain = (empty($_SERVER["HTTPS"]) ? "http://" : "https://") . $_SERVER["HTTP_HOST"];
		$response->header( 'Location', $domain );
		return $response;
	}
	

	if (isset($data["id"])){
		$query = "SELECT * FROM  wp_donate_project WHERE del_flag = 0 and id=%d";
		$results = $wpdb->get_results( $wpdb->prepare( $query, $data["id"]) );
	}else{
		$query = "SELECT * FROM  wp_donate_project WHERE del_flag = 0";
		$results = $wpdb->get_results( $query );
	}
	
	$response = new WP_REST_Response($results);
	$response->set_status(200);
	$domain = (empty($_SERVER["HTTPS"]) ? "http://" : "https://") . $_SERVER["HTTP_HOST"];
	$response->header( 'Location', $domain );
	return $response;
}

function post_projects($data){
	$header = $data->get_headers();
	$json = $data->get_params();

	$header_check = header_check($data);
	if (!$header_check["result"]){
		$results = array(
			"message" => $header_check["message"]
		);
		$response = new WP_REST_Response($results);
		$response->set_status(400);
		$domain = (empty($_SERVER["HTTPS"]) ? "http://" : "https://") . $_SERVER["HTTP_HOST"];
		$response->header( 'Location', $domain );
		return $response;
	}

	$results = array();

	global $wpdb;

	if (isset($data["id"])){
		//??????
		$id = $data["id"];
		$updates = array();
		if (is_array($json[0])){
			$json = $json[0];
		}

		if (isset($json["project_code"]) && !empty($json["project_code"])){
			$updates["project_code"] = $json["project_code"];
		}
		if (isset($json["project_name"]) && !empty($json["project_name"])){
			$updates["project_name"] = $json["project_name"];
		}
		if (isset($json["description"]) && !empty($json["description"])){
			$updates["description"] = $json["description"];
		}
		if (isset($json["url01"]) && !empty($json["url01"])){
			$updates["url01"] = $json["url01"];
		}
		if (isset($json["url02"]) && !empty($json["url02"])){
			$updates["url02"] = $json["url02"];
		}
		$update['moderator'] = get_current_user_id();
		$updates['update_date'] = current_time('mysql');

		$wpdb->update( 
			"wp_donate_project",
			$updates,
			array( 'ID' =>  $id ), 
		);
		$lastid = $id;
		$query = "SELECT * FROM  wp_donate_project WHERE del_flag = 0 and id=%d";
		$res = $wpdb->get_results( $wpdb->prepare( $query, $lastid) );
		array_push($results, $res[0]);
	}else{
		//??????
		if (is_array($json[0])){
			foreach ($json as $k => $v){

				$project_code = isset($v["project_code"]) ? $v["project_code"] : "";
				$project_name = isset($v["project_name"]) ? $v["project_name"] : "";
				$description = isset($v["description"]) ? $v["description"] : "";
				$url01 = isset($v["url01"]) ? $v["url01"] : "";
				$url02 = isset($v["url02"]) ? $v["url02"] : "";
		

				if (empty($project_code) || empty($project_name)){
					//DB??????????????????
				}else{
					$wpdb->insert( 
						"wp_donate_project",
						array(
							'project_code' => $project_code,
							'project_name' => $project_name,
							'description' => $description,
							'url01' => $url01,
							'url02' => $url02,
							'creator' => get_current_user_id(),
							'create_date' => current_time('mysql'),
							'moderator' => get_current_user_id(),
							'update_date' => current_time('mysql'),
						)
					);
					$lastid = $wpdb->insert_id;
					$query = "SELECT * FROM  wp_donate_project WHERE del_flag = 0 and id=%d";
					$res = $wpdb->get_results( $wpdb->prepare( $query, $lastid) );
				
					array_push($results, $res[0]);
				}
			}
			
		}else{
			$project_code = isset($json["project_code"]) ? $json["project_code"] : "";
			$project_name = isset($json["project_name"]) ? $json["project_name"] : "";
			$description = isset($json["description"]) ? $json["description"] : "";
			$url01 = isset($json["url01"]) ? $json["url01"] : "";
			$url02 = isset($json["url02"]) ? $json["url02"] : "";

			if (empty($project_code) || empty($project_name)){
				//DB??????????????????
			}else{
				$wpdb->insert( 
					"wp_donate_project",
					array(
						'project_code' => $project_code,
						'project_name' => $project_name,
						'description' => $description,
						'url01' => $url01,
						'url02' => $url02,
						'creator' => get_current_user_id(),
						'create_date' => current_time('mysql'),
						'moderator' => get_current_user_id(),
						'update_date' => current_time('mysql'),
					)
				);
				$lastid = $wpdb->insert_id;
				$query = "SELECT * FROM  wp_donate_project WHERE del_flag = 0 and id=%d";
				$res = $wpdb->get_results( $wpdb->prepare( $query, $lastid) );

				array_push($results, $res[0]);				
			}
		}
	}
	$response = new WP_REST_Response($results);
	$response->set_status(200);
	$domain = (empty($_SERVER["HTTPS"]) ? "http://" : "https://") . $_SERVER["HTTP_HOST"];
	$response->header( 'Location', $domain );
	return $response;
}

function put_projects($data){

}

function get_donate_Fields($table){
	global $wpdb;
	
	$sql = "select COLUMN_NAME from information_schema.COLUMNS where table_name = '$table' and COLUMN_NAME not in ( 'id', 'del_flag')";
	$rows = $wpdb->get_results( $sql );
	$fields = array(); 
	foreach ($rows as $row) {
		array_push($fields, $row->COLUMN_NAME);
	}
	return $fields;
}

function get_donates(){
	global $wpdb;
	
	$fields = get_donate_Fields("wp_donate");


	$query = "SELECT * FROM  wp_donate WHERE del_flag = 0";
	$results = $wpdb->get_results( $query );
	$response = new WP_REST_Response($results);
	$response->set_status(200);
	$domain = (empty($_SERVER["HTTPS"]) ? "http://" : "https://") . $_SERVER["HTTP_HOST"];
	$response->header( 'Location', $domain );
	return $response;
}


function post_donates($data){
	$table_name = "wp_donate";

	$header_check = header_check($data);
	if (!$header_check["result"]){
		$results = array(
			"message" => $header_check["message"]
		);
		$response = new WP_REST_Response($results);
		$response->set_status(400);
		$domain = (empty($_SERVER["HTTPS"]) ? "http://" : "https://") . $_SERVER["HTTP_HOST"];
		$response->header( 'Location', $domain );
		return $response;
	}

	$header = $data->get_headers();
	$json = $data->get_params();
	$fields = get_donate_Fields($table_name);

	$results = array();

	global $wpdb;

	if (isset($data["id"])){
		//??????
		$id = $data["id"];
		$updates = array();
		if (is_array($json[0])){
			$json = $json[0];
		}

		foreach ($fields as $f){
			if (isset($json[$f]) && !empty($json[$f])){
				$updates[$f] = $json[$f];
			}
		}
		$update['moderator'] = get_current_user_id();
		$updates['update_date'] = current_time('mysql');

		$wpdb->update( 
			"wp_donate",
			$updates,
			array( 'ID' =>  $id ), 
		);
		$lastid = $id;
		$query = "SELECT * FROM  wp_donate WHERE del_flag = 0 and id=%d";
		$res = $wpdb->get_results( $wpdb->prepare( $query, $lastid) );
		array_push($results, $res[0]);
	}else{
		//??????
		if (is_array($json[0])){
			foreach ($json as $k => $v){
				$inserts = array();
				foreach ($fields as $f){
					if (isset($v[$f]) && !empty($v[$f])){
						$inserts[$f] = $v[$f];
					}else{
						$inserts[$f] = null;
					}
				}
				$inserts['creator'] = get_current_user_id();
				$inserts['create_date'] = current_time('mysql');
				$inserts['moderator'] = get_current_user_id();
				$inserts['update_date'] = current_time('mysql');
		
				if (empty($inserts["project_id"])){
					//DB??????????????????
				}else{
					$wpdb->insert( 
						"wp_donate_project",
						$inserts
					);
					$lastid = $wpdb->insert_id;
					$query = "SELECT * FROM  wp_donate WHERE del_flag = 0 and id=%d";
					$res = $wpdb->get_results( $wpdb->prepare( $query, $lastid) );
				
					array_push($results, $res[0]);
				}
			}
			
		}else{
			$inserts = array(); 
			foreach ($fields as $f){
				
				if (isset($json[$f]) && !empty($json[$f])){
					$inserts[$f] = $json[$f];
				}else{
					$inserts[$f] = null;
				}
			}

			$inserts['creator'] = get_current_user_id();
			$inserts['create_date'] = current_time('mysql');
			$inserts['moderator'] = get_current_user_id();
			$inserts['update_date'] = current_time('mysql');

			if (empty($inserts["donate_project_id"])){
				//DB??????????????????
			}else{
				$wpdb->insert( 
					$table_name,
					$inserts
				);
				$lastid = $wpdb->insert_id;
				$query = "SELECT * FROM  $table_name WHERE del_flag = 0 and id=%d";
				$res = $wpdb->get_results( $wpdb->prepare( $query, $lastid) );
			
				array_push($results, $res[0]);
			}
		}
	}
	$response = new WP_REST_Response($results);
	$response->set_status(200);
	$domain = (empty($_SERVER["HTTPS"]) ? "http://" : "https://") . $_SERVER["HTTP_HOST"];
	$response->header( 'Location', $domain );
	return $response;
}

