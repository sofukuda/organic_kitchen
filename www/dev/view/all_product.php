<?php

// prepare
include_once '../logic/common/header.inc'; // header, need HTML close tag in this code
include_once '../logic/common/_connect.inc'; // DB Connect Class
include_once '../logic/common/_common_method.inc'; // Common Method Class
// for php-5.6.2x
date_default_timezone_set('Asia/Tokyo'); // not need if php version is over 5.6.5

// 1. Get Operator Id
$operator_id = $_SESSION["id"];

// db connect obj
$dbConnectObj = new DB_HANDLER();
$dbConnectObj->dbConnect();
$dbConnectObj->beginTransaction();
$commonMethodObj = new COMMON_METHOD($dbConnectObj);

// 2. Get Operator Detail
$resGetOperatorDetailArray = array();
$resGetOperatorDetailArray = $commonMethodObj->getOperatorDetail($operator_id);
if (!$resGetOperatorDetailArray) {
    // redirect to error page
    header('location: ./error.php?ecode=SE1801');
    exit();
}
$operatorRole = $resGetOperatorDetailArray['role'];
// check user role: admin(role=1) is OK
if ($operatorRole != 1) {
    // redirect to error page
    header('location: ./error.php?ecode=SE1802');
    exit();
}

// 3. Get All Product
$sqlGetAllProduct = "SELECT * FROM product;";
$resGetAllProduct = $dbConnectObj->executeSql($sqlGetAllProduct);
if (!$resGetAllProduct || $resGetAllProduct == null) {
    $dbConnectObj->rollback();
    $dbConnectObj->close();
    // redirect to error page
    header('location: ./error.php?ecode=SE1403');
    exit();
}


?>


  <!-- <div class="container-fluid">-->
    <div  class="row">
      <div >
        <div class="container">
          <br />
          <h2>商品マスター</h2>
          <div class="container col-md-5 col-sm-5 ">
            <table class="table" style="padding-top:10px; width: 720px; margin-top: 20px;">
              <thead>
                <tr>
                  <th>商品名</th>
                  <th>登録日時</th>
                  <th>販売状態</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($resGetAllProduct as $productRecord) { ?>
                <tr>
                  <td><?php echo $productRecord['product_name']; ?></td>
                  <td><?php echo $productRecord['regist_datetime']; ?></td>
                  <td><?php if ($productRecord['delete_flag'] == 0) { echo '販売中'; } else { echo '販売停止中'; } ?></td>
                </tr>
                <?php } ?>
              </tbody>
            </table>
            <br />
            <button type="button" class="btn btn-primary btn-lg btn-block" onClick="location.href='./add_product.php'">新商品追加</button>
            <button type="button" class="btn btn-primary btn-lg btn-block" onClick="location.href='./edit_product.php'">商品編集</button>
          </div>
        </div><!-- /.container -->
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
