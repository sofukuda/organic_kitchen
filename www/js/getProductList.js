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
                html += '<p class="product">'
                     + '<input type="hidden" name="product_id[]" value="' + productId + '">'
                     + '<a class="product_image"><img src="/img/' + imageUrl + '"></a><br /> '
                     + '<a class="product_name"><label>商品名: ' + productName + '</label></a><br />'
                     + '<a class="price"><label>金額: ' + price + ' 円</label></a><br />'
                     + '<a class="description" href="#">詳細:' + description + '</a><br />'
                     + '<a>注文数: <input type="number" name="product_id_' + productId + '" value="0"></a>'
                     + '</p>';
            }
            $('.productArea').append(html);
        },
        error: function( data ) {
            $( '#errorMsg' ).html( '<font color="red">商品一覧の表示に失敗しました</font>' );
        }
    });
});
