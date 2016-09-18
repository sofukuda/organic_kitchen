<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="utf-8">
    <link rel="shortcut icon" href="images/favicon.ico">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>オーガニックキッチン発注画面</title>
    <!-- BootstrapのCSS読み込み -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- jQuery読み込み -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <!-- BootstrapのJS読み込み -->
    <script src="js/bootstrap.min.js"></script>
  </head>

  <body>
    <div class="text-center">
      <img  src="images/logo.png" alt="" width="200" height="100"></div>

          <div class="container">
          <section id="fh5co-newsletter">
            <div class="container">
              <div class="row">
                <div class="col-md-4 col-md-offset-4 col-sm-6 col-sm-offset-3 text-center">
                  <h2 class="fh5co-uppercase-heading-sm fh5co-no-margin-bottom">ログイン画面</h2>
                  <?php
                    if ($_GET["ecode"] == 205){
                        echo '<h1><span class="label label-success btn-lg btn-block"/>ログアウトが完了しました</span></h1>';
                    }
                    else if ($_GET["ecode"] == 400){
                        echo '<h1><span class="label label-warning btn-lg btn-block"/>ログインしてください</span></h2>';
                    }
                    else if ($_GET["ecode"] == 401){
                        echo '<h1><span class="label label-danger btn-lg btn-block"/>ユーザ名かパスワードが間違っています</span></h2>';
                    }
                  ?>
                  <br>
                  <p>ユーザ名とパスワードを入力してください</p>
                  <div class="fh5co-spacer fh5co-spacer-xxs"></div>
                  <form method="post" action="login_done.php">
                    <div class="form-group">
                      <label for="client_name" class="sr-only">name</label>
                      <input type="text" name="username" class="form-control input-lg" id="name" placeholder="username" />
                    </div>
                    <div class="form-group">
                      <label for="password" class="sr-only">password</label>
                      <input type="password" name="password" class="form-control input-lg" id="password" placeholder="password" />
                    </div>
                    <input type="submit" class="btn btn-primary btn-lg btn-block" value="ログイン" />
                  </form>
                </div>
              </div>
            </div>
          </section>

          <div id = "push"></div>
          <div id = "footer" >
            <div class="row">
                <div class="col-lg-12 col-sm-12 col-xs-12 ">
    					<div class="container">

    						<p class="text-muted fh5co-no-margin-bottom text-center"><small>&copy; 2016
    						<a href="#">ORGANIC KITCHEN</a>. All rights reserved.
              </div>
              </div>
              </div>
    				</div>
  </body>

</html>
