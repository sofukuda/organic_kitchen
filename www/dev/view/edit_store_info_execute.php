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
$companyCode        = $_REQUEST['company_code'];
$storeCode          = $_REQUEST['store_code'];
$storeName          = $_REQUEST['store_name'];
$classifyCode       = $_REQUEST['classify_code'];
$deliverCharge      = $_REQUEST['deliver_charge'];
$slipType           = $_REQUEST['slip_type'];
$billingType        = $_REQUEST['billing_type'];
$taxType            = $_REQUEST['tax_type'];
$storeAddress       = $_REQUEST['store_address'];
$storePhoneNumber   = $_REQUEST['store_phone_number'];
$billingAddress     = $_REQUEST['billing_address'];
$billingPhoneNumber = $_REQUEST['billing_phone_number'];
$operatorIdArray          = $_REQUEST['operator_id'];
$operatorNameArray        = $_REQUEST['operator_name'];
$operatorPhoneNumberArray = $_REQUEST['operator_phone_number'];
$operatorMailAddressArray = $_REQUEST['operator_mail_address'];

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
    header('location: ./error.php?ecode=SE1615');
    exit();
}
$operatorRole = $resGetOperatorDetailArray['role'];
// check user role: admin(role=1) is OK
if ($operatorRole != 1) {
    $dbConnectObj->rollback();
    $dbConnectObj->close();
    // redirect to error page
    header('location: ./error.php?ecode=SE1616');
    exit();
}

// 5. Update Store Info
$sqlUpdateStoreInfo = "UPDATE company SET store_name = '$storeName', classify_code = $classifyCode, deliver_charge = $deliverCharge, slip_type = '$slipType', billing_type = '$billingType', tax_type = '$taxType', store_address = '$storeAddress', store_phone_number = $storePhoneNumber, billing_address = '$billingAddress', billing_phone_number = $billingPhoneNumber WHERE store_code = $storeCode;";
$resUpdateStoreInfo = $dbConnectObj->executeSql($sqlUpdateStoreInfo);
if (!$resUpdateStoreInfo) {
    $dbConnectObj->rollback();
    $dbConnectObj->close();
    // redirect to error page
    header('location: ./error.php?ecode=SE1617');
    exit();
}

// 6. Update Operator Info
for ($i = 0; $i < count($operatorIdArray); $i++) {
    $operator_id           = $operatorIdArray[$i];
    $operator_name         = $operatorNameArray[$i];
    $operator_phone_number = $operatorPhoneNumberArray[$i];
    $operator_mail_address = $operatorMailAddressArray[$i];

    $sqlUpdateOperatorInfo = "UPDATE operator SET operator_name = '$operator_name', operator_mail_address = '$operator_mail_address', operator_phone_number = '$operator_phone_number' WHERE operator_id = $operator_id AND delete_flag = 0;";
    $resUpdateOperatorInfo = $dbConnectObj->executeSql($sqlUpdateOperatorInfo);

    if (!$resUpdateStoreInfo) {
        $dbConnectObj->rollback();
        $dbConnectObj->close();
    // redirect to error page
        header('location: ./error.php?ecode=SE1618');
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
