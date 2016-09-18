<?php

// prepare
include_once '../logic/common/header.inc'; // header, need HTML close tag in this code
include_once '../logic/common/_connect.inc'; // DB Connect Class
// for php-5.6.2x
date_default_timezone_set('Asia/Tokyo'); // not need if php version is over 5.6.5

// 1. Get Parameter
$operator_id = $_SESSION["id"];
$calStartDate = $_REQUEST["sdate"];

// db connect obj
$dbConnectObj = new DB_HANDLER();
$dbConnectObj->dbConnect();
$dbConnectObj->beginTransaction();

// 2. Get Contract Info
// 企業担当者と一般ユーザで分けるか？ ph.1では企業担当者のみに焦点を絞るが今後の拡張性も考慮した実装にしたい
$sqlGetContractInfo = "SELECT o.operator_name, o.operator_mail_address, o.operator_phone_number, c.company_code, c.company_name, c.store_code, c.store_name, c.classify_code, c.deliver_charge, c.slip_type, c.billing_type, c.tax_type, c.store_address, c.store_phone_number, c.billing_address, c.billing_phone_number FROM operator o inner join company c on o.operator_id = c.operator_id WHERE o.operator_id = $operator_id AND o.delete_flag = 0 AND c.delete_flag = 0;";
$resGetContractInfo = $dbConnectObj->executeSql($sqlGetContractInfo);
if (!$resGetContractInfo || $resGetContractInfo == null) {
    $dbConnectObj->rollback();
    $dbConnectObj->close();
    // redirect to error page
    header('location: ./error.php?ecode=SE101');
    exit();
}

while ($row = mysqli_fetch_array($resGetContractInfo)) {
    $operator_name         = $row["operator_name"];
    $operator_mail_address = $row["operator_mail_address"];
    $operator_phone_number = $row["operator_phone_number"];
    $company_code          = $row["company_code"];
    $company_name          = $row["company_name"];
    $store_code            = $row["store_code"];
    $store_name            = $row["store_name"];
    $classify_code         = $row["classify_code"];
    $deliver_charge        = $row["deliver_charge"];
    $slip_type             = $row["slip_type"];
    $billing_type          = $row["billing_type"];
    $tax_type              = $row["tax_type"];
    $store_address         = $row["store_address"];
    $store_phone_number    = $row["store_phone_number"];
    $billing_address       = $row["billing_address"];
    $billing_phone_number  = $row["billing_phone_number"];
}



?>


      <!-- <div class="container-fluid">-->
        <div  class="row">
            <div >
            <h2 class="page-header" style="margin-left:30px">契約内容</h2>
            <div class="container">


              <div class="container col-md-5 col-sm-5 ">

                    <!--テーブル-->

                    <table class="table" style="padding-top:10px; width: 480px;">
                      <thead>
                        <tr style="width: 50%;">
                          <th>項目</th>
                          <th>内容</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr>
                            <td>企業名</td>
                            <td><?php echo $store_name . ' ( ' . $company_name . ' )'; ?></td>
                        </tr>
                        <tr>
                            <td>配送先</td>
                            <td><?php echo $store_address; ?></td>
                        </tr>
                        <tr>
                            <td>担当者名</td><!-- 複数人の場合はここのtr内でカンマ区切りで表示 -->
                            <td><?php echo $operator_name; ?></td>
                        </tr>
                        <tr>
                            <td>連絡用メールアドレス</td>
                            <td><?php echo $operator_mail_address; ?></td>
                        </tr>
                        <tr>
                            <td>連絡用電話番号</td>
                            <td><?php echo $store_phone_number . ' ( ' . $operator_phone_number . ' )'; ?></td>
                        </tr>
                    </tbody>
                  </table>

                  <div >
                    <label>送料</label>
                    <p><?php echo $deliver_charge; ?> 円</p>
                  </div>
                  <!--
                  <div>
                    <br />
                    <button type="button" class="btn btn-primary btn-lg btn-block" onclick="location.href='./my_information_edit.php'">契約内容を変更する</button>
                  </div>
                  -->

                </div>

      </div>
      </div>


      <div id = "push"></div>

      <div id = "footer" >
        <div class="row">
            <div class="col-lg-12 col-sm-12 col-xs-12 ">
					<div class="container">

						<p class="text-muted fh5co-no-margin-bottom text-center"><small>&copy; 2016
						<a href="#">ORGANIC KITCHEN</a>. All rights reserved.
          </div>
          </div>
          </div>
			</div>

  </div>
  </body>

</html>
