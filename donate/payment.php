<?php
    //実際には、session でデータを取得する
    print_r($_POST);

    /**array に戻す */
    $param = array();
    $param["donor_email"] = empty($_POST["donor-email"]) ? 0 : $_POST["donor-email"];
    $param["donor_name"] = empty($_POST["donor_name"]) ? 'Nanashi' : $_POST["donor_name"];
    $param["donor_address"] = empty($_POST["donor_address"]) ? 'Nowhere' : $_POST["donor_address"];
    $param["donor_zip"] = empty($_POST["donor_zip"]) ? '000-0000' : $_POST["donor_zip"];
    $param["donor_tel"] = empty($_POST["donor_tel"]) ? '000-0000-0000' : $_POST["donor_tel"];


    // 寄付結果表示ページにリダイレクト
    //header("Location: /wp-content/plugins/social-project-donation-with-payjp/mock/page3-thankyou.php");
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

    <title>ページ2：支払情報入力画面</title>
  </head>
  <body>
    <?php 
    /*
    $param = array(
      "donate_project_id" => "5",
      "payment_id" => 111,
      "donor_email" => "fuga@example.com",
      "donor_name" => "furuta takeshi",
      "donor_zip" => "000-0000",
      "donor_address" => "埼玉県戸田市・・・",
      "donor_tel" => "048-999-9999",
      "token" => "aaaaaaaaaaaaaa",
      "price" => 100000,
      "tax" => 10000,
      "charge" => 500,
      "payment_type_id" => 1,
      "payment_date" => "2021-11-11 11:11:11",
    );
    */

    ?>
    <div class="container">
      <div class="row">
        <div class="col">
          <h1>ページ2：支払情報入力画面</h1>

          <form method="POST" action="page3-thankyou.php">
            <div class="mb-3">
              <label for="exampleInputCardNo" class="form-label">カード</label>
              <input
                type="input"
                class="form-control"
                name="exampleInputCardNo"
              />
            </div>
            <div class="mb-3">
              <label for="exampleInputYukoKigen" class="form-label"
                >有効期限</label>
              <input
                type="input"
                class="form-control"
                name="exampleInputYukoKigen"
              />
            </div>
            
            <input type="hidden" name="price" value="<?php echo $param["price"]; ?>">
            <input type="hidden" name="tax_rate" value="<?php echo $param["tax_rate"]; ?>" />
            <input type="hidden" name="donor_email" value="<?php echo $param["donor_email"]; ?>" />
            <input type="hidden" name="donor_name" value="<?php echo $param["donor_name"]; ?>" />
            <input type="hidden" name="donor_address" value="<?php echo $param["donor_address"]; ?>" />
            <input type="hidden" name="donor_zip" value="<?php echo $param["donor_zip"]; ?>" />
            <input type="hidden" name="donor_tel" value="<?php echo $param["donor_tel"]; ?>" />
          

            <div class="alert alert-warning mt-5" role="alert">
              フォームはダミーなので、入力しても結果に反映されません。
            </div>
            <button type="submit" class="btn btn-success">決済実行</button>
          </form>
        </div>
      </div>
    </div>

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
