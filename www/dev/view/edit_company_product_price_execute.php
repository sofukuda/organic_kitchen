<?php

// prepare
session_start();
include_once '../logic/common/_connect.inc'; // DB Connect Class
include_once '../logic/common/_common_method.inc'; // Common Method Class
// for php-5.6.2x
date_default_timezone_set('Asia/Tokyo'); // not need if php version is over 5.6.5

// 1. Get Operator Id
$operator_id = $_SESSION["id"];

// 2. Get POST Parameter
$companyCode       = $_REQUEST['company_code'];
$companyIdList     = $_REQUEST['product_id'];
$updateProductList = array();
foreach ($companyIdList as $product_id) {
    $priceName = $product_id . '_price';
    $sellName  = $product_id . '_sell_select';
    $price     = $_REQUEST[$priceName];
    $sell      = $_REQUEST[$sellName];
    $updateProductList[] = array( 'product_id' => $product_id
                                 ,'price'      => $price
                                 ,'sell'       => $sell
                                );
}

// 3. db connect obj
$dbConnectObj = new DB_HANDLER();
$dbConnectObj->dbConnect();
$dbConnectObj->beginTransaction();
$commonMethodObj = new COMMON_METHOD($dbConnectObj);

// 4. Get Operator Detail
$resGetOperatorDetailArray = array();
$resGetOperatorDetailArray = $commonMethodObj->getOperatorDetail($operator_id);
if (!$resGetOperatorDetailArray) {
    $dbConnectObj->rollback();
    $dbConnectObj->close();
    // redirect to error page
    header('location: ./error.php?ecode=SE2601');
    exit();
}
$operatorRole = $resGetOperatorDetailArray['role'];
// check user role: admin(role=1) is OK
if ($operatorRole != 1) {
    $dbConnectObj->rollback();
    $dbConnectObj->close();
    // redirect to error page
    header('location: ./error.php?ecode=SE2602');
    exit();
}

// 5. Update Company Product Info
foreach ($updateProductList as $updateProductInfo) {
    $product_id = $updateProductInfo['product_id'];
    $price      = $updateProductInfo['price'];
    $sell       = $updateProductInfo['sell'];
    $sqlUpdateCompanyProductInfo = "UPDATE product_company SET price = $price, last_updated_operator_id = $operator_id, delete_flag = $sell WHERE product_id = $product_id AND company_code = $companyCode;";
    $resUpdateCompanyProductInfo = $dbConnectObj->executeSql($sqlUpdateCompanyProductInfo);
    if (!$resUpdateCompanyProductInfo || $resUpdateCompanyProductInfo == null) {
        $dbConnectObj->rollback();
        $dbConnectObj->close();
        // redirect to error page
        header('location: ./error.php?ecode=SE2603');
        exit();
    }
}

// 7. commit, close DB
$dbConnectObj->commit();
$dbConnectObj->close();

// redirect to error page
header("location: ./company_info_detail.php?ccode=$companyCode");
exit();

?>
