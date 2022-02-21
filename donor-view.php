<?php
/**
 * 寄付画面
 */

function donor_simplepayjppayment_handle_form_submitted() {
  if ( !isset( $_POST['payjp-token'] ) ) {
      return array( false, esc_html__( 'token is empty', 'simple-payjp-payment' ) );
  }

  error_log( "simplepayjppayment_handle_form_submitted実行中\n", 3, "/Applications/MAMP/htdocs/wp-content/plugins/wp-toiro-donate/error_log");

  $safe_amount = 0;
  $safe_plan_id = '';
  $safe_prorate = false;
  if ( !isset( $_POST['amount'] ) ) {
      if ( !isset( $_POST['plan-id'] ) ) {
          return array( false, esc_html__( 'amount or plan id is empty', 'simple-payjp-payment' ) );
      } else {
          $safe_plan_id = sanitize_text_field( $_POST['plan-id'] );
          $prorate = sanitize_text_field( $_POST['prorate'] );
          $safe_prorate = $prorate === 'yes' ? true : false;
      }
  } else {
      $safe_amount = intval( $_POST['amount'] );
  }

  if ( !isset( $_POST['form-id'] ) ) {
      return array( false, esc_html__( 'form id is empty', 'simple-payjp-payment' ) );
  }

  $err = '';
  $safe_token = sanitize_text_field ( $_POST['payjp-token'] );
  $safe_form_id = sanitize_text_field( $_POST['form-id'] );
  $currency = 'jpy';

  $secret_key = simplepayjppayment_get_secret_key();
  if ( $secret_key === '' ) {
      return array( false, esc_html__( 'Invalid key', 'simple-payjp-payment' ) );
  }

  $safe_mail = '';
  $safe_name = '';
  $safe_desc = "form-id:" . $safe_form_id;
  if ( isset( $_POST['user_mail'] ) ) {
      $safe_mail = sanitize_text_field( $_POST['user_mail'] );
      $safe_desc .=  ", mail:" . $safe_mail;
  }
  if ( isset( $_POST['user_name'] ) ) {
      $safe_name = sanitize_text_field( $_POST['user_name'] );
      $safe_desc .=  ", name:" . $safe_name;
  }

  $_SESSION["donor-project-id"] = $_POST["donor-project-id"];
  $_SESSION["donor-mail"] = $_POST["user_mail"];
  $_SESSION["donor-name"] = $_POST["user_name"];
  $_SESSION["donor-zipcode"] = $_POST["user_zipcode"];
  $_SESSION["donor-address"] = $_POST["user_address"];
  $_SESSION["donor-tel"] = $_POST["user_tel"];

  $customer_id = '';
  if ( $safe_amount > 0 ) {
      list($result, $charge_result) = simplepayjppayment_create_single_payment( $secret_key, $safe_token, $safe_amount, $currency, $safe_desc );
  } else {
      $result = simplepayjppayment_create_subscription_payment( $secret_key, $safe_token, $safe_plan_id, $safe_mail, $safe_desc, $safe_prorate );
  }
  if ( $result ) {
      return array( true, esc_html__( 'Payment completed', 'simple-payjp-payment' ), $charge_result );
  } else {
      return array( false, esc_html__( 'Payment failed', 'simple-payjp-payment' ) );
  }

  return array( false, esc_html__( 'Payment failed', 'simple-payjp-payment' ) );
}

/**
 * Pay.jpを使って1回支払う
 */
function donor_simplepayjppayment_create_payment($secret_key, $token, $amount, $currency, $description)
{
  error_log( "simplepayjppayment_create_payment実行中\n", 3, "/Applications/MAMP/htdocs/wp-content/plugins/wp-toiro-donate/error_log");
    try {
        Payjp\Payjp::setApiKey($secret_key);
        $result = Payjp\Charge::create(array(
              "card" => $token,
              "amount" => $amount,
              "currency" => $currency,
              "description" => $description,
      ));
        if (isset($result['error'])) {
            throw new Exception();
        }
    } catch (Exception $e) {
        return array(false, array());
    }

    $log = "charge result:\n";
    ob_start();
    var_dump($result->card);
    var_dump($result->id);
    $log .= ob_get_contents();
    ob_end_clean();
    debug_log($log);
    return array(true, array(
      "charge_id" => $result->id,
      "amount" => $result->amount,
      "fee_rate" => floatval($result->fee_rate),
      "token" => $token,
    ));
}

