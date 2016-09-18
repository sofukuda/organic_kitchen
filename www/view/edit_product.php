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

$dbConnectObj->commit();
$dbConnectObj->close();

?>


  <!-- <div class="container-fluid">-->
    <div  class="row">
      <div >
        <div class="container">
          <br />
          <h2>商品マスター</h2>
          <div class="container col-md-5 col-sm-5 ">
            <form action="./edit_product_execute.php" method="POST">
              <table class="table" style="padding-top:10px; width: 720px; margin-top: 20px;">
                <thead>
                  <tr>
                    <th>商品名</th>
                    <th>販売状態</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($resGetAllProduct as $productRecord) { ?>
                  <tr>
                    <input type="hidden" name="product_id[]" value="<?php echo $productRecord['product_id']; ?>">
                    <td><input type="text" name="product_name_<?php echo $productRecord['product_id']; ?>" style="width:500px; padding:6px 12px; height:34px; font-size:14px; border-radius:4px;" value="<?php echo $productRecord['product_name']; ?>"></td>
                    <td>
                      <select name="sell_select_<?php echo $productRecord['product_id']; ?>">
                        <option value="0" <?php if ($productRecord['delete_flag'] == 0) { echo 'selected'; } ?>>販売</option>
                        <option value="1" <?php if ($productRecord['delete_flag'] == 1) { echo 'selected'; } ?>>販売停止</option>
                      </select>
                    </td>
                  </tr>
                  <?php } ?>
                </tbody>
              </table>
              <br />
              <button type="submit" class="btn btn-primary btn-lg btn-block">決定</button>
            </form>
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
