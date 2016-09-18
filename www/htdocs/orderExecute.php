<?php

/**
 * getSelectedProductList.php
 * 選択された商品情報を取得
 *
 **/

// common
// ユーザ認証、DB操作クラス
require_once "./deliverDB/common/connect.inc";
require_once "./ui_common.inc";

// UI共通クラス
$this->uiCommon        = new UI_COMMON();
$this->uiProductCommon = new UI_PRODUCT_COMMON();

// original
try {

    // 利用SQL(path, section)
    $sql_ini_file   = 'product_table.ini';
    $target_section = 'get_product_by_product_id';

    // パラメータ取得
    $selectedProductIdsArray  = $_REQUEST['productIds'];
    $selectedProductIdsNumber = $_REQUEST['productIdsNumber'];
    $clientId                 = $_REQUEST['clientId'];
    $clientName               = $_REQUEST['clientName'];
    $clientMail               = $_REQUEST['clientMail'];
    $clientAddress            = $_REQUEST['clientAddress'];
    $totalPrice               = $_REQUEST['totalPrice'];
    $paymentMethod            = $_REQUEST['paymentMethod'];

    // DB操作用クラス
    $dbHandle = new DB_HANDLER();

    // 商品一覧を取得
    $resultGetAllProduct = $dbHandle->getProductByProductId($sql_ini_file, $target_section, $selectedProductIdsArray);

    // 表示用にフォーマットを整形
    $resultTransformedSelectedProduct = $this->transformSelectedProductAndNumber($resultGetSelectedProduct, $selectedProductIdsNumber);

    // クレジットカード決済時の決済処理(代引き選択時はスキップ)
    #fixme

    // 注文確定メール送信用本文作成
    #fixme

} catch (Exception $e) {

    $this->uiCommon->redirectErrorPage(400, "ui error: 選択された商品情報の注文に失敗しました。\n");

}

?>
