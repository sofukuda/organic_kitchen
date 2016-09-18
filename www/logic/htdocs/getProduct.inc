<?php

require_once "../common/connect.inc";
$connectObj = new DB_HANDLER();

// 登録済み届け先住所を取得
// 操作者のIDを返すAPIをコール, またはここで直接取得 author:あつお
// $operatorId = *****API;
$operatorId = 1; // temporary value

$sqlGetAddress = "
SELECT store_address
FROM company
WHERE operator_id = $operatorId
AND delete_flag = 0;
";

$connectObj->dbConnect();
$connectObj->beginTransaction();
$resGetAddress = $connectObj->executeDb($sqlGetAddress);
if (!$resGetAddress || $resGetAddress == null) {
    $connectObj->rollback();
    $connectObj->close();
    // redirect to error page
    // 届け先情報の取得に失敗
}
while ($row = mysqli_fetch_array($resGetAddress)) {
	$address = $row["store_address"];
}

// 商品情報を取得
// 現在有効な商品の商品ID, 商品名, 金額を取得
$sqlGetProduct = "
SELECT product_id, product_name, price
FROM product
WHERE delete_flag = 0;
";

$resGetProduct = connectObj->executeDb($sqlGetProduct);
if (!$resGetProduct || $resGetProduct == null) {
    $connectObj->rollback();
    $connectObj->close();
    // redirect to error page
    // 商品情報の取得に失敗
}
$connectObj->commit();
$connectObj->close();
$productInfoArray = array();
while ($row = mysqli_fetch_array($resGetProduct)) {
    $tmpArray = array();
    $tmpArray = array('product_id'   => $row["product_id"],
                      'product_name' => $row["product_name"],
                      'price'        => $row["price"]
                     );
    $productInfoArray[] = $tmpArray;
}
$allResultArray = array();
$allResultArray = array('address' => $address,
                        'productInfo' => $productInfoArray
                       );

print json_encode($allResultArray);
exit();

?>
