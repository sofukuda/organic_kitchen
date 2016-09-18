<?php

// prepare
include_once '../logic/common/header.inc'; // header, need HTML close tag in this code
include_once '../logic/common/_connect.inc'; // DB Connect Class
// for php-5.6.2x
date_default_timezone_set('Asia/Tokyo'); // not need if php version is over 5.6.5

// 1. Get Operator Id
$operator_id = $_SESSION["id"];

// db connect obj
$dbConnectObj = new DB_HANDLER();
$dbConnectObj->dbConnect();
$dbConnectObj->beginTransaction();

// 3. Get Operator Info
$sqlGetOperatorInfo = "SELECT * FROM company c inner join operator o on c.operator_id = o.operator_id WHERE o.operator_id = $operator_id";
$resGetOperatorInfo = $dbConnectObj->executeSql($sqlGetOperatorInfo);
if (!$resGetOperatorInfo || $resGetOperatorInfo == null) {
    $dbConnectObj->rollback();
    $dbConnectObj->close();
    // redirect to error page
    header('location: ./error.php?ecode=SE062');
    exit();
}
while ($row = mysqli_fetch_array($resGetOperatorInfo)) {
    $companyCode   = $row["company_code"];
    $companyName   = $row["company_name"];
    $storeCode     = $row["store_code"];
    $storeName     = $row["store_name"];
    $deliverCharge = $row["deliver_charge"];
    $operatorName  = $row["operator_name"];
}

// 4. Get Regular Purchase Plan Info By Operator
$limitMonth = date('Y-m-d 00:00:00', strtotime("-3 month")); // 3 month ago
$sqlGetRegularPurchasePlanByOperatorId = "SELECT order_id, deliver_address, total_product_price, created_datetime FROM regular_purchase_plan WHERE store_code = $storeCode AND created_datetime >= '$limitMonth' ORDER BY order_id DESC;";
$resGetRegularPurchasePlanByOperatorId = $dbConnectObj->executeSql($sqlGetRegularPurchasePlanByOperatorId);
if (!$resGetRegularPurchasePlanByOperatorId || $resGetRegularPurchasePlanByOperatorId == null) {
    $dbConnectObj->rollback();
    $dbConnectObj->close();
    // redirect to error page
//    header('location: ./error.php?ecode=SE063');
    exit();
}
$orderInfoByOperatorArray = array();
while ($row = mysqli_fetch_array($resGetRegularPurchasePlanByOperatorId)) {
    $orderInfoByOperatorArray[] = array( 'order_id'            => $row["order_id"],
                                         'deliver_address'     => $row["deliver_address"],
                                         'total_product_price' => $row["total_product_price"],
                                         'created_datetime'    => $row["created_datetime"]
                                       );
}


?>

      <!-- <div class="container-fluid">-->
        <div  class="row">
            <div >
            <div class="container">

              <!-- フォームの開始-->
              <div class="container col-md-5 col-sm-5 " style="margin-top: 40px;">
                <form>

                  <p>企業名: <?php   echo $companyName; ?> 様</p>
                  <p>店舗名: <?php   echo $storeName; ?> 様</p>
                  <p>担当者名: <?php echo $operatorName;?> 様</p>
                  <p>1営業日あたり送料: <?php echo $deliverCharge; ?> 円</p>
                  <p><a href="./history_detail.php">お届け日ごとの詳細を見る</a></p>

                    <!--テーブル-->

                    <table class="table" style="padding-top:10px; width: 720px;">
                      <thead>
                        <tr>
                          <th>注文番号</th>
                          <th>商品合計金額</th>
                          <th>お届け先</th>
                          <th>注文日時</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php foreach ($orderInfoByOperatorArray as $orderInfoRecord) {
                          $order_id   = $orderInfoRecord['order_id'];
                          $detailLink = './history_detail.php?order_id=' . $orderInfoRecord['order_id']; ?>
                          <tr>
                             <!-- <td><a href="<?php echo $detailLink; ?>"><?php echo $order_id; ?></a></td> -->
                            <td><?php echo $order_id; ?></td>
                            <td><?php echo number_format($orderInfoRecord['total_product_price']); ?></td>
                            <td><?php echo $orderInfoRecord['deliver_address']; ?></td>
                            <td><?php echo $orderInfoRecord['created_datetime']; ?></td>
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
