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
$store_code           = $_REQUEST['storeCode'];
$start_date           = $_REQUEST['startDate'];
$deliver_address      = $_REQUEST['deliverAddress'];
$deliver_address_type = $_REQUEST['deliverAddressType'];
$product_id_array     = $_REQUEST['productIds'];
$total_price          = $_REQUEST['totalPrice'];
$send_fee             = $_REQUEST['sendFee'];
$deliver_charge_discount_flag = 0;
if ($send_fee == 0) {
  $deliver_charge_discount_flag = 1;
}

// 2. Transform Post Parameter
$orderProductDateNumArray = array();
for ($i = 0, $len = count($product_id_array); $i < $len; $i++) {
    $product_id  = $product_id_array[$i];
    for ($j = 0; $j < 5; $j++) {
        $date_num     = (int) date("Ymd", strtotime("$start_date +$j day"));
        $index_str    = $product_id . '_' . $date_num;
        $purchase_num = $_REQUEST[$index_str];
        $orderProductDateNumArray[] = array( 'product_id'      => $product_id,
                                             'deliver_date'    => date("Y/m/d", strtotime("$start_date +$j day")),
                                             'purchase_number' => $purchase_num );
    }
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
$operatorType        = $resGetOperatorInfoArray['operatorType'];
$operatorName        = $resGetOperatorInfoArray['operatorName'];
$operatorMailAddress = $resGetOperatorInfoArray['operatorMailAddress'];
$operatorPhoneNumber = $resGetOperatorInfoArray['operatorPhoneNumber'];
$role                = $resGetOperatorInfoArray['role'];

// 4. Publish New Order Id
// 発行方法について
$order_id = time();

// 5. Register Purchase Info By Order Unit
$regLastOrderInfo    = "INSERT INTO regular_purchase_plan (`order_id`, `total_product_price`, `operator_id`, `store_code`, `deliver_address`, `deliver_charge_discount_flag`, `created_datetime`) VALUES ($order_id, $total_price, $operator_id, $store_code, '$deliver_address', $deliver_charge_discount_flag, NOW());";
$resRegLastOrderInfo = $dbConnectObj->executeSql($regLastOrderInfo);
if (!$resRegLastOrderInfo || $resRegLastOrderInfo == null) {
    $dbConnectObj->rollback();
    $dbConnectObj->close();
    // redirect to error page
    header('location: ./error.php?ecode=SE006');
    exit();
}

// 6. Register Purchase History
foreach ($orderProductDateNumArray as $orderRecord) {
    $purchase_number = $orderRecord['purchase_number'];
    if ($purchase_number == 0) {
        continue;
    }
    $product_id      = $orderRecord['product_id'];
    $deliver_date    = date("Y-m-d", strtotime($orderRecord['deliver_date']));
    $sqlRegPurchaseHistory = "INSERT INTO purchase_history (`order_id`, `store_code`, `operator_id`, `product_id`, `deliver_date`, `purchase_number`) VALUES ($order_id, $store_code, $operator_id, $product_id, '$deliver_date', $purchase_number);";
    $resRegPurchaseHistory = $dbConnectObj->executeSql($sqlRegPurchaseHistory);
    if (!$resRegPurchaseHistory) {
        $dbConnectObj->rollback();
        $dbConnectObj->close();
        // redirect to error page
        header('location: ./error.php?ecode=SE007');
        exit();
    }
}

// あとで共通クラスにメソッドを作る
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
    $subject = "【自動送信】注文完了メール";
    $headers = "From: <info@organic-kitchen.co.jp> \n";
    $headers .= "Reply-To: <info@organic-kitchen.co.jp> \n";
    return mb_send_mail($mailto, $subject, $msg, $headers);
}


?>
