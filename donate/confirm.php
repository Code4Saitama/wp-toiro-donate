<?php
//最終的にはSESSIONでとる予定
//一旦ページ遷移時にPOSTで値を取得する想定
//カード情報は受け取らないので、1ページ目の情報がhiddenで呼ばれる

//date格納用
$timestamp = new Datetime();

/** PAY.JPからのデータをarray に戻す想定 */
$param = array();
$param["donate_project_id"] = empty($_POST["donor_project_id"]) ? '' : $_POST["donor_project_id"];
$param["payment_id"] = empty($_POST["payment_id"]) ? '' : $_POST["payment_id"];
$param["donor_email"] = empty($_POST["donor-email"]) ? 0 : $_POST["donor-email"];
$param["donor_name"] = empty($_POST["donor_name"]) ? 0 : $_POST["donor_name"];
$param["donor_address"] = empty($_POST["donor_address"]) ? 0 : $_POST["donor_address"];
$param["donor_zip"] = empty($_POST["donor_zip"]) ? 0 : $_POST["donor_zip"];
$param["donor_tel"] = empty($_POST["donor_tel"]) ? 0 : $_POST["donor_tel"];
$param["token"] = empty($_POST["payjp-token-id"]) ? 0 : $_POST["payjp-token-id"];
$param["price"] = empty($_POST["price"]) ? 0 : $_POST["price"];
$param["tax"] = empty($_POST["price"]) ? 0 : $_POST["price"]*10/100;
$param["charge"] = empty($_POST["charge"]) ? 0 : $_POST["price"]*$_POST["payjp-charge-fee-rate"]/100;
$param["payment_type_id"] = 1;
$param["payment_date"] = $timestamp->format('Y-m-d H:i:s');
$param["del_flag"] = 0;
$param["creator"] = 0;
$param["create_date"] = $timestamp->format('Y-m-d H:i:s');
$param["moderator"] = 0;
$param["update_date"] = $timestamp->format('Y-m-d H:i:s');

print_r($param);

include('./code_registPayData.php');
$result = regist_pay_data($param);
print_r($result);

/**
 * 
 * {"id":"1",
 * "donate_project_id":"5",
 * "payment_id":"111",
 * "donor_email":"fuga@example.com",
 * "donor_name":"akihiro tamemoto",
 * "donor_zip":"174-0071",
 * "donor_address":"\u6771\u4eac\u90fd\u677f\u6a4b\u533a",
 * "donor_tel":"048-999-9999",
 * "token":"aaaaaaaaaaaaaa",
 * "price":"100000",
 * "tax":"10000",
 * "charge":"500",
 * "payment_type_id":"1",
 * "payment_date":"2021",
 * "del_flag":"0",
 * "creator":"0",
 * "create_date":"2021-11-27 23:17:18",
 * "moderator":"0",
 * "update_date":"2021-11-27 23:30:43"}
 */

/**payment_dateは必須項目。サーバー日付。*/

?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <!-- Bootstrap CSS -->
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css"
      rel="stylesheet"
      integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3"
      crossorigin="anonymous"
    />

    <title>ページ3：寄付結果表示画面</title>
  </head>
  <body>
    <?php if(http_response_code() === 200){/*正常系*/?>
    <div class="container">
      <div class="row">
        <div class="col">
          <h1>ページ3：寄付結果表示画面</h1>
          <div class="alert alert-primary" role="alert">
            ありがとうございました。
          </div>

          <a
            href="/wp-content/plugins/social-project-donation-with-payjp/mock/page1-inputPersonalInfo.php"
            >最初に戻る</a
          >
        </div>
      </div>
    </div>
    <?php }else{/*異常系 決済が失敗した時に戻す*/ ?>
      <div>異常が起きました。</div>
    <?php } ?>

    <!-- Optional JavaScript; choose one of the two! -->

    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script
      src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
      integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p"
      crossorigin="anonymous"
    ></script>

    <!-- Option 2: Separate Popper and Bootstrap JS -->
    <!--
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js" integrity="sha384-7+zCNj/IqJ95wo16oMtfsKbZ9ccEh31eOz1HGyDuCQ6wgnyJNSYdrPa03rtR1zdB" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js" integrity="sha384-QJHtvGhmr9XOIpI6YVutG+2QOK9T+ZnN4kzFN1RtK3zEFEIsxhlmWl5/YESvpZ13" crossorigin="anonymous"></script>
    -->
  </body>
</html>
