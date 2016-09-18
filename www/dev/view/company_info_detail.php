<?php

// prepare
include_once '../logic/common/admin_header.inc'; // header, need HTML close tag in this code
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
$searchCompanyCode = '';
if ($_REQUEST['ccode'] != '' && $_REQUEST['ccode'] != null) {
    $searchCompanyCode = $_REQUEST['ccode'];
} else {
    $dbConnectObj->rollback();
    $dbConnectObj->close();
    // redirect to company info page
    header('location: ./company_info.php');
    exit();
}

// 3. Get Operator Info
$resGetOperatorDetailArray = array();
$resGetOperatorDetailArray = $commonMethodObj->getOperatorDetail($operator_id);
if (!$resGetOperatorDetailArray) {
    $dbConnectObj->rollback();
    $dbConnectObj->close();
    // redirect to error page
    header('location: ./error.php?ecode=SE1405');
    exit();
}
$operatorRole = $resGetOperatorDetailArray['role'];
// check user role: admin(role=1) is OK
if ($operatorRole != 1) {
    $dbConnectObj->rollback();
    $dbConnectObj->close();
    // redirect to error page
    header('location: ./error.php?ecode=SE1406');
    exit();
}

// 4. Get Company Info
$sqlGetCompanyInfo = "SELECT * FROM company c inner join operator o on c.operator_id = o.operator_id WHERE c.company_code = $searchCompanyCode AND c.delete_flag = 0 AND o.delete_flag = 0;";
$resGetCompanyInfo = $dbConnectObj->executeSql($sqlGetCompanyInfo);
if (!$resGetCompanyInfo || $resGetCompanyInfo == null) {
    $dbConnectObj->rollback();
    $dbConnectObj->close();
    // redirect to error page
    header('location: ./error.php?ecode=SE1407');
    exit();
}
$companyInfoArray = array();
$companyId   = '';
$companyCode = '';
$companyName = '';
$storeCode   = '';
while ($row = mysqli_fetch_array($resGetCompanyInfo)) {
    $tmpArray = array();
    $tmpArray = array( 'company_id'            => $row["company_id"],
                       'company_code'          => $row["company_code"],
                       'company_name'          => $row["company_name"],
                       'store_code'            => $row["store_code"],
                       'store_name'            => $row["company_name"],
                       'classify_code'         => $row["classify_code"],
                       'deliver_charge'        => $row["deliver_charge"],
                       'slip_type'             => $row["slip_type"],
                       'billing_type'          => $row["billing_type"],
                       'tax_type'              => $row["tax_type"],
                       'store_address'         => $row["store_address"],
                       'store_phone_number'    => $row["store_phone_number"],
                       'billing_address'       => $row["billing_address"],
                       'billing_phone_number'  => $row["billing_phone_number"],
                       'operator_name'         => $row["operator_name"],
                       'operator_mail_address' => $row["operator_mail_address"],
                       'operator_phone_number' => $row["operator_phone_number"]
                     );
    $companyInfoArray[] = $tmpArray;
    $companyId   = $row["company_id"];
    $companyCode = $row["company_code"];
    $companyName = $row["company_name"];
    $storeCode   = $row["store_code"];
}

// 5. commit, close DB
$dbConnectObj->commit();
$dbConnectObj->close();

?>

      <!-- <div class="container-fluid">-->
        <div  class="row">
            <div >
            <div class="container">

              <!-- フォームの開始-->
              <div class="container col-md-5 col-sm-5 ">
                <form>
                  <br /><br />
                    <span style="padding-top:10px;"><b>企業コード</b>: <?php echo $companyCode; ?></span><br />
                    <span style="padding-top:10px;"><b>企業名</b>: <?php echo $companyName; ?></span><br />
                    <span style="padding-top:10px;"><a href="./edit_company_product_price.php?ccode=<?php echo $companyCode; ?>">この企業の商品価格を変更する</a></span><br />

                    <table class="table" style="padding-top:10px; width: 720px; margin-left: 30px; margin-top: 20px;">
                      <tbody>
                          <tr>
                            <td><b>店舗・担当者情報</b></td>
                            <td><a href="./edit_store_info.php?scode=<?php echo $storeCode; ?>">この店舗の情報を編集する</a></td>
                          </tr>
                          <tr>
                            <td>店舗コード</td>
                            <td><?php echo $companyInfoArray[0]['store_code']; ?></td>
                          </tr>
                          <tr>
                            <td>店舗名</td>
                            <td><?php echo $companyInfoArray[0]['store_name']; ?></td>
                          </tr>
                          <tr>
                            <td>分類コード</td>
                            <td><?php echo $companyInfoArray[0]['classify_code']; ?></td>
                          </tr>
                          <tr>
                            <td>配送料</td>
                            <td><?php echo $companyInfoArray[0]['deliver_charge']; ?></td>
                          </tr>
                          <tr>
                            <td>伝票タイプ</td>
                            <td><?php echo $companyInfoArray[0]['slip_type']; ?></td>
                          </tr>
                          <tr>
                            <td>請求タイプ</td>
                            <td><?php echo $companyInfoArray[0]['billing_type']; ?></td>
                          </tr>
                          <tr>
                            <td>課税タイプ</td>
                            <td><?php echo $companyInfoArray[0]['tax_type']; ?></td>
                          </tr>
                          <tr>
                            <td>店舗住所</td>
                            <td><?php echo $companyInfoArray[0]['store_address']; ?></td>
                          </tr>
                          <tr>
                            <td>店舗電話番号</td>
                            <td><?php echo $companyInfoArray[0]['store_phone_number']; ?></td>
                          </tr>
                          <tr>
                            <td>請求先住所</td>
                            <td><?php echo $companyInfoArray[0]['billing_address']; ?></td>
                          </tr>
                          <tr>
                            <td>請求先電話番号</td>
                            <td><?php echo $companyInfoArray[0]['billing_phone_number']; ?></td>
                          </tr>

                          <?php for ($i = 0; $i < count($companyInfoArray); $i++) { $j = $i+1; ?>
                            <tr>
                              <td><b>担当者 <?php echo '(' . $j . ')'; ?></b></td>
                              <td></td>
                            </tr>
                            <tr>
                              <td>担当者名</td>
                              <td><?php echo $companyInfoArray[$i]['operator_name']; ?></td>
                            </tr>
                            <tr>
                              <td>担当者メールアドレス</td>
                              <td><?php echo $companyInfoArray[$i]['operator_mail_address']; ?></td>
                            </tr>
                            <tr>
                              <td>担当者電話番号</td>
                              <td><?php echo $companyInfoArray[$i]['operator_phone_number']; ?></td>
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
