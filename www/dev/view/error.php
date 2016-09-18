<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="utf-8">
    <link rel="shortcut icon" href="images/favicon.ico">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>オーガニックキッチン発注画面</title>
    <!-- BootstrapのCSS読み込み -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/my-custom.css" rel="stylesheet">

    <!-- jQuery読み込み -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <!-- BootstrapのJS読み込み -->
    <script src="js/bootstrap.min.js"></script>
  </head>

<?php

// Error Msg List
$errorMsgList = array(
                       'SE001' => 'DB Connect Error (Single Order): データベースの接続に失敗しました。',
                       'SE002' => 'Get User Info Error (Single Order): 会員情報の取得に失敗しました。',
                       'SE003' => 'Get Product Info Error (Single Order): 商品情報の取得に失敗しました。',
                       'SE004' => 'DB connect error (Single Order Confirm): データベースの接続に失敗しました。',
                       'SE005' => 'Get User Info Error (Single Order Confirm): 会員情報の取得に失敗しました。',
                       'SE006' => 'Register Regular Purchase Plan Error (Single Order Execute): 購入予定情報の登録に失敗しました。',
                       'SE007' => 'Register Purchase History Error (Single Order Execute): 購入履歴情報の登録に失敗しました。',
                       'SE008' => 'Pay By Credit Card Error (Single Order Execute): クレジットカード決済に失敗しました。',
                       'SE009' => 'Send Mail Error (Single Order Execute): 注文完了メールの送信に失敗しました。'
                     );


// Get URL Parameter
$errorCode = $_REQUEST['ecode'];
$eMsg      = $errorMsgList[$errorCode];

// fixme: eMsgはSystemErrorの時は全て「システムエラーが発生しました。管理者にお問い合わせください。」に統一するかどうか。
// トラブル対応時のロギングにさえ活用できれば特に問題ないはず
// $eMsg = 'システムエラーが発生しました。管理者にお問い合わせください。';

?>

  <body>
    <div id="wrap">
      <!-- <div class="container-fluid">-->
            <div >
            <h3 class="page-header" style="margin-left:20px"><?php echo $eMsg; ?></h2>
            </div>
              <div style="margin:20px">
              <p>お手数ですが、システム管理者にお問い合わせください。</p>
              </div>

            <div class="container col-md-5 col-sm-5 ">
              <button type="button" onclick="location.href='main_menu.php'" class="btn btn-primary btn-lg btn-block">TOPへ</button>
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
