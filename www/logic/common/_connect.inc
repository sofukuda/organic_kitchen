<?php

/**
 * 発行対象のSQLを取得・実行して、取得結果を返す接続クラス
 * author @sofukuda
 */

// common
//include_once '_sql_key.php';

class DB_HANDLER {

    function __construct()
    {
        $this->conn;

    }

    /**
     * 接続メソッド
     *
     */
    public function dbConnect()
    {

        $HOSTNAME     = "mysql435.db.sakura.ne.jp";
        $USERNAME     = "organic-kitchen";
        $DATABASENAME = "organic-kitchen_db";
        $PASSWORD     = "obento2016";

        try {

            $this->conn = mysqli_connect(
                                    $HOSTNAME,
                                    $USERNAME,
                                    $PASSWORD,
                                    $DATABASENAME
                                  )
                                  or die
                                  ("Error: " . mysqli_error($this->conn));
            // 文字コードのセット
            $this->conn->query('SET NAMES utf8');

        } catch (Exception $e) {

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
        // $this->conn->begin_transaction(MYSQLI_TRANS_START_READ_ONLY);
        $this->executeSql('begin;');

    }

    /**
     * 実行用メソッド
     *
     */
    public function executeSql($exec_sql)
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
