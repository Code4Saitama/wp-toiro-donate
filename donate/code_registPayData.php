<?php

function regist_pay_data($param)
{
    require_once('../vendor/autoload.php');
    /**
 * こちらに寄付データ登録処理を実装して頂きたいです
 * 引数の個数、形式（連想配列、キーと値。詳細はpage2-pay.php参照）は固定の想定です。
 * 引数名、記載場所は変更してもらって大丈夫です。
 *
 */

    //$api_instance = new Swagger\Client\ApiDonatesApi();
    $url = ($_SERVER['HTTPS'] == on ? "https://"  : "http://" ).$_SERVER["HOST"] .'/wp-json/v1/donate/donates';
    $ch = curl_init( $url );

    //$api_key = get_option("donate_apikey", "");

    # Setup request to send json via POST.
    $payload  = json_encode($param, JSON_UNESCAPED_UNICODE);

    echo($url);
    echo($payload);
    
    try {
        //$result = $api_instance->donatesPost($body);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload );
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json',"X-API-KEY:$api_key"));
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        # Return response instead of printing.
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        # Send request.
        $result = curl_exec($ch);
        return $result;


    } catch (Exception $e) {

        trigger_error(sprintf(
            'Curl failed with error #%d: %s',
            $e->getCode(), $e->getMessage()),
            E_USER_ERROR);

    } finally {
    // Close curl handle unless it failed to initialize
        if (is_resource($ch)) {
            curl_close($ch);
        }
        # Print response.
        var_dump($result);
    }
 
    

}


?>