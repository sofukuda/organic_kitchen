<?php

// prepare
include_once '../logic/common/header.inc'; // header, need HTML close tag in this code
include_once '../logic/common/_connect.inc'; // DB Connect Class
// for php-5.6.2x
date_default_timezone_set('Asia/Tokyo'); // not need if php version is over 5.6.5

// 1. prepare
$operatorId = $_SESSION["id"];

// db connect obj
$dbConnectObj = new DB_HANDLER();
$dbConnectObj->dbConnect();
$dbConnectObj->beginTransaction();

// Get url parameter
$storeCode          = $_REQUEST['storeCode'];
$productIdArray     = $_REQUEST['productIds'];
$productNameArray   = $_REQUEST['productNames'];
$startDate          = $_REQUEST['startDate'];
$totalPrice         = $_REQUEST['productTotalPrice'];
$sendFee            = $_REQUEST['sendFee'];
$deliverAddressType = $_REQUEST['deliverAddressSelect']; // regist / new

$deliverAddress = '';
if ($deliverAddressType == 'regist') {
    $deliverAddress = $_REQUEST['deliverAddressRegist'];
} else if ($deliverAddressType == 'new') {
    $deliverAddress = $_REQUEST['deliverAddressNew'];
}

// 2. 注文可能日付を取得する
$week  = array("日", "月", "火", "水", "木", "金", "土");
$deliverDateArray = array( '0' => $startDate,
                           '1' => date("m/d", strtotime("$startDate +1 day")),
                           '2' => date("m/d", strtotime("$startDate +2 day")),
                           '3' => date("m/d", strtotime("$startDate +3 day")),
                           '4' => date("m/d", strtotime("$startDate +4 day"))
                         );

$deliverDateNumArray = array( '0' => str_replace('/', '', $startDate),
                              '1' => date("Ymd", strtotime("$startDate +1 day")),
                              '2' => date("Ymd", strtotime("$startDate +2 day")),
                              '3' => date("Ymd", strtotime("$startDate +3 day")),
                              '4' => date("Ymd", strtotime("$startDate +4 day"))
                            );

?>


      <!-- <div class="container-fluid">-->
        <div  class="row">
            <div >
            <h2 class="page-header" style="margin-left:30px">発注確認</h2>
            <p style="margin-left:30px">以下の内容でよろしいですか？</p>
            <div class="container">

              <!-- フォームの開始-->
              <div class="container col-md-5 col-sm-5 ">
                <form method="POST" action="./single_order_execute.php">
                  <input type="hidden" name="operatorId" value="<?php echo $operatorId; ?>">
                  <input type="hidden" name="storeCode" value="<?php echo $storeCode; ?>">
                  <input type="hidden" name="deliverAddressType" value="<?php echo $deliverAddressType; ?>">
                  <!-- セレクト-->

                      <!--<label>password</label>-->
                    <div >
                      <label>配送先</label>
                      <p><?php echo $deliverAddress; ?></p>
                      <input type="hidden" name="startDate" value="<?php echo $startDate; ?>">
                      <input type="hidden" name="deliverAddress" value="<?php echo $deliverAddress; ?>">
                    </div>

                    <!--テーブル-->

                    <table class="table" style="padding-top:10px;">
                      <thead>
                        <tr>
                          <th>品目/個数</th>
                          <?php
                            $weekIndex = 0;
                            foreach ($deliverDateArray as $deliverDate) {
                              $weekIndex++;
                          ?>
                              <th><?php echo $deliverDate . '(' . $week[$weekIndex] . ')'; ?></th>
                          <?php
                            }
                          ?>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                          for ($i = 0, $len = count($productIdArray); $i < $len; $i++) {
                            $productId   = $productIdArray[$i];
                            $productName = $productNameArray[$i];
                        ?>
                        <input type="hidden" name="productIds[]" value="<?php echo $productId; ?>">
                        <tr>
                          <td><?php echo $productName; ?></td>
                        <?php
                            foreach ($deliverDateNumArray as $deliverDateNum) {
                              $str            = $productId . '_' . $deliverDateNum;
                              $purchaseNumStr = $_REQUEST[$str];
                              $purchaseNum    = (int)($purchaseNumStr);
                        ?>
                          <td><?php echo $purchaseNum; ?></td>
                          <input type="hidden" name="<?php echo $str; ?>" value="<?php echo $purchaseNum; ?>">
                        <?php
                            }
                        ?>
                        </tr>
                        <?php
                          }
                        ?>
                      </tbody>
                  </table>
                  <table class="table table-bordered" style="margin-top:20px;">
                    <thead>
                      <tr>
                        <th>項目名</th>
                        <th>金額</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                          <td>商品合計</td>
                          <td><?php echo number_format($totalPrice); ?></td>
                          <input type="hidden" name="totalPrice" value="<?php echo $totalPrice; ?>">
                      </tr>
                      <tr>
                          <td>送料</td>
                          <td><?php echo $sendFee; ?></td>
                          <input type="hidden" name="sendFee" value="<?php echo $sendFee; ?>">
                      </tr>

                    </tbody>
                </table>
                  <button type="submit" class="btn btn-primary btn-lg btn-block">発注する</button>
                  <button type="button" class="btn btn-default btn-lg btn-block" onClick="location.href='./single_order.php'">キャンセル</button>
                </form>
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
