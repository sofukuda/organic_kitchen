<?php
  ob_start();
  session_start();
  $username = $_POST["username"];
  $password = $_POST["password"];
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
       // redirect to error page
       header('location: ./error.php?ecode=SE001');
       exit();
   }
   //$conn->begin_transaction(MYSQLI_TRANS_START_READ_ONLY);
   $conn->query('begin;');
   
   // login check
   $sqlGetUserInfo = "
       SELECT 
           operator_id, 
           user_id
       FROM
           operator
       WHERE
           user_id = '".$username."' AND 
           password = '".$password."';";

    $resGetUserInfo = $conn->query($sqlGetUserInfo);
    $conn->close();
    $row = mysqli_fetch_array($resGetUserInfo);
    if ($row) {
        //save session
        $user = $row["user_id"];
        $id = $row["operator_id"];
        $_SESSION["username"] = $user;
        $_SESSION["id"] = $id;
        header( "Location: ./main_menu.php" );
        exit();
    } else {
        header( "Location: ./login.php?ecode=401" );
        exit();
    }
?>
<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="utf-8">
    <link rel="shortcut icon" href="images/favicon.ico"> <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>オーガニックキッチン発注画面</title>
    <!-- BootstrapのCSS読み込み -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- jQuery読み込み -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <!-- BootstrapのJS読み込み -->
    <script src="js/bootstrap.min.js"></script>
  </head>

  <body>
    <div class="text-center">
    <img  src="images/logo.png" alt="" width="200" height="100">
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
  </body>

</html>
