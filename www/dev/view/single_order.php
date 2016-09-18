<?php

// prepare
include_once '../logic/common/header.inc'; // header, need HTML close tag in this code
include_once '../logic/common/_connect.inc'; // DB Connect Class
include_once '../logic/common/_common_method.inc'; // Common Method Class
// for php-5.6.2x
date_default_timezone_set('Asia/Tokyo'); // not need if php version is over 5.6.5

// 1. Get Parameter
$operator_id = $_SESSION["id"];
$calStartDate = $_REQUEST["sdate"];

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
$deliverCharge  = $resGetOperatorInfoArray['deliverCharge'];
$deliverAddress = $resGetOperatorInfoArray['deliverAddress'];
$deliverPriceThreshold = $resGetOperatorInfoArray['deliverPriceThreshold'];

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
    $tmpArray = array();
    $tmpArray = array('product_id'   => $row["product_id"],
                      'product_name' => $row["product_name"],
                      //'price'        => $row["price"]
                      'price'        => $productPriceArray[$row["product_id"]]
                     );
    $productInfoArray[] = $tmpArray;
}

// 5. 注文可能日付を取得する(start_dateは必ず月曜日)
$today   = date('Y/m/d');
$nowTime = date('H:i:s');
$w       = date('w');
$week    = array("日", "月", "火", "水", "木", "金", "土");

if ( ($w >= 1 && $w <= 3) || ($w == 4 && $nowTime < '10:00:00') ) {
    $diff = 8 - $w;
    $start_date = date("Y/m/d", strtotime("$today +$diff day"));
} else {
    if ($w == 0) {
        $diff = 8;
        $start_date = date("Y/m/d", strtotime("$today +8 day"));
    } else {
        $diff = 15 - $w;
        $start_date = date("Y/m/d", strtotime("$today +$diff day"));
    }
}

$nextMonthDay     = date("Y/m/d", strtotime("$today +1 month"));
$dateOfDatePicker = '';
if ($calStartDate >= $start_date && $start_date <= $nextMonthDay) {
    $year     = substr($calStartDate, 0, 4);
    $month    = substr($calStartDate, 5, 2);
    $day      = substr($calStartDate, 8, 2);
    $datetime = new DateTime();
    $datetime->setDate($year, $month, $day);
    $calW     = (int)$datetime->format('w');
    if ($calW == 0) {
        $start_date = date("Y/m/d", strtotime("$calStartDate -6 day"));
    } else {
        $calDiff = $calW - 1;
        $start_date = date("Y/m/d", strtotime("$calStartDate -$calDiff day"));
    }
    $dateOfDatePicker = $calStartDate;
}

