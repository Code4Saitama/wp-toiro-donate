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

    <title>ページ1：寄付者情報入力画面</title>
  </head>
  <body>
    <div class="container">
      <div class="row">
        <div class="col">
          <h1>ページ1：寄付者情報入力画面</h1>

          <form
            action="page2-pay.php"
            method="POST"
          >
            <div class="mb-3">
              <label for="exampleInputName" class="form-label">氏名</label>
              <input type="input" class="form-control" name="donor_name" />
            </div>
            <div class="mb-3">
              <label for="exampleInputEmail1" class="form-label">E-mail</label>
              <input
                type="email"
                class="form-control"
                name="donor_email"
              />
            </div>
            <div class="col-md-2">
                <label for="inputZip">Zip</label>
                <input type="text" class="form-control" name="donor_zip" placeholder="郵便番号">
            </div>
            <div class="mb-3">
              <label for="inputAddress">住所</label>
              <input type="text" class="form-control" name="donor_address1" placeholder="都道府県">
              <input type="text" class="form-control" name="donor_address2" placeholder="市区町村以下">
            </div>
            <div class="mb-3">
              <label for="inputTel" class="form-label">電話番号</label>
              <input type="tel" class="form-control" name="donor_tel">
            </div>
            <div class="mb-3">
              <label for="exampleInputProject" class="form-label">プロジェクト名</label>
              <select class="custom-select" name="donate_project_id">
                <option selected>プロジェクト選択</option>
                <option value="1">プロジェクト１</option>
                <option value="2">プロジェクト２</option>
                <option value="3">プロジェクト３</option>
              </select>
            </div>

            <!--
            <div class="alert alert-warning mt-5" role="alert">
              フォームはダミーなので、入力しても結果に反映されません。
            </div>
            -->

            <input type="hidden" name="price" value="10000" />
            <input type="hidden" name="tax_rate" value="10" />
            <input type="hidden" name="subscription" value="0" />

            <button type="submit" class="btn btn-primary">支払情報入力</button>
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