/**
 *  寄付画面表示
 */
function donor_simplepayjppayment_handler($atts)
{
  error_log( "simplepayjppayment_handler実行中\n", 3, "/Applications/MAMP/htdocs/wp-content/plugins/wp-toiro-donate/error_log");

    simplepayjppayment_security_migration();

    $a = shortcode_atts(array(
      'amount'  => 0,
      'form-id'  => "",
      'name' => 'yes',
      'result-ok' => "",
      'result-ng' => "",
      'plan-id' => "",
      'prorate' => 'no',
      'project-id' => 0,
  ), $atts);

    $safe_amount = intval($a['amount']);
    $safe_plan_id = sanitize_text_field($a['plan-id']);
    if ($safe_amount == 0) {
        if ($safe_plan_id === "") {
            return esc_html__('Set amount value or plan-id', 'simple-payjp-payment');
        }
    } else {
        if ($safe_plan_id != "") {
            return esc_html__('amount and plan-id should be exclusive', 'simple-payjp-payment');
        }
        if (($safe_amount < 50) || (3000000 < $safe_amount)) {
            return esc_html__('Invalid amount value', 'simple-payjp-payment');
        }
    }

    $safe_form_id = sanitize_text_field($a['form-id']);
    if ($safe_form_id === "") {
        return esc_html__('Invalid form-id test', 'simple-payjp-payment');
    }

    $name_enabled = $a['name'] === 'yes' ? true : false;

    $public_key = simplepayjppayment_get_public_key();
    if ($public_key == "") {
        return esc_html__('Invalid key', 'simple-payjp-payment');
    }

    $safe_result_ok_page = sanitize_text_field($a['result-ok']);
    $safe_result_ng_page = sanitize_text_field($a['result-ng']);

    $prorate_enabled = $a['prorate'] === 'yes' ? 'yes' : 'no';

    if (!isset($_SESSION["key"])) {
      $_SESSION["key"] = md5(uniqid().mt_rand());
    }

    /* ----------------------------------------------
     * プロジェクト情報取得
     * ---------------------------------------------- */
    if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
      $api_url = "https://";
    } else {
      $api_url = "http://";
    }
    $api_url .= $_SERVER['HTTP_HOST'] . "/wp-json/donate/v1/projects";
    $safe_project_id = intval($a["project-id"]);
    if ($safe_project_id != 0) {
      $api_url .= $api_base_url . "/" . $safe_project_id;
    }
    $curl=curl_init();
    curl_setopt($curl, CURLOPT_URL, $api_url);
    $api_key = get_option("donate_apikey", "");
    curl_setopt($curl, CURLOPT_HTTPHEADER, array("X-API-KEY: $api_key", 'Content-Type: application/json'));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLINFO_HEADER_OUT, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    // API叩く
    $api_response = curl_exec($curl);
    $errno = curl_errno($curl);
    $curl_result = curl_getinfo($curl);
    $api_response_json = json_decode($api_response, true);

    $api_has_error = false;
    if ($errno != 'CURL_OK' || $curl_result["http_code"] != 200 || $api_response_json == "") {
      $api_has_error = true;
    }

    curl_close($curl); 
    // 実行結果取得
    $projects = array();
    foreach ($api_response_json as $project) {
      array_push($projects, array(
        "id" => $project["id"],
        "name" => $project["project_name"],
      ));
    }
    if ($safe_project_id != 0 && count($projects) == 0) {
      // 指定したプロジェクトIDが存在しない場合
      return "存在しないプロジェクトIDです。";
    }
    if ($api_has_error) {
      return "エラーが発生しました。";
    }
    

    // test
    // return "テスト。$domain";
    //error_log("エラー！", 3, ”/Applications/MAMP/htdocs/wp-content/plugins/wp-toiro-donate/error_log”)；

    ?>

  <?php ob_start(); ?>
  <div class="simplepayjppayment-container">
      <form action="<?php the_permalink(); ?>" method="post">
          <input type="hidden" name="key" value="<?php echo htmlspecialchars($_SESSION["key"], ENT_QUOTES); ?>">
          <label for="simplepayjppayment-mail">E-mail:</label>
          <input type="text" id="simplepayjppayment-mail" name="user_mail" inputmode="email" required>
          <span aria-live="polite" role="status">
          <span class="errorMessage messageBox empty" aria-hidden="true">必須入力です。</span>
            <span class="errorMessage messageBox pattern" aria-hidden="true">
                正しい形式で入力してください。
            </span>
          </span>
          <?php if ($name_enabled) { ?>
              <br /><label for="simplepayjppayment-mail"><?php esc_html_e('Name', 'simple-payjp-payment'); ?>:</label>
              <input type="text" id="simplepayjppayment-name" name="user_name" required>
              <span aria-live="polite" role="status">
                <span class="errorMessage messageBox" aria-hidden="true">必須入力です。</span>
              </span>
          <?php } ?>
          <br /><label for="simplepayjppayment-address">郵便番号:</label>
              <input type="text" id="simplepayjppayment-zipcode" name="user_zipcode" pattern="[0-9]{3}-[0-9]{4}" inputmode="numeric" required>
              <span aria-live="polite" role="status">
                <span class="errorMessage messageBox" aria-hidden="true">必須入力です。</span>
                <span class="errorMessage messageBox pattern" aria-hidden="true">正しい形式で入力してください。（999-9999）</span>
              </span>
              <br /><label for="simplepayjppayment-address">住所:</label>
              <input type="text" id="simplepayjppayment-address" name="user_address" required>
              <span aria-live="polite" role="status">
                <span class="errorMessage messageBox" aria-hidden="true">必須入力です。</span>
              </span>
              <br /><label for="simplepayjppayment-address">電話番号:</label>
              <input type="text" id="simplepayjppayment-tel" name="user_tel" inputmode="numeric" pattern="0[0-9]{1,4}-[0-9]{1,4}-[0-9]{1,4}" required>
              <span aria-live="polite" role="status">
                <span class="errorMessage messageBox empty" aria-hidden="true">必須入力です。</span>
                <span class="errorMessage messageBox pattern" aria-hidden="true">正しい形式で入力してください。（09999-9999-9999）</span>
              </span>
              <br /><label for="simplepayjppayment-project">プロジェクト:</label>
          <?php if ($safe_project_id == 0) { ?>
          <select id="simplepayjppayment-project" name="donor-project-id">
            <?php foreach ($projects as $project) { ?>
              <option value="<?php echo esc_attr($project["id"]); ?>"><?php echo esc_attr($project["name"]); ?></option>
            <?php } ?>
          </select>
          <?php 
        } else { ?>
            <?php echo esc_attr($projects[0]["name"]); ?>
            <input name="donor-project-id" value="<?php echo esc_attr($projects[0]["name"]); ?>" type="hidden">
          <?php 
        } ?>
          <script src="https://checkout.pay.jp/" class="payjp-button" data-key="<?php
          echo esc_attr($public_key); ?>"></script>
          <?php if ($safe_amount != 0) { ?>
              <input name="amount" value="<?php echo esc_attr($safe_amount); ?>" type="hidden">
          <?php } else { ?>
              <input name="plan-id" value="<?php echo esc_attr($safe_plan_id); ?>" type="hidden">
          <?php } ?>
          <input name="form-id" value="<?php echo esc_attr($safe_form_id); ?>" type="hidden">
          <?php if ($safe_result_ok_page) { ?>
              <input name="result-ok" value="<?php echo esc_attr($safe_result_ok_page); ?>" type="hidden">
          <?php } ?>
          <?php if ($safe_result_ng_page) { ?>
          <input name="result-ng" value="<?php echo esc_attr($safe_result_ng_page); ?>" type="hidden">
          <?php } ?>
          <input name="prorate" value="<?php echo esc_attr($prorate_enabled); ?>" type="hidden">
      </form>
  </div>
  <?php return ob_get_clean();
}

