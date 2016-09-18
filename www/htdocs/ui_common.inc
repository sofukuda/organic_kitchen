<?php

/**
 * UI各種ページからコールされるPHPに共通して利用されるクラス
 * author @sofukuda
 */

class UI_COMMON
{

    function __construct () {
echo "UI COMMON 01";
        $this->today = date('Y-m-d');

    }

    public function redirectErrorPage($errorCode, $errorMsg) {

        header("Location: http://organic_kitchen/error.php?error_code=$errorCode&$msg=$errorMsg");
        exit;

    }

    public function errorLog($code = null, $msg) {

        error_log($msg, 0, "/var/tmp/error_log/$this->today_error.log");

    }

    public function accessLog($code = null, $msg) {

        error_log($msg, 0, "/var/tmp/access_log/$this->today_access.log");

    }

}

?>
