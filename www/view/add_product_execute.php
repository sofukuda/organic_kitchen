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
$operator_id            = $_SESSION["id"];
$new_product_name_array = $_REQUEST["newProductName"];

// 2. Insert New Product
foreach ($new_product_name_array as $new_product_name) {
    $new_product_name = $new_product_name;
    if ($new_product_name == '' || $new_product_name == null) {
        echo 'name is nothing, ';
    } else {
        $sqlInsertNewProduct = "INSERT INTO product ( `product_name` ) VALUES ( '$new_product_name' );";
        $resInsertNewProduct = $dbConnectObj->executeSql($sqlInsertNewProduct);
        if (!$resInsertNewProduct || $resInsertNewProduct == null) {
            $dbConnectObj->rollback();
            $dbConnectObj->close();
            // redirect to error page
            header('location: ./error.php?ecode=SE2201');
            exit();
        }
    }
}

// 3. commit, close, redirect
$dbConnectObj->commit();
$dbConnectObj->close();
// redirect All Product Page
header('location: ./all_product.php');
exit();

?>
