<?php

session_start();
include_once '../logic/common/_connect.inc'; // DB Connect Class
include_once '../logic/common/_common_method.inc'; // Common Method Class
// for php-5.6.2x
date_default_timezone_set('Asia/Tokyo'); // not need if php version is over 5.6.5

// db connect obj
$dbConnectObj = new DB_HANDLER();
$dbConnectObj->dbConnect();
$dbConnectObj->beginTransaction();
$commonMethodObj = new COMMON_METHOD($dbConnectObj);

// 1. Get Parameters
$operator_id          = $_SESSION["id"];
$productTotalPrice    = $_REQUEST['productTotalPrice'];
$wday                 = $_REQUEST['wday'];
$weekDeliverDateList  = $_REQUEST['weekDeliverDateList'];
$productIdList        = $_REQUEST['productId'];
$deliverAddress       = $_REQUEST['deliverAddress'];
$sendFee              = $_REQUEST['sendFee'];
$deliver_charge_discount_flag = 0;
if ($send_fee == 0) {
  $deliver_charge_discount_flag = 1;
}

// 2. Transform Post Parameter
$orderProductIdNum = array();
for ($i = 0, $len = count($productIdList); $i < $len; $i++) {
    $product_id  = $productIdList[$i];
    $orderNumber = $_REQUEST[$product_id];
    $orderProductIdNum += array( $product_id => $orderNumber );
}
$weekDayNum = (int)substr($wday, 0, 1)*16 + (int)substr($wday, 1, 1)*8 + (int)substr($wday, 2, 1)*4 + (int)substr($wday, 3, 1)*2 + (int)substr($wday, 4, 1)*1;

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
//$deliverCharge = $resGetOperatorInfoArray['deliverCharge'];
$operatorType        = $resGetOperatorInfoArray['operatorType'];
$operatorName        = $resGetOperatorInfoArray['operatorName'];
$operatorMailAddress = $resGetOperatorInfoArray['operatorMailAddress'];
$operatorPhoneNumber = $resGetOperatorInfoArray['operatorPhoneNumber'];
$role                = $resGetOperatorInfoArray['role'];

// 4. Publish New Order Id
$order_id = time();

// 5. Register Purchase Info By Order Unit
$regLastOrderInfo    = "INSERT INTO regular_purchase_plan (`order_id`, `order_type`, `deliver_address`, `total_product_price`, `operator_id`, `store_code`, `week_day_number`, `deliver_charge_discount_flag`, `created_datetime`) VALUES ($order_id, 1, '$deliverAddress', $productTotalPrice, $operator_id, $storeCode, $weekDayNum, $deliver_charge_discount_flag, NOW());";
$resRegLastOrderInfo = $dbConnectObj->executeSql($regLastOrderInfo);
if (!$resRegLastOrderInfo || $resRegLastOrderInfo == null) {
    $dbConnectObj->rollback();
    $dbConnectObj->close();
    // redirect to error page
    header('location: ./error.php?ecode=SE006');
    exit();
}

// 6. Register Purchase History
foreach ($weekDeliverDateList as $deliverDate) {
    foreach ($productIdList as $productId) {
        $purchase_number = $orderProductIdNum[$productId];
        $sqlRegPurchaseHistory = "INSERT INTO purchase_history (`order_id`, `store_code`, `operator_id`, `product_id`, `deliver_date`, `purchase_number`) VALUES ($order_id, $storeCode, $operator_id, $product_id, '$deliverDate', $purchase_number);";
        $resRegPurchaseHistory = $dbConnectObj->executeSql($sqlRegPurchaseHistory);
        if (!$resRegPurchaseHistory || $resRegPurchaseHistory == null) {
            $dbConnectObj->rollback();
            $dbConnectObj->close();
            // redirect to error page
            header('location: ./error.php?ecode=SE007');
            exit();
        }
    }
}

// fixme
// 7. Send Order Complete Mail
$message = "本メールはシステムより自動送信されています。
ご注文ありがとうございました。
ご注文番号: $order_id

各注文が確定するのは配達予定日の前週木曜日の午前10:00です。
ご注文内容の変更は確定日時までにお願いいたします。
";
$send_result = _sendMail($operatorMailAddress, $message);

if ($send_result == true) {
    $dbConnectObj->commit();
    $dbConnectObj->close();
    // redirect to thanks page
    header('location: ./order_thanks.php');
    exit();
} else {
    $dbConnectObj->rollback();
    $dbConnectObj->close();
    // redirect to error page
    header('location: ./error.php?ecode=SE009');
    exit();
}



// private function
function _sendMail($sendMailAddress, $msg) {
    mb_internal_encoding('UTF-8');
    $mailto  = $sendMailAddress;
    $subject = "定期注文完了メール";
    $headers = "From: <test.organic.kitchen@gmail.com> \n";
    $headers .= "Reply-To: <test.organic.kitchen@gmail.com> \n";
    return mb_send_mail($mailto, $subject, $msg, $headers);
}


?>
