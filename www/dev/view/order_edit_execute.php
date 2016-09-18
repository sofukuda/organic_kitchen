<?php

// prepare
session_start();
include_once '../logic/common/_connect.inc'; // DB Connect Class
include_once '../logic/common/_common_method.inc'; // Common Method Class
// for php-5.6.2x
date_default_timezone_set('Asia/Tokyo'); // not need if php version is over 5.6.5

// 1. Get Parameters
$operator_id          = $_SESSION["id"];
$purchase_history_ids = $_REQUEST["purchaseHistoryIds"];
$total_price          = $_REQUEST["totalPrice"];

// db connect obj
$dbConnectObj = new DB_HANDLER();
$dbConnectObj->dbConnect();
$dbConnectObj->beginTransaction();
$commonMethodObj = new COMMON_METHOD($dbConnectObj);

// 2. Get & Make Array of PurchaseHistoryId and ChangeValue
$phId_chgValue_array  = array();
foreach ($purchase_history_ids as $purchase_history_id) {
    $str                   = 'changeValue_' . $purchase_history_id;
    $change_value          = $_REQUEST[$str];
    $phId_chgValue_array[] = array( "purchase_history_id" => $purchase_history_id,
                                    "change_value"        => $change_value );
}

// 3. Get Operator Info
$resGetOperatorInfoArray = array();
$resGetOperatorInfoArray = $commonMethodObj->getOperatorInfo($operator_id);
if (!$resGetOperatorInfoArray) {
    // redirect to error page
    header('location: ./error.php?ecode=SE120');
    exit();
}
$companyCode    = $resGetOperatorInfoArray['companyCode'];
$companyName    = $resGetOperatorInfoArray['companyName'];
$storeCode      = $resGetOperatorInfoArray['storeCode'];
$storeName      = $resGetOperatorInfoArray['storeName'];
$deliverCharge  = $resGetOperatorInfoArray['deliverCharge'];
$operatorName   = $resGetOperatorInfoArray['operatorName'];
$deliverAddress = $resGetOperatorInfoArray['deliverAddress'];

// 4. Execute Change Order
$newOrderId = time();
foreach ($phId_chgValue_array as $phId_chngVal) {
    // 4-1. Get PurchaseHistoryId, ChangeValue
    $targetPurchaseHistoryId = $phId_chngVal['purchase_history_id'];
    $changeValue             = $phId_chngVal['change_value'];
    // 4-2. Select PurchaseHistoryInfo By PurchaseHistoryId
    $sqlGetPurchaseInfo = "SELECT * FROM purchase_history WHERE purchase_history_id = $targetPurchaseHistoryId AND deliver_status = 0";
    $resGetPurchaseInfo = $dbConnectObj->executeSql($sqlGetPurchaseInfo);
    if (!$resGetPurchaseInfo || $resGetPurchaseInfo == null) {
        $dbConnectObj->rollback();
        $dbConnectObj->close();
        // redirect to error page
        header('location: ./error.php?ecode=SE121');
        exit();
    }
    while ($row = mysqli_fetch_array($resGetPurchaseInfo)) {
        $storeCode   = $row["store_code"];
        $productId   = $row["product_id"];
        $deliverDate = $row["deliver_date"];
    }
    // 4-3. Update Old PurchaseInfo, DeliverStatus -> 3
    $sqlUpdateDeliverStatusToCancel = "UPDATE purchase_history SET deliver_status = 3 WHERE purchase_history_id = $targetPurchaseHistoryId AND deliver_status = 0";
    $resUpdateDeliverStatusToCancel = $dbConnectObj->executeSql($sqlUpdateDeliverStatusToCancel);
    if (!$resUpdateDeliverStatusToCancel) {
        $dbConnectObj->rollback();
        $dbConnectObj->close();
        // redirect to error page
        header('location: ./error.php?ecode=SE122');
        exit();
    }
    // 4-4. Register Change Order Info To Purchase History
    $sqlRegistPurchaseHistory = "INSERT INTO purchase_history (`order_id`, `store_code`, `operator_id`, `product_id`, `deliver_date`, `purchase_number`) VALUES ($newOrderId, $storeCode, $operator_id, $productId, '$deliverDate', $changeValue);";
    $resRegistPurchaseHistory = $dbConnectObj->executeSql($sqlRegistPurchaseHistory);
    if (!$resRegistPurchaseHistory) {
        $dbConnectObj->rollback();
        $dbConnectObj->close();
        // redirect to error page
        header('location: ./error.php?ecode=SE123');
        exit();
    }
}

// 5. Register Change Order Info To Purchase History Plan (One Time)
$sqlRegisterChangedOrderInfo = "INSERT INTO regular_purchase_plan (`order_id`, `deliver_address`, `total_product_price`, `operator_id`, `store_code`, `created_datetime`) VALUES ($newOrderId, '$deliverAddress', $total_price, $operator_id, $storeCode, NOW());";
$resRegisterChangedOrderInfo = $dbConnectObj->executeSql($sqlRegisterChangedOrderInfo);
if (!$resRegisterChangedOrderInfo) {
    $dbConnectObj->rollback();
    $dbConnectObj->close();
    // redirect to error page
    header('location: ./error.php?ecode=SE124');
    exit();
}


// 5. Send Order Change Complete Mail
$message = "本メールはシステムより自動送信されています。
以下の内容での変更を承りました。
ご注文番号: $newOrderId

各注文が確定するのは配達予定日の前週木曜日の午前10:00です。
ご注文内容の変更は確定日時までにお願いいたします。
";
$send_result = _sendMail($operatorMailAddress, $message);

if ($send_result == true) {
    $dbConnectObj->commit();
    $dbConnectObj->close();
    // redirect to thanks page
    header('location: ./order_edit_complete.php');
    exit();
} else {
    $dbConnectObj->rollback();
    $dbConnectObj->close();
    // redirect to error page
    header('location: ./error.php?ecode=SE125');
    exit();
}


// private function
function _sendMail($sendMailAddress, $msg) {
    mb_internal_encoding('UTF-8');
    $mailto  = $sendMailAddress;
    $subject = "【自動送信】注文変更完了メール";
    $headers = "From: <info@organic-kitchen.co.jp> \n";
    $headers .= "Reply-To: <info@organic-kitchen.co.jp> \n";
    return mb_send_mail($mailto, $subject, $msg, $headers);
}


?>
