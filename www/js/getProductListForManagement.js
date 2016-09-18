$(function() {
    $.ajax( {
        type: 'GET',
        url: "./htdocs/getProductList.php",
        dataType: 'json',
        success: function( data ) {
            var html = '';
            for ( var i = 0; i < data.length; i++ ) {
                var productId   = data[i]["product_id"];
                var productName = data[i]["product_name"];
                var imageUrl    = data[i]["img_url"];
                var price       = data[i]["price"];
                var description = data[i]["description"];
                var deleteFlag  = data[i]["delete_flag"];

                html += '<p class="product">';

                if (deleteFlag == 0) {
                    html += '<a style="color: #ff0033;"><b>提供中</b></a><br />';
                } else if (deleteFlag == 1) {
                    html += '<a><b>提供外</b></a><br />';
                }

                html += '<input type="hidden" name="product_id[]" value="' + productId + '">'
                     + '<a class="product_image"><img src="/img/' + imageUrl + '"></a><br /> '
                     + '<a class="product_name"><label>商品名: ' + productName + '</label></a><br />'
                     + '<a class="price"><label>金額: ' + price + ' 円</label></a><br />'
                     + '<a class="description" href="#">詳細:' + description + '</a><br />'
                     + '<input class="btnNormal" type="button" value="変更" onClick="document.location=\'managementProductEdit.php\';">'
                     + '</p>';
            }
            $('.productArea').append(html);
        },
        error: function( data ) {
            $( '#errorMsg' ).html( '<font color="red">商品一覧の表示に失敗しました</font>' );
        }
    });
});
