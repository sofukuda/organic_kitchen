<?php

/**
 * ページ共通処理を実行するメソッドを提供するクラス
 * トランザクション処理内でのDB関連操作のみ提供する
 * author @sofukuda
 */

class COMMON_METHOD {

    function __construct($dbConnectObj)
    {
        $this->dbConnectObj = $dbConnectObj;
    }

    /**
     * getOperatorInfo
     * 操作者情報と所属企業情報を取得して返す
     *
     * @param int operator_id
     *
     * @return array
     *
     */
    public function getOperatorInfo($operator_id)
    {

        $sqlGetOperatorInfo = "SELECT * FROM company c inner join operator o on c.operator_id = o.operator_id WHERE o.operator_id = $operator_id";

        $resGetOperatorInfo = $this->dbConnectObj->executeSql($sqlGetOperatorInfo);

        if (!$resGetOperatorInfo || $resGetOperatorInfo == null) {
            $this->dbConnectObj->rollback();
            $this->dbConnectObj->close();
            return false;
        }

        $resArray = array();

        while ($row = mysqli_fetch_array($resGetOperatorInfo)) {

            $resArray = array( // company
                               'companyId'          => $row["company_id"]
                              ,'companyCode'        => $row["company_code"]
                              ,'companyName'        => $row["company_name"]
                              ,'storeCode'          => $row["store_code"]
                              ,'storeName'          => $row["store_name"]
                              ,'classifyCode'       => $row["classify_code"]
                              ,'deliverCharge'      => $row["deliver_charge"]
                              ,'deliverPriceThreshold' => $row["deliver_price_threshold"]
                              ,'slipType'           => $row["slip_type"]
                              ,'billingType'        => $row["billing_type"]
                              ,'taxType'            => $row["tax_type"]
                              ,'deliverAddress'     => $row["store_address"]
                              ,'storePhoneNumber'   => $row["store_phone_number"]
                              ,'billingAddress'     => $row["billing_address"]
                              ,'billingPhoneNumber' => $row["billing_phone_number"]
                               // operator
                              ,'operatorType'        => $row["operator_type"]
                              ,'operatorName'        => $row["operator_name"]
                              ,'operatorMailAddress' => $row["operator_mail_address"]
                              ,'operatorPhoneNumber' => $row["operator_phone_nubmer"]
                              ,'role'                => $row["role"]
                             );

        }

        return $resArray;

    }

    /**
     * getOperatorDetail
     * 操作者情報のみを取得して返す
     *
     * @param int operator_id
     *
     * @return array
     *
     */
    public function getOperatorDetail($operator_id)
    {

        $sqlGetOperatorDetail = "SELECT * FROM operator WHERE operator_id = $operator_id";

        $resGetOperatorDetail = $this->dbConnectObj->executeSql($sqlGetOperatorDetail);

        if (!$resGetOperatorDetail || $resGetOperatorDetail == null) {
            $this->dbConnectObj->rollback();
            $this->dbConnectObj->close();
            return false;
        }

        $resArray = array();

        while ($row = mysqli_fetch_array($resGetOperatorDetail)) {

            $resArray = array( // operator
                               'operatorId'          => $row["operator_id"]
                              ,'operatorType'        => $row["operator_type"]
                              ,'operatorName'        => $row["operator_name"]
                              ,'operatorMailAddress' => $row["operator_mail_address"]
                              ,'operatorPhoneNumber' => $row["operator_phone_number"]
                              ,'role'                => $row["role"]
            );

        }

        return $resArray;

    }


    /**
     * GetCompanyInfo
     * 指定された企業の企業情報を取得して返す
     *
     * @param int company_code
     *
     * @return array
     *
     */
    public function GetCompanyInfo($company_code)
    {

        $sqlGetCompanyInfo = "SELECT * FROM company WHERE company_code = $company_code AND delete_flag = 0;";

        $resGetCompanyInfo = $this->dbConnectObj->executeSql($sqlGetCompanyInfo);

        if (!$resGetCompanyInfo || $resGetCompanyInfo == null) {
            $this->dbConnectObj->rollback();
            $this->dbConnectObj->close();
            return false;
        }

        $resArray = array();

        while ($row = mysqli_fetch_array($resGetCompanyInfo)) {

            $resArray[] = array( 'companyId' => $row["company_id"]
                                ,'companyCode' => $row["company_code"]
                                ,'companyName' => $row["company_name"]
                                ,'storeCode' => $row["store_code"]
                                ,'storeName' => $row["store_name"]
                                ,'classifyCode' => $row["classify_code"]
                                ,'deliverCharge' => $row["deliver_charge"]
                                ,'deliverPriceThreshold' => $row["deliver_price_threshold"]
                                ,'slipType' => $row["slip_type"]
                                ,'billingType' => $row["billing_type"]
                                ,'taxType' => $row["tax_type"]
                                ,'operatorId' => $row["operator_id"]
                                ,'storeAddress' => $row["store_address"]
                                ,'storePhoneNumber' => $row["store_phone_number"]
                                ,'billingAddress' => $row["billing_address"]
                                ,'billingPhoneNumber' => $row["billing_phone_number"]
                                ,'' => $row["store_address"]
                               );
        }

        return $resArray;

    }


    /**
     * GetProductPricePerCompany
     * 企業ごとの商品販売情報を取得して返す
     *
     * @param int company_code
     *
     * @return array
     *
     */
    public function GetProductPricePerCompany($company_code)
    {

        $sqlGetProductPricePerCompany = "SELECT product_id, price, delete_flag FROM product_company WHERE company_code = $company_code;";

        $resGetProductPricePerCompany = $this->dbConnectObj->executeSql($sqlGetProductPricePerCompany);

        if (!$resGetProductPricePerCompany || $resGetProductPricePerCompany == null) {
            $this->dbConnectObj->rollback();
            $this->dbConnectObj->close();
            return false;
        }

        return $resGetProductPricePerCompany;

    }


    /**
     * GetProductMaster
     * 商品マスター情報を取得して返す
     *
     * @return array
     *
     */
    function GetProductMaster()
    {

        $sqlGetProductMaster = "SELECT * FROM product;";

        $resGetProductMaster = $this->dbConnectObj->executeSql($sqlGetProductMaster);

        if (!$resGetProductMaster || $resGetProductMaster == null || count($resGetProductMaster) == 0) {
            $this->dbConnectObj->rollback();
            $this->dbConnectObj->close();
            return false;
        }

        $resArray = array();

        while ($row = mysqli_fetch_array($resGetProductMaster)) {

            $resArray[] = array( 'productId'   => $row["product_id"]
                                ,'productName' => $row["product_name"]
                                ,'deleteFlag'  => $row["delete_flag"]
                               );

        }

        return $resArray;
        
    }


}

?>
