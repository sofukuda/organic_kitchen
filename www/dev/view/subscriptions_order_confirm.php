<?php

// prepare
include_once '../logic/common/header.inc'; // header, need HTML close tag in this code
include_once '../logic/common/_connect.inc'; // DB Connect Class
include_once '../logic/common/_common_method.inc'; // Common Method Class
// for php-5.6.2x
date_default_timezone_set('Asia/Tokyo'); // not need if php version is over 5.6.5

// 1. Get Parameter
$operator_id = $_SESSION["id"];
$productTotalPrice    = $_REQUEST["productTotalPrice"];
$wday                 = $_REQUEST["wday"];
$wdayNameList         = $_REQUEST["wdayNameList"];
$deliverAddressRegist = $_REQUEST["deliverAddressRegist"];
$weekDeliverDateList  = $_REQUEST["weekDeliverDateList"];
$sendFee              = $_REQUEST["sendFee"];

// db connect obj
$dbConnectObj = new DB_HANDLER();
$dbConnectObj->dbConnect();
$dbConnectObj->beginTransaction();
$commonMethodObj = new COMMON_METHOD($dbConnectObj);

// 2. Get Operator Info
$resGetOperatorInfoArray = array();
$resGetOperatorInfoArray = $commonMethodObj->getOperatorInfo($operator_id);
if (!$resGetOperatorInfoArray) {
    // redirect to error page
    header('location: ./error.php?ecode=SE065');
    exit();
}
$companyCode    = $resGetOperatorInfoArray['companyCode'];
$storeCode      = $resGetOperatorInfoArray['storeCode'];
//$deliverCharge  = $resGetOperatorInfoArray['deliverCharge'];
$deliverCharge  = $sendFee;
$deliverAddress = $resGetOperatorInfoArray['deliverAddress'];

// 3. Get Product Price per Company
$resGetProductPricePerCompany = array();
$resGetProductPricePerCompany = $commonMethodObj->GetProductPricePerCompany($companyCode);
if (!$resGetProductPricePerCompany) {
    // redirect to error page
    header('location: ./error.php?ecode=SE066');
    exit();
}
$productPriceArray = array();
while ($row = mysqli_fetch_array($resGetProductPricePerCompany)) {
    $productPriceArray += array( $row["product_id"] => $row["price"] );
}

// 4. Get Product Company Info
//$sqlGetProduct = "SELECT product_id, product_name, price FROM product WHERE delete_flag = 0;";
$sqlGetProduct = "SELECT pc.product_id, pc.price, p.product_name FROM product_company pc INNER JOIN product p on pc.product_id = p.product_id WHERE pc.delete_flag = 0 AND p.delete_flag = 0 AND pc.company_code = $companyCode";
$resGetProduct = $dbConnectObj->executeSql($sqlGetProduct);
if (!$resGetProduct || $resGetProduct == null) {
    $dbConnectObj->rollback();
    $dbConnectObj->close();
    // redirect to error page
    header('location: ./error.php?ecode=SE003');
    exit();
}
$dbConnectObj->commit();
$dbConnectObj->close();
$productInfoArray = array();
while ($row = mysqli_fetch_array($resGetProduct)) {
    $product_id = $row["product_id"];
    $order_num  = $_REQUEST[$product_id];
    if ($order_num == null || $order_num == '' || $order_num < 1) {
        continue;
    }
    $tmpArray = array();
    $tmpArray = array('product_id'   => $product_id,
                      'product_name' => $row["product_name"],
                      //'price'        => $row["price"]
                      'price'        => $productPriceArray[$row["product_id"]],
                      'order_num'    => $order_num
                     );
    $productInfoArray[] = $tmpArray;
}

?>

      <!-- <div class="container-fluid">-->
        <div  class="row">
            <div >
            <h2 class="page-header" style="margin-left:20px">定期発注確認</h2>
            <div class="container">

              <!-- フォームの開始-->
              <div class="container col-md-8 col-md-offset-2 col-sm-6 col-sm-offset-3 ">
                <form name="order" action="./subscriptions_order_execute.php" method="POST">
                  <input type="hidden" name="productTotalPrice" value="<?php echo $productTotalPrice; ?>">
                  <input type="hidden" name="sendFee" value="<?php echo $sendFee; ?>">
                  <input type="hidden" name="wday" value="<?php echo $wday; ?>">
                  <!-- セレクト-->
                    <!--<label>password</label>-->
                    <div>
                      <label style="padding-top:10px;">配達先</label><br />
                      <p><?php echo $deliverAddressRegist; ?></p>
                      <input type="hidden" name="deliverAddress" value="<?php echo $deliverAddressRegist; ?>">
                    </div>

                    <div id="selectedWeekDaySchedule">
                      <label style="padding-top:10px;">配達日</label><br />
                      <table class="table" style="padding-top:10px;">
                        <thead>
                          <tr>
                            <?php foreach ($wdayNameList as $wdayName) { ?>
                              <th><?php echo $wdayName; ?></th>
                            <?php } ?>
                          </tr>
                        </thead>
                        <tbody>
                          <tr>
                          <?php
                            $holdNumber = count($wdayNameList);
                            $trIndex    = $holdNumber + 1;
                            foreach ($weekDeliverDateList as $oneWeekDeliverDate) {
                          ?>
                              <td><?php echo $oneWeekDeliverDate; ?></td>
                              <input type="hidden" name="weekDeliverDateList[]" value="<?php echo $oneWeekDeliverDate; ?>">
                          <?php
                              if ($trIndex % $holdNumber == 0) {
                          ?>
                              </tr><tr>
                          <?php
                              }
                              $trIndex++;
                            }
                          ?>
                          </tr>
                        </tbody>
                      </table>
                    </div>

                    <!--テーブル-->

                    <table class="table" style="padding-top:10px;">
                      <thead>
                        <tr>
                          <th>品目</th>
                          <th>個数</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                          foreach ($productInfoArray as $productInfoRecord) {
                            $productId   = $productInfoRecord['product_id'];
                            $productName = $productInfoRecord['product_name'];
                            $price       = $productInfoRecord['price'];
                            $orderNum    = $productInfoRecord['order_num'];
                        ?>
                            <tr>
                              <td><?php echo $productName; ?></td>
                              <td><?php echo $orderNum; ?></td>
                              <input type="hidden" name="productId[]" value="<?php echo $productId; ?>">
                              <input type="hidden" name="<?php echo $productId; ?>" value="<?php echo $orderNum; ?>">
                            </tr>
                        <?php
                          }
                        ?>
                      </tbody>
                  </table>
                  <a href="URL" target="_blank">商品単価の確認はこちら</a>
                  <table id="totalAndSendFee" class="table table-bordered" style="margin-top:20px;">
                    <thead>
                      <tr>
                        <th>項目名</th>
                        <th>金額(円)</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                          <td>商品合計</td>
                          <td><?php echo number_format($productTotalPrice); ?></td>
                      </tr>
                      <tr>
                          <td>送料</td>
                          <td><?php echo $deliverCharge; ?></td>
                      </tr>
                      <div id="sendDetail"></div>
                    </tbody>
                </table>
                  <button type="submit" class="btn btn-primary btn-lg btn-block">決定</button>
                  <button type="button" class="btn btn-default btn-lg btn-block" onClick="location.href='./subscriptions_order.php'">キャンセル</button>
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
