<?php

// prepare
include_once '../logic/common/header.inc'; // header, need HTML close tag in this code
include_once '../logic/common/_connect.inc'; // DB Connect Class
include_once '../logic/common/_common_method.inc'; // Common Method Class
// for php-5.6.2x
date_default_timezone_set('Asia/Tokyo'); // not need if php version is over 5.6.5

// 1. Get Operator Id
$operator_id = $_SESSION["id"];

// db connect obj
$dbConnectObj = new DB_HANDLER();
$dbConnectObj->dbConnect();
$dbConnectObj->beginTransaction();
$commonMethodObj = new COMMON_METHOD($dbConnectObj);

// 2. Get URL Parameter
if ($_REQUEST['search'] == 'd') {
    $searchTargetDeliverDate = $_REQUEST['date'];
}

// 3. Get Operator Info
$resGetOperatorInfoArray = array();
$resGetOperatorInfoArray = $commonMethodObj->getOperatorInfo($operator_id);
if (!$resGetOperatorInfoArray) {
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

// 5. Get Purchase History Info By Store Code
if ($searchTargetDeliverDate === date("Y-m-d", strtotime($searchTargetDeliverDate))) { // select by target day
    $sqlGetPurchaseHistoryByOperatorId = "SELECT ph.purchase_history_id, ph.order_id, ph.store_code, ph.product_id, ph.deliver_date, ph.deliver_status, ph.purchase_number, p.product_name, p.price FROM purchase_history ph inner join product p on ph.product_id = p.product_id WHERE ph.store_code = $storeCode AND ph.deliver_date = '$searchTargetDeliverDate' ORDER BY p.product_id";
} else { // select from 3 month ago to latest
    $limitMonth = date('Y-m-d', strtotime("-3 month"));
    $sqlGetPurchaseHistoryByOperatorId = "SELECT ph.purchase_history_id, ph.order_id, ph.store_code, ph.product_id, ph.deliver_date, ph.deliver_status, ph.purchase_number, p.product_name, p.price FROM purchase_history ph inner join product p on ph.product_id = p.product_id WHERE ph.store_code = $storeCode AND ph.deliver_date >= '$limitMonth' ORDER BY ph.deliver_date";
}
$resGetPurchaseHistoryByOperatorId = $dbConnectObj->executeSql($sqlGetPurchaseHistoryByOperatorId);
if (!$resGetPurchaseHistoryByOperatorId || $resGetPurchaseHistoryByOperatorId == null) {
    $dbConnectObj->rollback();
    $dbConnectObj->close();
    // redirect to error page
    header('location: ./error.php?ecode=SE067');
    exit();
}
$orderInfoByOperatorArray = array();
while ($row = mysqli_fetch_array($resGetPurchaseHistoryByOperatorId)) {
    $orderInfoByOperatorArray[] = array( 'order_id'        => $row["order_id"],
                                         'store_code'      => $row["store_code"],
                                         'product_id'      => $row["product_id"],
                                         'deliver_date'    => $row["deliver_date"],
                                         'deliver_status'  => $row["deliver_status"],
                                         'purchase_number' => $row["purchase_number"],
                                         'product_name'    => $row["product_name"],
                                         // 'price'           => $row['price']
                                         'price'           => $productPriceArray[$row["product_id"]]
                                       );
}

// 6. commit, close DB
$dbConnectObj->commit();
$dbConnectObj->close();

?>

    <script>
      function searchByDeliverDate() {
        var targetDeliverDate = document.getElementById("datepicker").value;
        if (targetDeliverDate == '' || targetDeliverDate == null) {
          return false;
        }
        location.href = './history_detail.php?search=d&date=' + targetDeliverDate.replace(/\//g,"-");
      }
    </script>


      <!-- <div class="container-fluid">-->
        <div  class="row">
            <div >
            <div class="container">

              <!-- フォームの開始-->
              <div class="container col-md-5 col-sm-5 ">
                <form>
                  <br /><br />
                  <p>企業名: <?php echo $companyName; ?> 様</p>
                  <p>店舗名: <?php echo $storeName; ?> 様</p>
                  <p>担当者名: <?php echo $operatorName; ?> 様</p>
                  <p>1営業日あたり送料: <?php echo $deliverCharge; ?> 円</p>
                  <table width="340">
                    <tr>
                      <td width="150">
                        <input name="searchDeliverDate" type="text" id="datepicker" style="width:150px; padding:6px 12px; height:34px; font-size:14px; border-radius:4px;" value="" placeholder="日付を選択">
                      </td>
                      <td width="180">
                        <button type="button" style="width:180px; margin-left:10px; padding:6px 12px; height:34px; font-size:14px; border-radius:4px;" onClick="searchByDeliverDate()">配達日で絞り込む</button>
                      </td>
                    </tr>
                  </table>
                    <!--テーブル-->

                    <table class="table" style="padding-top:10px; width: 720px;">
                      <thead>
                        <tr>
                          <th>配達日</th>
                          <th>品名</th>
                          <th>個数</th>
                          <th>小計</th>
                          <th>注文状況</th>
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
                        ?>
                          <tr>
                            <td><?php echo $deliverDate; ?></td>
                            <td><?php echo $productName; ?></td>
                            <td><?php echo $purchaseNumber; ?></td>
                            <td><?php echo number_format($purchaseNumber * $price); ?></td>
                          <?php if ($now < $compDate && $deliverStatus == 0) { ?>
                            <td><a href="./order_edit.php?ddate=<?php echo $deliverDate; ?>">確定前（変更可）</a></td>
                          <?php } else if ($deliverStatus == 3) { ?>
                            <td>キャンセル済み</td>
                          <?php } else { ?>
                            <td>確定（変更不可）</td>
                          <?php } ?>
                          </tr>
                        <?php } ?>
                      </tbody>
                  </table>
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
