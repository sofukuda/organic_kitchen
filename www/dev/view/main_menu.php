<?php
// header 読み込み
include_once '../logic/common/header.inc';

?>

      <!-- <div class="container-fluid">
        <div class="row">
          <div class="col-sm-2" style="background-color:red;">Red</div>
          <div class="col-sm-8" style="background-color:blue;">Blue</div>
          <div class="col-sm-2" style="background-color:yellow;">Yellow</div>
        </div>-->
        <div  class="row">
            <div class="col-lg-12" >
            <h2 class="page-header">メインメニュー</h2>

        <div class="card col-lg-4 col-sm-6 text-center center-block">
          <img class="card-img" src="images/cart.png" alt="">
          <div class="card-content">
            <h1 class="card-title">商品注文</h1>
            <p class="card-text">日付を指定して、購入いただけます。</p></br>
          </div>
          <div class="card-link">
            <a href="single_order.php">こちら</a>
          </div>
        </div>

        <div class="card col-lg-4 col-sm-6 text-center center-block">
          <img class="card-img" src="images/cart.png" alt="">
          <div class="card-content">
            <h1 class="card-title">定期注文</h1>
            <p class="card-text">曜日ごとに継続して購入いただけます。</p></br>
          </div>
          <div class="card-link">
            <a href="subscriptions_order.php">こちら</a>
          </div>
        </div>

        <div class="card col-lg-4 col-sm-6 text-center center-block">
          <img class="card-img" src="images/history.png" alt="">
          <div class="card-content">
            <h1 class="card-title">購入履歴</h1>
            <p class="card-text">購入内容の確認が行えます。</br>発送２営業日前の10時までは変更可能です。</p>
          </div>
          <div class="card-link">
            <a href="history.php">こちら</a>
          </div>
        </div>

        <div class="card col-lg-4 col-sm-6 text-center center-block">
                <img class="card-img" src="images/menu.png" alt="">
                <div class="card-content">
                  <h1 class="card-title">今週のメニュー</h1>
                  <p class="card-text">HPより今週のメニューを確認できます。</p></br>
                </div>
                <div class="card-link">
                  <a href="http://staging.organic-kitchen.co.jp/?page_id=207">こちら</a>
                </div>
        </div>

        <div class="card col-lg-4 col-sm-6 text-center center-block">
                  <img class="card-img" src="images/information.png" alt="">
                  <div class="card-content">
                    <h1 class="card-title">契約情報</h1>
                    <p class="card-text">商品単価や登録情報を確認できます。</p></br>
                  </div>
                  <div class="card-link">
                    <a href="my_information.php">こちら</a>
                  </div>
        </div>
      </div>
      </div>


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

  </div>
  </body>

</html>
