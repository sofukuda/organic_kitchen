<?php

// prepare
include_once '../logic/common/admin_header.inc'; // header, need HTML close tag in this code
include_once '../logic/common/_connect.inc'; // DB Connect Class
include_once '../logic/common/_common_method.inc'; // Common Method Class
// for php-5.6.2x
date_default_timezone_set('Asia/Tokyo'); // not need if php version is over 5.6.5

// 1. Get Operator Id
$operator_id = $_SESSION["id"];

// 2. Get POST Parameter
$storeCode = '';
if ($_REQUEST['store_code'] != '' && $_REQUEST['store_code'] != null) {
    $storeCode = $_REQUEST['store_code'];
} else {
    // redirect to company info page
    header('location: ./company_info.php');
    exit();
}
$newStoreName          = $_REQUEST['store_name'];
$newClassifyCode       = $_REQUEST['classify_code'];
$newDeliverCharge      = $_REQUEST['deliver_charge'];
$newSlipType           = $_REQUEST['slip_type'];
$newBillingType        = $_REQUEST['billing_type'];
$newTaxType            = $_REQUEST['tax_type'];
$newStoreAddress       = $_REQUEST['store_address'];
$newStorePhoneNumber   = $_REQUEST['store_phone_number'];
$newBillingAddress     = $_REQUEST['billing_address'];
$newBillingPhoneNumber = $_REQUEST['billing_phone_number'];
$OperatorIdArray             = $_REQUEST['operator_id'];
$newOperatorNameArray        = $_REQUEST['operator_name'];
$newOperatorPhoneNumberArray = $_REQUEST['operator_phone_number'];
$newOperatorMailAddressArray = $_REQUEST['operator_mail_address'];

// 3. db connect obj
$dbConnectObj = new DB_HANDLER();
$dbConnectObj->dbConnect();
$dbConnectObj->beginTransaction();
$commonMethodObj = new COMMON_METHOD($dbConnectObj);

// 4. Get Operator Detail
$resGetOperatorDetailArray = array();
$resGetOperatorDetailArray = $commonMethodObj->getOperatorDetail($operator_id);
if (!$resGetOperatorDetailArray) {
    // redirect to error page
    header('location: ./error.php?ecode=SE1405');
    exit();
}
$operatorRole = $resGetOperatorDetailArray['role'];
// check user role: admin(role=1) is OK
if ($operatorRole != 1) {
    // redirect to error page
    header('location: ./error.php?ecode=SE1406');
    exit();
}

