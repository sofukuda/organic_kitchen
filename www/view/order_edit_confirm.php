<?php

// prepare
include_once '../logic/common/header.inc'; // header, need HTML close tag in this code
include_once '../logic/common/_connect.inc'; // DB Connect Class
include_once '../logic/common/_common_method.inc'; // Common Method Class
// for php-5.6.2x
date_default_timezone_set('Asia/Tokyo'); // not need if php version is over 5.6.5

// 1. Get Parameters
$operator_id          = $_SESSION["id"];
$purchase_history_ids = $_REQUEST["purchaseHistoryIds"];
$editTargetDate       = $_REQUEST["editTargetDate"];

// db connect obj
$dbConnectObj = new DB_HANDLER();
$dbConnectObj->dbConnect();
$dbConnectObj->beginTransaction();
$commonMethodObj = new COMMON_METHOD($dbConnectObj);

// 2. Get & Make Array of PurchaseHistoryId and ChangeValue
$phId_chgValue_array  = array();
foreach ($purchase_history_ids as $purchase_history_id) {
    $change_value        = number_format($_REQUEST["$purchase_history_id"]);
    $phId_chgValue_array += array( "$purchase_history_id" => $change_value ); // associative array: key => purchase_history_id, value => change_value
}

// 3. Get Operator Info
$resGetOperatorInfoArray = array();
$resGetOperatorInfoArray = $commonMethodObj->getOperatorInfo($operator_id);if (!$resGetOperatorInfoArray) {
    // redirect to error page
    header('location: ./error.php?ecode=SE065');
    exit();
}
$companyCode   = $resGetOperatorInfoArray['companyCode'];
$companyName   = $resGetOperatorInfoArray['companyName'];
$storeCode     = $resGetOperatorInfoArray['storeCode'];
$storeName     = $resGetOperatorInfoArray['storeName'];
$deliverCharge = $resGetOperatorInfoArray['deliverCharge'];
$operatorName  = $resGetOperatorInfoArray['operatorName'];

// 4. Get Product Price per Company
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

// 5. Get Purchase History Info By Store
$sqlGetPurchaseHistoryByOperatorId = "SELECT ph.purchase_history_id, ph.order_id, ph.store_code, ph.product_id, ph.deliver_date, ph.deliver_status, ph.purchase_number, p.product_name, p.price FROM purchase_history ph inner join product p on ph.product_id = p.product_id WHERE ph.store_code = $storeCode AND ph.deliver_date = '$editTargetDate' AND ph.deliver_status = 0 ORDER BY ph.deliver_date";
$resGetPurchaseHistoryByOperatorId = $dbConnectObj->executeSql($sqlGetPurchaseHistoryByOperatorId);
if (!$resGetPurchaseHistoryByOperatorId || $resGetPurchaseHistoryByOperatorId == null) {
    $dbConnectObj->rollback();
    $dbConnectObj->close();
    // redirect to error page
    header('location: ./error.php?ecode=SE066');
    exit();
}
$orderInfoByOperatorArray = array();
while ($row = mysqli_fetch_array($resGetPurchaseHistoryByOperatorId)) {
    $orderInfoByOperatorArray[] = array( 'purchase_history_id' => $row["purchase_history_id"],
                                         'order_id'            => $row["order_id"],
                                         'store_code'          => $row["store_code"],
                                         'product_id'          => $row["product_id"],
                                         'deliver_date'        => $row["deliver_date"],
                                         'deliver_status'      => $row["deliver_status"],
                                         'purchase_number'     => $row["purchase_number"],
                                         'product_name'        => $row["product_name"],
                                         //'price'               => $row['price']
                                         'price'               => $productPriceArray[$row["product_id"]]
                                       );
}

// 6. commit, close DB
$dbConnectObj->commit();
$dbConnectObj->close();

?>


      <!-- <div class="container-fluid">-->
        <div  class="row">
            <div >
            <div class="container">

              <!-- フォームの開始-->
              <div class="container col-md-5 col-sm-5 ">
                <form method="POST" action="./order_edit_execute.php">
                  <br /><br />
                  <p>企業名: <?php echo $companyName; ?> 様</p>
                  <p>店舗名: <?php echo $storeName; ?> 様</p>
                  <p>担当者名: <?php echo $operatorName; ?> 様</p>
                  <p>1営業日あたり送料: <?php echo $deliverCharge; ?> 円</p>
                  <p>変更対象日：<?php echo str_replace('-', '/', $editTargetDate); ?></p>

                    <!--テーブル-->
                    <table class="table" style="padding-top:10px; width: 720px;">
                      <thead>
                        <tr>
                          <th>変更対象商品名</th>
                          <th>個数（変更前）</th>
                          <th>個数（変更後）</th>
                          <th>金額（変更前）</th>
                          <th>金額（変更後）</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php foreach ($orderInfoByOperatorArray as $orderRecord) {
                          $purchaseHistoryId = $orderRecord['purchase_history_id'];
                          $orderId           = $orderRecord['order_id'];
                          $storeCode         = $orderRecord['store_code'];
                          $productId         = $orderRecord['product_id'];
                          $deliverDate       = $orderRecord['deliver_date'];
                          $deliverStatus     = $orderRecord['deliver_status'];
                          $purchaseNumber    = $orderRecord['purchase_number'];
                          $productName       = $orderRecord['product_name'];
                          $price             = $orderRecord['price'];
                          $now               = date("Y-m-d H:m:s");
                          $compDate          = date("Y-m-d 10:00:00", strtotime("$deliverDate -2 day"));
                          if ($now >= $compDate) { continue; }
                          $changeValue = $phId_chgValue_array[$purchaseHistoryId];
                          if ($purchaseNumber == $changeValue) { continue; } // if no change, do not view and not change
                          $totalPrice += $price * $changeValue;
                        ?>
                          <tr>
                            <input type="hidden" name="purchaseHistoryIds[]" value="<?php echo $purchaseHistoryId; ?>">
                            <input type="hidden" name="<?php echo 'changeValue_' . $purchaseHistoryId; ?>" value="<?php echo $changeValue; ?>">
                            <td><?php echo $productName; ?></td>
                            <td><?php echo $purchaseNumber; ?></td>
                            <td><?php echo $changeValue; ?></td>
                            <td><?php echo number_format($price * $purchaseNumber); ?></td>
                            <td name="modPrice"><?php echo number_format($price * $changeValue); ?></td>
                          </tr>
                        <?php } ?>
                      </tbody>
                    <input type="hidden" name="totalPrice" value="<?php echo $totalPrice; ?>">
                  </table>
                  <button type="submit" class="btn btn-primary btn-lg btn-block">変更する</button>
                  <button type="button" class="btn btn-default btn-lg btn-block" onclick="location.href='./order_edit.php?ddate=<?php echo $editTargetDate; ?>'">戻る</button>
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
