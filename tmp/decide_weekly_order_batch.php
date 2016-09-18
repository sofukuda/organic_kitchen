<?
echo 'weekly batch start \n';
// 1. prepare
include_once '../www/dev/logic/common/_connect.inc'; // DB Connect Class
// for php-5.6.2x
date_default_timezone_set('Asia/Tokyo'); // not need if php version is over 5.6.5

// 2. DB connect obj
$dbConnectObj = new DB_HANDLER();
$dbConnectObj->dbConnect();
$dbConnectObj->beginTransaction();
$commonMethodObj = new COMMON_METHOD($dbConnectObj);
// write record
_writeRecord("batch record 0");
echo "batch record 0";

// 3. Get Purchase History, Regular Purchase Plan, Product Master Of Next Week
// variables
$today                 = date('Y-m-d');
$nextWeekStartDatetime = date('Y-m-d 00:00:00', strtotime("+4 day", strtotime($today))); // run every thursday
$nextWeekEndDatetime   = date('Y-m-d 23:59:59', strtotime("104 day", strtotime($today)));
// sql
$sqlGetUpdateDeliverStatusByWeeklyBatch = "SELECT
ph.order_id, ph.product_id, ph.deliver_date, ph.purchase_number,
rpp.deliver_address, rpp.total_product_price, rpp.operator_id, rpp.store_code,
p.product_name,
o.operator_name, o.operator_mail_address, o.operator_phone_number,
c.company_code, c.company_name, c.store_name, c.deliver_charge, c.slip_type, c.billing_type, c.tax_type, c.store_address, c.store_phone_number, c.billing_address, c.billing_phone_number
from purchase_history ph
inner join regular_purchase_plan rpp on ph.order_id = rpp.order_id
inner join product p on ph.product_id = p.product_id
inner join operator o on rpp.operator_id = o.operator_id
inner join company c on rpp.store_code = c.store_code
where ph.deliver_date >= '$nextWeekStartDatetime'
and ph.deliver_date <= '$nextWeekEndDatetime'
order by rpp.store_code, ph.deliver_date;";
// exec sql
$resGetUpdateDeliverStatusByWeeklyBatch = $dbConnectObj->executeSql($sqlGetUpdateDeliverStatusByWeeklyBatch);
if (!$resGetUpdateDeliverStatusByWeeklyBatch || !$resGetUpdateDeliverStatusByWeeklyBatch == false) {
    $dbConnectObj->rollback();
    $dbConnectObj->close();
    exit();
}
// write record
_writeRecord("batch record 1");
// transform result
$updateDeliverStatusArray = array();
while ($row = mysqli_fetch_array($resGetUpdateDeliverStatusByWeeklyBatch)) {
    $tmpArray = array( 'order_id'              => $row["order_id"],
                       'product_id'            => $row["product_id"],
                       'deliver_date'          => $row["deliver_date"],
                       'purchase_number'       => $row["purchase_number"],
                       'deliver_address'       => $row["deliver_address"],
                       'total_product_price'   => $row["total_product_price"],
                       'operator_id'           => $row["operator_id"],
                       'store_code'            => $row["store_code"],
                       'product_name'          => $row["product_name"],
                       'operator_name'         => $row["operator_name"],
                       'operator_mail_address' => $row["operator_mail_address"],
                       'operator_phone_number' => $row["operator_phone_number"],
                       'company_code'          => $row["company_code"],
                       'company_name'          => $row["company_name"],
                       'store_name'            => $row["store_name"],
                       'deliver_charge'        => $row["deliver_charge"],
                       'slip_type'             => $row["slip_type"],
                       'billing_type'          => $row["billing_type"],
                       'tax_type'              => $row["tax_type"],
                       'store_address'         => $row["store_address"],
                       'store_phone_number'    => $row["store_phone_number"],
                       'billing_address'       => $row["billing_address"],
                       'billing_phone_number'  => $row["billing_phone_number"]
                     );
    $updateDeliverStatusArray[] = $tmpArray;
}
// write record
_writeRecord("batch record 2");

// 4. Update Deliver Status 0:undecision -> 1:decision
$sqlUpdateDeliverStatusToDecision = "UPDATE purchase_history SET deliver_status = 1 WHERE deliver_status = 0 AND deliver_date >= '$nextWeekStartDatetime' AND deliver_date <= '$nextWeekEndDatetime';";
$resUpdateDeliverStatusToDecision = $dbConnectObj->executeSql($sqlUpdateDeliverStatusToDecision);
if (!$resUpdateDeliverStatusToDecision || $resUpdateDeliverStatusToDecision == null) {
    $dbConnectObj->rollback();
    $dbConnectObj->close();
    exit();
}
// write record
_writeRecord("batch record 3");

// 5. Send Mail Every Store
$pre_deliver_date = '';
$pre_store_code   = '';
$msg              = '本メールはシステムより自動送信されています。
ご注文ありがとうございました。以下の内容でご注文を承ります。
=============================================================
【注文内容】
';
$sub_total_price = 0;
$total_price = 0;
foreach ($updateDeliverStatusArray as $deliverData) {
    // get product price
    $product_id   = $deliverData['product_id'];
    $company_code = $deliverData['company_code'];
    $sqlGetCompanyProductPrice = "SELECT price FROM product_company WHERE product_id = $product_id AND company_code = $company_code AND delete_flag = 0;";
    $resGetCompanyProductPrice = $dbConnectObj->executeSql($sqlGetCompanyProductPrice);
    if (!$resGetCompanyProductPrice || $resGetCompanyProductPrice == null) {
        $dbConnectObj->rollback();
        $dbConnectObj->close();
        exit();
    }
    $price = 0;
    while ($row = mysqli_fetch_array($resGetCompanyProductPrice)) {
        $price = $row["price"];
    }

    // make message body
    if ($deliverData['store_code'] == $pre_store_code || $pre_store_code == '') {
        $sub_total_price = $price * $deliverData['purchase_number'];
        $msg            .= "
配達日：" . $deliverData['deliver_date'] . "
配達先住所：" . $deliverData['store_address'] . "
商品：" . $deliverData['product_name'] . "
単価：" . $price . "
注文数：" . $deliverData['purchase_number'] . "
小計：" . $sub_total_price;
        $total_price += $sub_total_price;
    } else {
        // send mail
        $msg .= "
=============================================================
合計：" . $total_price . "
配送料：" . $deliverData['deliver_charge'];
        $send_result = _sendMail($deliverData['operator_mail_address'], $msg);
        if (!$send_result) {
            $dbConnectObj->rollback();
            $dbConnectObj->close();
            exit();
        }
        // total_price, msg is void
        $total_price = 0;
        $msg = '';
    }

    // set new store code
    $pre_store_code = $deliverData['store_code'];

}


// private function
function _sendMail($sendMailAddress, $msg) {
    mb_internal_encoding('UTF-8');
    $mailto  = $sendMailAddress;
    $subject = "注文確定メール";
    $headers = "From: <test.organic.kitchen@gmail.com> \n";
    $headers .= "Reply-To: <test.organic.kitchen@gmail.com> \n";
    return mb_send_mail($mailto, $subject, $message, $headers);
}

function _writeRecord($txt) {

    $file = 'log.txt';
    $current = file_get_contents($file);
    $current .= "$txt\n";
    file_put_contents($file, $current);

}


?>
