<?php

// prepare
include_once '../logic/common/admin_header.inc'; // header, need HTML close tag in this code
include_once '../logic/common/_connect.inc'; // DB Connect Class
include_once '../logic/common/_common_method.inc'; // Common Method Class
// for php-5.6.2x
date_default_timezone_set('Asia/Tokyo'); // not need if php version is over 5.6.5

// 1. Get Operator Id
$operator_id = $_SESSION["id"];

// 2. Get URL Parameter
$companyCode = '';
if ($_REQUEST['ccode'] != '' && $_REQUEST['ccode'] != null) {
    $companyCode = $_REQUEST['ccode'];
} else {
    // redirect to company info page
    header('location: ./company_info.php');
    exit();
}

// 3. db connect obj
$dbConnectObj = new DB_HANDLER();
$dbConnectObj->dbConnect();
$dbConnectObj->beginTransaction();
$commonMethodObj = new COMMON_METHOD($dbConnectObj);

// 4. Get Operator Info
$resGetOperatorDetailArray = array();
$resGetOperatorDetailArray = $commonMethodObj->getOperatorDetail($operator_id);
if (!$resGetOperatorDetailArray) {
    $dbConnectObj->rollback();
    $dbConnectObj->close();
    // redirect to error page
    header('location: ./error.php?ecode=SE2401');
    exit();
}
$operatorRole = $resGetOperatorDetailArray['role'];
// check user role: admin(role=1) is OK
if ($operatorRole != 1) {
    $dbConnectObj->rollback();
    $dbConnectObj->close();
    // redirect to error page
    header('location: ./error.php?ecode=SE2402');
    exit();
}

// 5. Get Company Info
$resGetCompanyInfo = array();
$resGetCompanyInfo = $commonMethodObj->GetCompanyInfo($companyCode);
if (!$resGetCompanyInfo || $resGetCompanyInfo == null || count($resGetCompanyInfo) == 0) {
    $dbConnectObj->rollback();
    $dbConnectObj->close();
    // redirect to error page
    header('location: ./error.php?ecode=SE2403');
    exit();
}
foreach ($resGetCompanyInfo as $companyInfoRecord) {
    $company_code = $companyInfoRecord['companyCode'];
    $company_name = $companyInfoRecord['companyName'];
}

// 6. Get Product Master
$resGetProductMaster = array();
$resGetProductMaster = $commonMethodObj->GetProductMaster();
if (!$resGetProductMaster || $resGetProductMaster == null || count($resGetProductMaster) == 0) {
    $dbConnectObj->rollback();
    $dbConnectObj->close();
    // redirect to error page
    header('location: ./error.php?ecode=SE2404');
    exit();
}

// 7. Get Company Product Price
$resGetCompanyProductPrice = array();
$resGetCompanyProductPrice = $commonMethodObj->GetProductPricePerCompany($companyCode);
if (!$resGetCompanyProductPrice || $resGetCompanyProductPrice == null) {
    $dbConnectObj->rollback();
    $dbConnectObj->close();
    // redirect to error page
    header('location: ./error.php?ecode=SE2405');
    exit();
}
$productPriceArray = array();
while ($row = mysqli_fetch_array($resGetCompanyProductPrice)) {
    $productPriceArray[] = array( 'product_id' => $row["product_id"]
                                 ,'price'      => $row["price"]
                                 ,'sell_flag'  => $row["delete_flag"]
                                );
}

// 8. commit, close DB
$dbConnectObj->commit();
$dbConnectObj->close();

?>

      <!-- <div class="container-fluid">-->
        <div  class="row">
            <div >
            <div class="container">

              <!-- フォームの開始-->
              <div class="container col-md-5 col-sm-5 ">
                <form action ="./edit_company_product_price_execute.php" method="POST">
                  <input type="hidden" name="company_code" value="<?php echo $company_code; ?>">
                  <input type="hidden" name="company_name" value="<?php echo $company_name; ?>">

                  <br /><br />
                    <span style="padding-top:10px;"><b>企業コード</b>: <?php echo $company_code; ?></span><br />
                    <span style="padding-top:10px;"><b>企業名</b>: <?php echo $company_name; ?></span><br />

                    <table class="table" style="padding-top:10px; width: 720px; margin-left: 30px; margin-top: 20px;">
                      <tbody>
                        <tr>
                          <td><b>販売商品情報</b></td>
                          <td></td>
                          <td></td>
                        </tr>
                        <tr>
                          <td>商品名</td>
                          <td>販売価格</td>
                          <td>販売中/停止</td>
                        </tr>
                        <?php foreach ($resGetProductMaster as $productData) {
                                if ($productData['deleteFlag'] == 1) { continue; }
                                $product_id = $productData['productId'];
                                foreach ($productPriceArray as $companySellInfo) {
                                    if ($companySellInfo['product_id'] == $product_id) {
                                        $price     = $companySellInfo['price'];
                                        $sell_flag = $companySellInfo['sell_flag'];
                                    }
                                }
                        ?>
                          <tr>
                            <input type="hidden" name="product_id[]" value="<?php echo $product_id; ?>">
                            <td><?php echo $productData['productName']; ?></td>
                            <td><input type="text" style="width:200px; padding:6px 12px; height:34px; font-size:14px; border-radius:4px;" name="<?php echo $product_id; ?>_price" value="<?php echo $price; ?>"></td>
                            <td>
                              <select name="<?php echo $product_id; ?>_sell_select">
                                <option value="0" <?php if ($sell_flag == 0) { echo 'selected'; } ?>>販売中</option>
                                <option value="1" <?php if ($sell_flag == 1) { echo 'selected'; } ?>>販売停止</option>
                              </select>
                            </td>
                          </tr>
                        <? } ?>
                      </tbody>
                  </table>
                  <br />
                  <button type="submit" class="btn btn-primary btn-lg btn-block">決定</button>
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
