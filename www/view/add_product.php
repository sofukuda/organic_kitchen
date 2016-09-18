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

?>

  <script>

    function addProductNameBox() {

      var html = '<tr><td><input type="text" style="width:100%; padding:6px 12px; height:34px; font-size:14px; border-radius:4px;" name="newProductName[]" value="" placeholder="新規商品名を入力"></td></tr>';
      $('#newProductNameArea tbody').append(html);

    }

  </script>

  <!-- <div class="container-fluid">-->
    <div  class="row">
      <div >
        <div class="container">
          <br />
          <div class="container col-md-5 col-sm-5 ">
            <form method="POST" action="./add_product_execute.php">
            <table id="newProductNameArea" class="table" style="padding-top:10px; width: 480px; margin-top: 20px;">
              <thead>
                <tr>
                  <th>新規商品名</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td><input type="text" style="width:100%; padding:6px 12px; height:34px; font-size:14px; border-radius:4px;" name="newProductName[]" value="" placeholder="新規商品名を入力"></td>
                </tr>
              </tbody>
            </table>
            <button type="button" class="btn btn-default btn-lg btn-block" onClick="addProductNameBox()">さらに追加</button>
            <br />
            <button type="submit" class="btn btn-primary btn-lg btn-block">決定</button>
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
