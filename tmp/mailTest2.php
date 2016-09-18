<?php

mb_internal_encoding('UTF-8');
$mailto  = "spice.sister.arimino@gmail.com";
$subject = "【自動送信】メール送信テスト";
$message = "テストメールを送信します：\n";
$headers = "From: <test.organic.kitchen@gmail.com> \n";
$headers .= "Reply-To: <test.organic.kitchen@gmail.com> \n";
$result = mb_send_mail($mailto, $subject, $message, $headers);

?>
