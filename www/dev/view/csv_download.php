<?php
// term
//$start_date = '2016-07-10';
//$end_date =   '2016-09-01';
if($_GET['from_date']){$start_date = $_GET['from_date'];}
if($_GET['to_date'])  {$end_date =   $_GET['to_date'];}

// db connect obj 
try 
{
    $conn = mysqli_connect(
            "mysql435.db.sakura.ne.jp",
            "organic-kitchen", 
            "obento2016", 
            "organic-kitchen_db")
        or die ("Error: " . mysqli_error($conn));
} catch (Exception $e) {
    // print error
    print('a DB error occurs.');
    exit();
}   
$conn->query('SET NAMES utf8;');
$conn->query('begin;');

// 出力テーブル用の情報を取得
// 店舗コード、会社名、日付、商品数は購入履歴から取得
//$subscript = array('企業コード', '商品ID', '商品名', '商品金額');

$sqlGetOutputTableInfo = "
    SELECT
        product_company.company_code,
        product_company.product_id,
        product.product_name,
        product_company.price
    FROM
        product_company
    INNER JOIN
        product
    ON
        product_company.product_id = product.product_id
    AND
        product.delete_flag = 0
    AND
        product_company.delete_flag = 0
    ORDER BY
        company_code, product_id ;";


$resGetOutputTableInfo = $conn->query($sqlGetOutputTableInfo);

// DB から全レコード抽出
while($rowTableInfo = mysqli_fetch_array($resGetOutputTableInfo, $result_type = MYSQL_ASSOC)){
    $array_valueTableInfo = array_values($rowTableInfo);
    $recordsTableInfo[] = $array_valueTableInfo;
};

//sql 結果の確認用
//var_dump($sqlGetOutputTableInfo);
//var_dump($resGetOutputTableInfo);

// subscription の商品名作成
$subscript_item = "";
$pre_id = "";
foreach($recordsTableInfo as $record){
    if($record[0] == $pre_id){
        $subscript_item = $subscript_item + $record[2].",";
    }
//    print_r($record);
//    echo "<br>";
    $pre_id = $record[0];
}
//    print_r($subscript_item);
//    echo "<br>";



// 購入履歴情報を取る sql
//$conn->query('begin;');

$sqlGetAllPurchaseInfo = "
    SELECT
        company.company_id,
        company.company_code,
        company.company_name,
        purchase_history.deliver_date,
        product.product_id,
        product.product_name,
        purchase_history.purchase_number
    FROM
    (
        (
                company
            INNER JOIN
                purchase_history
            ON 
                purchase_history.operator_id = 1
            AND
                purchase_history.deliver_date BETWEEN '".$start_date."' AND '".$end_date."'
            AND
                purchase_history.deliver_status = 0
        )
        INNER JOIN
            product
        ON
            product.product_id = purchase_history.product_id
        AND
            product.delete_flag = 0
    )
    ORDER BY
        company.company_id, company.company_code, company.company_name, purchase_history.deliver_date, purchase_history.product_id ;";


//purchase_history.deliver_date BETWEEN '".$start_date."' AND '".$end_date."'

$resGetAllPurchaseInfo = $conn->query($sqlGetAllPurchaseInfo);
$conn->close();

// DB から全レコード抽出
while($row = mysqli_fetch_array($resGetAllPurchaseInfo, $result_type = MYSQL_ASSOC)){
    $array_value = array_values($row);
    $records[] = $array_value;
}

//sql 結果の確認用
//var_dump($sqlGetAllPurchaseInfo);
//var_dump($row);

header('Content-Type: application/octet-stream; charset=UTF-8');
header('Content-Disposition: attachment; filename=data.csv');

$stream = fopen('php://output', 'w');

// 結果の加工
$pre_company_id = "";
$pre_company_code = "";
$pre_company_name = "";
$pre_product_name = "";
$pre_deliver_date = "";
$pre_purchase_number = "";
$output_array = array();

//echo "テーブルリスト<br>";
foreach($records as $record){
    // 価格の挿入
    $price = "";
    foreach($recordsTableInfo as $recordTableInfo){
        if ($record[1] == $recordTableInfo[0]) {
            if($record[4] == $recordTableInfo[1]){
                $price = $recordTableInfo[3];
            } else {
                $table_purchase_number = 0;
            }
            if( $price && $table_purchase_number == 0){
                break;
            }
        }
    }
    if (!$price) {
        $price = "undefined";
    }
    unset($record[4]);
    array_splice($record, 5, 0, $price);

    // 同じレコードの整理
    if ($record[1] == $pre_company_code &&
        $record[3] == $pre_deliver_date &&
        $record[4] == $pre_product_name){
        // 出荷数を加算する
        $sum_purchase_number = $record[6] + $pre_purchase_number;
        array_splice($record, 6, 1, $sum_purchase_number);
    }
    // 出力用テーブル作成
    if ($record[0] == $pre_company_id &&
        $record[1] == $pre_company_code &&
        $record[2] == $pre_company_name &&
        $record[3] == $pre_deliver_date){

        } else if ($pre_company_id == "") {
            //subscription 生成 & 新しいレコード作成
            $subscript = array('企業コード', '店舗コード', '企業名', '日付');
            $output_array = array($record[0], $record[1], $record[2], $record[3]);
            foreach($recordsTableInfo as $recordTableInfo){
                if ($record[1] == $recordTableInfo[0]){
                    $subscript[] = $recordTableInfo[2]." の価格";
                    $subscript[] = $recordTableInfo[2]." の数量";
                    $output_array[] = $recordTableInfo[2];
                    $output_array[] = $recordTableInfo[3];
                    $output_array[] = 0;
                }
            }
//            print_r($subscript);
//            echo "<br>";
            fputcsv($stream, $subscript);
        }
         else {
            // 商品名の削除
            $count_array = count($output_array);
            for($i=4;$i<$count_array;$i++){
                if( $i%3 == 1 ){
                    unset($output_array[$i]);
                }
            }
            $output_array = array_values($output_array);

            // 前のレコード出力
//            print_r($output_array);
//            echo "<br>";
        fputcsv($stream, $output_array);

            //初期化して新しいレコード作成
            $output_array = array($record[0], $record[1], $record[2], $record[3]);
            foreach($recordsTableInfo as $recordTableInfo){
                if ($record[1] == $recordTableInfo[0]){
                    $output_array[] = $recordTableInfo[2];
                    $output_array[] = $recordTableInfo[3];
                    $output_array[] = 0;
                }
            }
         }
    //商品名と合うところに個数を上書き
    for($i=0;$i<count($output_array);$i++){
        if( $i%3 != 0 && $record[4] == $output_array[$i]){
//            echo $i."<br>";
            array_splice($output_array, $i+2, 1, array($record[6]));
        }
    }

    $pre_company_id   = $record[0];
    $pre_company_code = $record[1];
    $pre_company_name = $record[2];
    $pre_deliver_date = $record[3];
    $pre_product_name = $record[4];
    $pre_purchase_number = $record[6];



}
     // 商品名の削除
     $count_array = count($output_array);
     for($i=4;$i<$count_array;$i++){
         if( $i%3 == 1 ){
             unset($output_array[$i]);
         }
     }
     $output_array = array_values($output_array);
     // 最後のレコード出力
//     print_r($output_array);
//     echo "<br>";
     fputcsv($stream, $output_array);
