<?php

echo 'getProduct 1';
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

echo 'getProduct 2';

$connectObj = dbConnect();

echo 'getProduct 2-1';
beginTransaction();
echo 'getProduct 2-2';
$resGetAddress = executeDb($sqlGetAddress);
echo 'getProduct 2-3';
if (!$resGetAddress || $resGetAddress == null) {
echo 'getProduct 2-4';
    rollback();
    close();
echo 'getProduct 2-5';
    // redirect to error page
    // 届け先情報の取得に失敗
}
echo 'getProduct 3';
while ($row = mysqli_fetch_array($resGetAddress)) {
	$address = $row["store_address"];
}

echo 'getProduct 4';
// 商品情報を取得
// 現在有効な商品の商品ID, 商品名, 金額を取得
$sqlGetProduct = "
SELECT product_id, product_name, price
FROM product
WHERE delete_flag = 0;
";

$resGetProduct = executeDb($sqlGetProduct);
echo 'getProduct 5';
if (!$resGetProduct || $resGetProduct == null) {
    rollback();
    close();
    // redirect to error page
    // 商品情報の取得に失敗
}
$this->conn->commit();
$this->conn->close();
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



function dbConnect(){
    try {
echo 'db func 1';
        $this->conn = mysqli_connect(
                                "mysql435.db.sakura.ne.jp",
                                "organic-kitchen",
                                "obento2016",
                                "organic-kitchen_db"
                              )
                              or die
                              ("Error: " . mysqli_error($this->conn));
        return $this->conn;
    } catch (Exception $e) {
        $error_code = '0000';
        $error_msg  = "DB connect error: データベースの接続に失敗しました。";
    }
}

function beginTransaction(){
    $this->conn->autocommit(false);
    $this->conn->begin_transaction(MYSQLI_TRANS_START_READ_ONLY);
}

function executeDb($exec_sql){
    try {
        $exec_result = $this->conn->query($exec_sql);
        return $exec_result;
    } catch (Exception $e) {
        $this->rollback();
        $error_code = '0001';
        $error_msg  = "SQL Execute Error: クエリーの実行に失敗しました。";
    }
}