wp_enqueue_script("simple-payjp-payment-javascript", plugins_url( 'js/simple-payjp-payment.js', __FILE__ ), array("jquery", "jquery-core"), false, true);
#wp_enqueue_script("db-post-javascript", plugins_url( 'js/db-post.js', __FILE__ ), array("jquery", "jquery-core"), false, true);

// shortcode

function donor_simplepayjppayment_redirect()
{
  error_log( "redirect実行中\n", 3, "/Applications/MAMP/htdocs/wp-content/plugins/wp-toiro-donate/error_log");
    if (! empty($_POST) && ! empty($_POST[ 'form-id' ])) {
        session_start();
        error_log( "redirect sessionstart\n", 3, "/Applications/MAMP/htdocs/wp-content/plugins/wp-toiro-donate/error_log");
        if (isset($_SESSION["key"], $_POST["key"]) && $_SESSION["key"] == $_POST["key"]) {
            unset($_SESSION["key"]);
            list($result, $message, $pay_result) = donor_simplepayjppayment_handle_form_submitted();
            if ($result) {
              $param = array();
              $timestamp = new DateTime('now', new DateTimeZone('Asia/Tokyo'));
              $param["donate_project_id"] = intval($_SESSION["donor-project-id"]);
              $param["payment_id"] = $pay_result["charge_id"];
              $param["donor_email"] = $_SESSION["donor-mail"];
              $param["donor_name"] = $_SESSION["donor-name"];
              $param["donor_address"] = $_SESSION["donor-address"];
              $param["donor_zip"] = $_SESSION["donor-zipcode"];
              $param["donor_tel"] = $_SESSION["donor-tel"];
              $param["token"] = $pay_result["token"];
              $param["price"] = $pay_result["amount"];
              $param["tax"] = 0;
              $param["charge"] = $pay_result["fee_rate"];
              $param["payment_type_id"] = 1;
              $param["payment_date"] = $timestamp->format('Y-m-d H:i:s');
              $param["del_flag"] = 0;
              $param["creator"] = 0;
              $param["create_date"] = $timestamp->format('Y-m-d H:i:s');
              $param["moderator"] = 0;
              $param["update_date"] = $timestamp->format('Y-m-d H:i:s');
                do_action('simplepayjppayment_result_ok', $param);
                if (! empty($_POST[ 'result-ok' ])) {
                    wp_safe_redirect($_POST[ 'result-ok' ], 302);
                    exit();
                }

                ob_start();
                var_dump($param);
                $param_dump = ob_get_clean();
                echo "<pre>$param_dump</pre>";
                echo "支払いが完了しました。toiro-donate";
            } else {
                do_action('simplepayjppayment_result_ng');
                if (! empty($_POST[ 'result-ng' ])) {
                    wp_safe_redirect($_POST[ 'result-ng' ], 302);
                    exit();
                }
            }

            echo($message);
            exit();
        } else {
            wp_safe_redirect(get_permalink(), 302);
            exit();
        }
    } else {
        session_start();
        add_shortcode('donor_simple-payjp-payment', 'donor_simplepayjppayment_handler');
    }
}

add_action('template_redirect', 'donor_simplepayjppayment_redirect', 10000);

function debug_log($str) {
  $log = "[" . date("Y/M/d H:m:i") . "]";
  $log .= $str;
  $log .= "\n\n";
  file_put_contents("/var/www/wordpress/wp-content/plugins/simple-pay-jp-payment/debug.log",
  $log, FILE_APPEND);
}