// 5. Get Store Info
$sqlGetStoreInfo = "SELECT * FROM company c inner join operator o on c.operator_id = o.operator_id WHERE c.store_code = $storeCode AND c.delete_flag = 0 AND o.delete_flag = 0 LIMIT 1;";
$resGetStoreInfo = $dbConnectObj->executeSql($sqlGetStoreInfo);
if (!$resGetStoreInfo || $resGetStoreInfo == null) {
    $dbConnectObj->rollback();
    $dbConnectObj->close();
    // redirect to error page
    header('location: ./error.php?ecode=SE1407');
    exit();
}
while ($row = mysqli_fetch_array($resGetStoreInfo)) {
    $tmpArray = array( 'company_id'            => $row["company_id"]
                       ,'company_code'          => $row["company_code"]
                       ,'company_name'          => $row["company_name"]
                       ,'store_code'            => $row["store_code"]
                       //'store_name'            => $row["store_name"],
                       //'classify_code'         => $row["classify_code"],
                       //'deliver_charge'        => $row["deliver_charge"],
                       //'slip_type'             => $row["slip_type"],
                       //'billing_type'          => $row["billing_type"],
                       //'tax_type'              => $row["tax_type"],
                       //'store_address'         => $row["store_address"],
                       //'store_phone_number'    => $row["store_phone_number"],
                       //'billing_address'       => $row["billing_address"],
                       //'billing_phone_number'  => $row["billing_phone_number"],
                       //'operator_name'         => $row["operator_name"],
                       //'operator_mail_address' => $row["operator_mail_address"],
                       //'operator_phone_number' => $row["operator_phone_number"]
                     );

    $companyId          = $row["company_id"];
    $companyCode        = $row["company_code"];
    $companyName        = $row["company_name"];
    $storeCode          = $row["store_code"];
    //$storeName          = $row["store_name"];
    //$classifyCode       = $row["classify_code"];
    //$deliverCharge      = $row["deliver_charge"];
    //$slipType           = $row["slip_type"];
    //$billingType        = $row["billing_type"];
    //$taxType            = $row["tax_type"];
    //$storeAddress       = $row["store_address"];
    //$storePhoneNumber   = $row["store_phone_number"];
    //$billingAddress     = $row["billing_address"];
    //$billingPhoneNumber = $row["billing_phone_number"];
}
if ($companyId == null || $companyId == '') {
    // redirect to error page
    header('location: ./error.php?ecode=SE1606');
    exit();
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
                <form action="./edit_store_info_execute.php" method="POST">
                  <input type="hidden" name="company_code" value="<?php echo $companyCode; ?>">
                  <input type="hidden" name="store_code" value="<?php echo $storeCode; ?>">
                  <input type="hidden" name="store_name" value="<?php echo $newStoreName; ?>">
                  <input type="hidden" name="classify_code" value="<?php echo $newClassifyCode; ?>">
                  <input type="hidden" name="deliver_charge" value="<?php echo $newDeliverCharge; ?>">
                  <input type="hidden" name="slip_type" value="<?php echo $newSlipType; ?>">
                  <input type="hidden" name="billing_type" value="<?php echo $newBillingType; ?>">
                  <input type="hidden" name="tax_type" value="<?php echo $newTaxType; ?>">
                  <input type="hidden" name="store_address" value="<?php echo $newStoreAddress; ?>">
                  <input type="hidden" name="store_phone_number" value="<?php echo $newStorePhoneNumber; ?>">
                  <input type="hidden" name="billing_address" value="<?php echo $newBillingAddress; ?>">
                  <input type="hidden" name="billing_phone_number" value="<?php echo $newBillingPhoneNumber; ?>">
                  <br /><br />
                    <span style="padding-top:10px;"><b>企業コード</b>: <?php echo $companyCode; ?></span><br />
                    <span style="padding-top:10px;"><b>企業名</b>: <?php echo $companyName; ?></span><br />

                    <table class="table" style="padding-top:10px; width: 720px; margin-left: 30px; margin-top: 20px;">
                      <tbody>
                          <tr>
                            <td><b>店舗情報</b></td>
                            <td></td>
                          </tr>
                          <tr>
                            <td>店舗コード</td>
                            <td><?php echo $storeCode; ?></td>
                          </tr>
                          <tr>
                            <td>店舗名</td>
                            <td><?php echo $newStoreName; ?></td>
                          </tr>
                          <tr>
                            <td>分類コード</td>
                            <td><?php echo $newClassifyCode; ?></td>
                          </tr>
                          <tr>
                            <td>配送料</td>
                            <td><?php echo $newDeliverCharge; ?> 円</td>
                          </tr>
                          <tr>
                            <td>伝票タイプ</td>
                            <td><?php echo $newSlipType; ?></td>
                          </tr>
                          <tr>
                            <td>請求タイプ</td>
                            <td><?php echo $newBillingType; ?></td>
                          </tr>
                          <tr>
                            <td>課税タイプ</td>
                            <td><?php echo $newTaxType; ?></td>
                          </tr>
                          <tr>
                            <td>店舗住所</td>
                            <td><?php echo $newStoreAddress; ?></td>
                          </tr>
                          <tr>
                            <td>店舗電話番号</td>
                            <td><?php echo $newStorePhoneNumber; ?></td>
                          </tr>
                          <tr>
                            <td>請求先住所</td>
                            <td><?php echo $newBillingAddress; ?></td>
                          </tr>
                          <tr>
                            <td>請求先電話番号</td>
                            <td><?php echo $newBillingPhoneNumber; ?></td>
                          </tr>

                          <?php for ($i = 0; $i < count($OperatorIdArray); $i++) { $j = $i+1; ?>
                            <tr>
                              <td><b>担当者 <?php echo '(' . $j . ')'; ?></b></td>
                              <td></td>
                            </tr>
                            <input type="hidden" name="operator_id[]" value="<?php echo $OperatorIdArray[$i]; ?>">
                            <tr>
                              <td>担当者名</td>
                              <td><?php echo $newOperatorNameArray[$i]; ?></td>
                              <input type="hidden" name="operator_name[]" value="<?php echo $newOperatorNameArray[$i]; ?>">
                            </tr>
                            <tr>
                              <td>担当者メールアドレス</td>
                              <td><?php echo $newOperatorMailAddressArray[$i]; ?></td>
                              <input type="hidden" name="operator_mail_address[]" value="<?php echo $newOperatorMailAddressArray[$i]; ?>">
                            <tr>
                              <td>担当者電話番号</td>
                              <td><?php echo $newOperatorPhoneNumberArray[$i]; ?></td>
                              <input type="hidden" name="operator_phone_number[]" value="<?php echo $newOperatorPhoneNumberArray[$i]; ?>"
                            </tr>
                            </tr>
                          <?php } ?>

                      </tbody>
                  </table>

                  <button type="submit" class="btn btn-primary btn-lg btn-block">決定</button>
                  <button type="button" class="btn btn-default btn-lg btn-block" onClick="location.href='./edit_store_info.php?scode=<?php echo $storeCode; ?>'">キャンセル</button>

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
