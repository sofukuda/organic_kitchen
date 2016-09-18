<?php session_start();
   if(!isset($_SESSION["username"])) {
       header('location: ./login.php?ecode=400');
    }
?>
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
    <link href="css/my-custom.css" rel="stylesheet">
    <link rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1/themes/smoothness/jquery-ui.css" >

    <!-- jQuery読み込み -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1/jquery-ui.min.js"></script>
    <script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1/i18n/jquery.ui.datepicker-ja.min.js"></script>
    <!-- BootstrapのJS読み込み -->
    <script src="js/bootstrap.min.js"></script>

    <script>
      $(function() {
        $("#datepicker").datepicker();
      });
    </script>
  </head>

  <body>
    <div id="wrap">
    <nav class="navbar navbar-default navbar-fixed-top">
      <div class="container-fluid">
      <!-- ヘッダー部分 ================ -->
      <div class="navbar-header">
          <a class="navbar-brand" href="main_menu.php">ORGANIC KITCHEN</a>
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#nav_target">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
        </div>
      <!-- 中央のナビゲーション部分 ================ -->
        <div class="collapse navbar-collapse" id="nav_target">

      <!-- 右寄せになる部分 ================ -->
          <ul class="nav navbar-nav navbar-right">
            <!-- ログイン -->
              <?php
                    $user = $_SESSION["username"];
                    echo '<li><a>ようこそ '.$user.' さん</a></li>';
                    echo '<li><a href="logout.php">ログアウト</a></li>';
              ?>
            <!-- リンクのみ -->
              <li><a href="single_order.php">商品発注</a></li>
              <li><a href="subscriptions_order.php">定期発注</a></li>

            <!-- Nav6 ドロップダウン -->
              <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">その他 <span class="caret"></span></a>
                <ul class="dropdown-menu">
                  <li><a href="history.php">購入履歴</a></li>
                  <li><a href="my_information.php">契約情報</a></li>
                  <li><a href="http://staging.organic-kitchen.co.jp/?page_id=207">今週のメニュー</a></li>
                </ul>
              </li>
          </ul>
        </div>
      </div>
    </nav>
