<?php

/**
 * 発行対象のSQLを取得・実行して、取得結果を返す接続クラス
 * author @sofukuda
 */

// common
require_once(dirname(__FILE__) . './sql_key.php');

class DB_HANDLER {

    public __construct()
    {

        $this->conn;

    }

    /**
     * 接続メソッド
     *
     */
    public function dbConnect()
    {

        try {

            $this->conn = mysqli_connect(
                                    $HOSTNAME,
                                    $USERNAME,
                                    $DATABASENAME,
                                    $PASSWORD
                                  )
                                  or die
                                  ("Error: " . mysqli_error($this->conn));

        } catch {

            $error_code = '0000';
            $error_msg  = "DB connect error: データベースの接続に失敗しました。";
            $this->_redirectErrorPage($error_code, $error_msg);

        }

    }

    /**
     * 実行開始前メソッド
     *
     */
    public function beginTransaction()
    {

        $this->conn->autocommit(false);
        $this->conn->begin_transaction(MYSQLI_TRANS_START_READ_ONLY);

    }

    /**
     * コミット
     *
     */
    public function commit()
    {

        $this->conn->commit();

    }

    /**
     * ロールバック
     *
     */
    public function rollback()
    {

        $this->conn->rollback();

    }

    /**
     * 実行終了後メソッド
     *
     */
    public function close()
    {

        $this->conn->close();

    }

    /**
     * 内部で呼び出される実行用メソッド
     *
     */
    private function $_executeDb($exec_sql)
    {

        try {

            $exec_result = $this->conn->query($exec_sql);
            return $exec_result;

        } catch (Exception $e) {

            $this->rollback();

            $error_code = '0001';
            $error_msg  = "SQL Execute Error: クエリーの実行に失敗しました。";
            $this->_redirectErrorPage($error_code, $error_msg);

        }

    }

    /**
     * redirectErrorPage
     * エラーページにリダイレクトする
     *
     */
    private function _redirectErrorPage($errorCode, $errorMsg)
    {

        header("Location: http://organic_kitchen/error.php?error_code=$errorCode&$msg=$errorMsg");
        exit;

    }

}

?>
