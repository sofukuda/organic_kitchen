<?php

// prepare
include_once '../logic/common/admin_header.inc'; // header, need HTML close tag in this code
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

// 2. Get URL Parameter
if ($_REQUEST['search'] == 'n') {
    $searchTargetCompanyName = $_REQUEST['cname'];
}

// 3. Get Operator Detail
$resGetOperatorDetailArray = array();
$resGetOperatorDetailArray = $commonMethodObj->getOperatorDetail($operator_id);
if (!$resGetOperatorDetailArray) {
    $dbConnectObj->rollback();
    $dbConnectObj->close();
    // redirect to error page
    header('location: ./error.php?ecode=SE1401');
    exit();
}
$operatorRole = $resGetOperatorDetailArray['role'];
// check user role: admin(role=1) is OK
if ($operatorRole != 1) {
    $dbConnectObj->rollback();
    $dbConnectObj->close();
    // redirect to error page
    header('location: ./error.php?ecode=SE1402');
    exit();
}

// 4. Get Company Info
$sqlGetCompanyInfo = '';
if ($searchTargetCompanyName != '' && $searchTargetCompanyName != null) {
    $sqlGetCompanyInfo = "SELECT * FROM company WHERE company_name LIKE '%$searchTargetCompanyName%' AND delete_flag = 0 GROUP BY company_code;";
} else {
    $sqlGetCompanyInfo = "SELECT * FROM company WHERE delete_flag = 0 GROUP BY company_code;";
}
$resGetCompanyInfo = $dbConnectObj->executeSql($sqlGetCompanyInfo);
if (!$resGetCompanyInfo || $resGetCompanyInfo == null) {
    $dbConnectObj->rollback();
    $dbConnectObj->close();
    // redirect to error page
    header('location: ./error.php?ecode=SE1403');
    exit();
}
$companyInfoArray = array();
while ($row = mysqli_fetch_array($resGetCompanyInfo)) {
    $tmpArray = array();
    $tmpArray = array( 'company_id'   => $row["company_id"],
                       'company_code' => $row["company_code"],
                       'company_name' => $row["company_name"]
                     );
    $companyInfoArray[] = $tmpArray;
}

// 5. commit, close DB
$dbConnectObj->commit();
$dbConnectObj->close();

?>

    <script>
      function searchByCompanyName() {
        var targetCompanyName = document.getElementById("searchCompanyName").value;
        if (targetCompanyName == '' || targetCompanyName == null) {
          return false;
        }
        location.href = './company_info.php?search=n&cname=' + targetCompanyName;
      }
    </script>


      <!-- <div class="container-fluid">-->
        <div  class="row">
            <div class="container">

              <!-- フォームの開始-->
              <div class="container col-md-5 col-sm-5 ">

                <h3 style="margin-top:50px">【管理用】企業情報編集</h3>
                    <div>
                      <p>企業情報の編集・商品価格の変更が行えます。編集する企業の詳細情報をご確認ください。</p>
                    </div>

                <form>
                  <br /><br />
                  <table width="540">
                    <tr>
                      <td width="350">
                        <input name="searchCompanyName" type="text" id="searchCompanyName" style="width:340px; padding:6px 12px; height:34px; font-size:14px; border-radius:4px;" value="" placeholder="企業名で検索">
                      </td>
                      <td width="180">
                        <button type="button" style="width:180px; margin-left:10px; padding:6px 12px; height:34px; font-size:14px; border-radius:4px;" onClick="searchByCompanyName()">企業名で絞り込む</button>
                      </td>
                    </tr>
                  </table>
                    <!--テーブル-->

                    <table class="table" style="padding-top:10px; width: 720px;">
                      <thead>
                        <tr>
                          <th>企業コード</th>
                          <th>企業名</th>
                          <th>詳細情報</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php foreach ($companyInfoArray as $companyInfoRecord) {
                          $companyId   = $companyInfoRecord['company_id'];
                          $companyCode = $companyInfoRecord['company_code'];
                          $companyName = $companyInfoRecord['company_name'];
                        ?>
                          <tr>
                            <td><?php echo $companyCode; ?></td>
                            <td><?php echo $companyName; ?></td>
                            <td><a href="./company_info_detail.php?ccode=<?php echo $companyCode; ?>">詳細情報</a></td>
                          </tr>
                        <?php } ?>
                      </tbody>
                  </table>
                </form>
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
