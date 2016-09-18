<?php

try {

    $referer = $_SERVER["HTTP_REFERER"];

    $sql = '';
    if (strpos($referer,'order.php') !== false) { // fixme
        $sql = 'select * from product where delete_flag = 0';
    } else if (strpos($referer,'managementTop.php') !== false) { // fixme
        $sql = 'select * from product';
    } else {
        exit();
    }

    $_conn  = mysqli_connect(
                     'mysql435.db.sakura.ne.jp',
                     'organic-kitchen',
                     'obento2016',
                     'organic-kitchen_db'
             ) or die (
                     "Error " . mysqli_error($_conn)
             );

    $result = $_conn->query($sql);
    $resArr = array();

    while ($row = mysqli_fetch_array($result)){
        $tmpArray = array(
                          'product_id'      => $row["product_id"],
                          'product_name'    => $row["product_name"],
                          'img_url'         => $row["img_url"],
                          'price'           => $row["price"],
                          'description'     => $row["description"],
                          'regist_datetime' => $row["regist_datetime"],
                          'delete_flag'     => $row["delete_flag"]
        );
        $eventListArray[] = $tmpArray;
    }

    $_conn->close();
    echo json_encode($eventListArray);

} catch (Exception $e) {
        $error_code = '0001';
        $error_msg  = "DB error: Get All Product List is Failure.\n";
        $this->_redirectErrorPage($error_code, $error_msg);
}


?>
