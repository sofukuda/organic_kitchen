<?php

/**
 * UI各種ページからコールされる商品情報関連ページに共通して利用されるクラス
 * author @sofukuda
 */

class UI_PRODUCT_COMMON
{

    public function __construct() {
    }

    /**
     * transformSelectedProductAndNumber
     * 選択された商品情報と、選択された個数が格納されている配列を整形(マージ)する
     * 
     * @param allProductArray            選択された商品情報の配列
     * @param selectedProductNumberArray 選択された個数の配列
     *
     */
    public function transformSelectedProductAndNumber($allProductArray, $selectedProductNumberArray) {

        foreach ($allProductArray as $index) {

            $selectedProductId = $allProductArray[$index]['product_id'];

            foreach ($selectedProductNumberArray as $selectedProductNumber) {

                $allProductArray[$index]['selected_product_number'] = $selectedProductNumber;

            }

        }

    }

}

?>
