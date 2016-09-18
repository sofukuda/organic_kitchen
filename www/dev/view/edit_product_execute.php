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
$operator_id      = $_SESSION["id"];
$product_id_array = $_REQUEST["product_id"];
$edit_product_array = array();
foreach ($product_id_array as $product_id) {
    $name_index   = "product_name_" . $product_id;
    $product_name = $_REQUEST[$name_index];
    $sell_index   = "sell_select_" . $product_id;
    $sell_flag    = $_REQUEST[$sell_index];
    $edit_product_array[] = array( 'product_id'   => $product_id,
                                   'product_name' => $product_name,
                                   'sell_flag'    => $sell_flag );
}

// 2. Update All Product Info
foreach ($edit_product_array as $edit_product_info_record) {
    $product_id   = $edit_product_info_record['product_id'];
    $product_name = $edit_product_info_record['product_name'];
    $sell_flag    = $edit_product_info_record['sell_flag'];
    $sqlUpdateProductInfo = "UPDATE product SET product_name = '$product_name', delete_flag = '$sell_flag' WHERE product_id = $product_id;";
    $resUpdateProductInfo = $dbConnectObj->executeSql($sqlUpdateProductInfo);
    if (!$resUpdateProductInfo || $resUpdateProductInfo == null) {
        $dbConnectObj->rollback();
        $dbConnectObj->close();
        // redirect to error page
        header('location: ./error.php?ecode=SE2001');
        exit();
    }
}

// 3. commit, close, redirect
$dbConnectObj->commit();
$dbConnectObj->close();
// redirect All Product Page
header('location: ./all_product.php');
exit();

?>