?>

    <script>

      $(function() {
        $("#datePicker").datepicker({
          minDate: '<?php echo '+' . $diff . 'd'; ?>',
          maxDate: '+1m'
        });
      });

      function aggregate() {
        var productTotalPrice = 0;
        var inputProductNumberArray = document.getElementsByClassName("inputProductNumber");
        var inputProductPriceArray  = document.getElementsByClassName("inputProductPrice");
        for (i = 0; i < inputProductNumberArray.length; i++) {
          var num   = parseInt(inputProductNumberArray[i].value);
          var price = parseInt(inputProductPriceArray[i].value);
          productTotalPrice += price * num;
        }
        var deliverPriceThreshold = <?php echo $deliverPriceThreshold; ?>;
        $("#totalAndSendFee tbody tr:eq(0) td:eq(1)").empty();
        $("#totalAndSendFee tbody tr:eq(0) td:eq(1)").append(productTotalPrice);
        document.getElementById('productTotalPrice').value = productTotalPrice;
console.log('total: ' + productTotalPrice + ', thre: ' + deliverPriceThreshold);
        if (productTotalPrice >= deliverPriceThreshold) {
          $("#totalAndSendFee tbody tr:eq(1) td:eq(1)").empty();
          $("#totalAndSendFee tbody tr:eq(1) td:eq(1)").append(0);
          document.getElementById('sendFee').value = 0;
        } else {
          $("#totalAndSendFee tbody tr:eq(1) td:eq(1)").empty();
          $("#totalAndSendFee tbody tr:eq(1) td:eq(1)").append(<?php echo $deliverCharge; ?>);
          document.getElementById('sendFee').value = <?php echo $deliverCharge; ?>;
        }
      }

      function changeDeiverWeek() {
        var targetStartDateOfTargetWeek = document.getElementById("datePicker").value;
        if (targetStartDateOfTargetWeek == '' || targetStartDateOfTargetWeek == null) {
          return false;
        }
        location.href = './single_order.php?sdate=' + targetStartDateOfTargetWeek;

      }

    </script>


      <!-- <div class="container-fluid">-->
        <div  class="row">
            <div >
            <h2 class="page-header" style="margin-left:20px">商品発注</h2>
            <div class="container">

              <!-- フォームの開始-->
              <div class="container col-md-8 col-md-offset-2 col-sm-6 col-sm-offset-3 ">
                <form name="order" action="./single_order_confirm.php" method="POST">
                  <input type="hidden" name="storeCode" value="<?php echo $storeCode; ?>">
                  <input type="hidden" name="startDate" value="<?php echo $start_date; ?>">
                  <input id="sendFee" type="hidden" name="sendFee" value="<?php echo $deliverCharge; ?>">
                  <input id="productTotalPrice"type="hidden" name="productTotalPrice" value="0">

                  <!-- セレクト-->
                    <!--<label>password</label>-->
                    <div>
                      <!--<label>password</label>-->
                      <div class="select-box01">
                        <label style="padding-top:10px;">配送先選択</label><br />
                        <input type="radio" id="registeredDeliverAddress" name="deliverAddressSelect" value="regist" checked="checked"> 登録済みの配送先<br />
                          <pre type="text" style="width:100%; padding:6px 12px; height:34px; font-size:14px; border-radius:4px;"><?php echo $deliverAddress; ?></pre>
                          <input type="hidden" name="deliverAddressRegist" value="<?php echo $deliverAddress; ?>">
                      </div>

                      <table width="340">
                        <tr>
                          <td width="150">
                            <input name="selectedStartDate" type="text" id="datePicker" style="width:150px; padding:6px 12px; height:34px; font-size:14px; border-radius:4px;" value="<?php echo $dateOfDatePicker; ?>" placeholder="日付を選択">
                          </td>
                          <td width="180">
                            <button type="button" style="width:180px; margin-left:10px; padding:6px 12px; height:34px; font-size:14px; border-radius:4px;" onClick="changeDeiverWeek()">を含む週で絞り込む</button>
                          </td>
                        </tr>
                      </table>
                    </div>

                    <!--テーブル-->

                    <table class="table" style="padding-top:10px;">
                      <thead>
                        <tr>
                          <th>品目/個数</th>
                          <th><?php echo $start_date . "(月)"; ?></th>
                          <th><?php echo date("m/d", strtotime("$start_date +1 day")) . "(火)"; ?></th>
                          <th><?php echo date("m/d", strtotime("$start_date +2 day")) . "(水)"; ?></th>
                          <th><?php echo date("m/d", strtotime("$start_date +3 day")) . "(木)"; ?></th>
                          <th><?php echo date("m/d", strtotime("$start_date +4 day")) . "(金)"; ?></th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                          foreach ($productInfoArray as $productInfoRecord) {
                            $productId   = $productInfoRecord['product_id'];
                            $productName = $productInfoRecord['product_name'];
                            $price       = $productInfoRecord['price'];
                        ?>
                            <tr>
                              <input type="hidden" name="productIds[]" value="<?php echo $productId; ?>">
                              <input type="hidden" name="productNames[]" value="<?php echo $productName; ?>">
                              <td><?php echo $productName; ?></td>
                              <td><input type="number" name="<?php echo $productId . '_' . date("Ymd", strtotime("$start_date")); ?>" value="0" placeholder="0" min="0" class="form-control inputProductNumber" onChange="aggregate()"></input>
                                  <input type="hidden" value="<?php echo $price; ?>" class="inputProductPrice">
                              </td>
                              <td><input type="number" name="<?php echo $productId . '_' . date("Ymd", strtotime("$start_date +1 day")); ?>" value="0" placeholder="0" min="0" class="form-control inputProductNumber" onChange="aggregate()"></input>
                                  <input type="hidden" value="<?php echo $price; ?>" class="inputProductPrice">
                              </td>
                              <td><input type="number" name="<?php echo $productId . '_' . date("Ymd", strtotime("$start_date +2 day")); ?>" value="0" placeholder="0" min="0" class="form-control inputProductNumber" onChange="aggregate()"></input>
                                  <input type="hidden" value="<?php echo $price; ?>" class="inputProductPrice">
                              </td>
                              <td><input type="number" name="<?php echo $productId . '_' . date("Ymd", strtotime("$start_date +3 day")); ?>" value="0" placeholder="0" min="0" class="form-control inputProductNumber" onChange="aggregate()"></input>
                                  <input type="hidden" value="<?php echo $price; ?>" class="inputProductPrice">
                              </td>
                              <td><input type="number" name="<?php echo $productId . '_' . date("Ymd", strtotime("$start_date +4 day")); ?>" value="0" placeholder="0" min="0" class="form-control inputProductNumber" onChange="aggregate()"></input>
                                  <input type="hidden" value="<?php echo $price; ?>" class="inputProductPrice">
                              </td>
                            </tr>
                        <?php
                          }
                        ?>
                      </tbody>
                  </table>

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
                          <td>0</td>
                      </tr>
                      <tr>
                          <td>送料</td>
                          <td><?php echo $deliverCharge; ?></td>
                      </tr>
                      <div id="sendDetail"></div>
                    </tbody>
                </table>
                  <button type="submit" class="btn btn-primary btn-lg btn-block">注文内容を確認する</button>

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
