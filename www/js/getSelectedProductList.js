$(function() {

    var resultGetUrlParams = {};
    var urlParamQuery      = '';
console.log('test');
    if (1 < window.location.search.length) {
        urlParamQuery = window.location.search.substring( 1 );
console.log('param: ' + urlParamQuery);
    }
console.log('test');

    $.ajax( {
        type: 'GET',
        url: "./htdocs/getSelectedProductList.php?" + urlParamQuery,
        dataType: 'json',
        success: function( data ) {
            var totalPrice = 0;
            var html       = '';
            for ( var i = 0; i < data.length; i++ ) {
                var productId     = data[i]["product_id"];
                var productName   = data[i]["product_name"];
                var imageUrl      = data[i]["img_url"];
                var price         = data[i]["price"];
                var description   = data[i]["description"];
                var selectedNum   = data[i]["selected_number"];
                var subTotalPrice = price * selectedNum;
                totalPrice        += subTotalPrice;
                html += '<p class="product">'
                      + '<a class="product_image"><img src="./img/' + imageUrl + '"></a>'
                      + '<a class="product_name"><label>商品名: ' + productName + '</label></a>'
                      + '<a class="price"><label>金額: ' + price + ' 円</label></a>'
                      + '<a class="description" href="#"><label>詳細: ' + description + '</label></a>'
                      + '<a class="purchase_number" name="purchase_number"><label>注文数: ' + selectedNum + '</label></a>'
                      + '<a class="subTotalPrice"><label>小計: ' + subTotalPrice + ' 円</label></a>'
                      + '</p>';
            }
            html += '<p><a class="totalPrice"><label>合計: ' + totalPrice + ' 円</label></a></p>'
            html += '<p><label>氏名: </label><input type="text" name="client_name" maxlength="32"></p>';
            html += '<p><label>送り先住所: </label><input type="text" name="address" maxlength="128"></p>';
            html += '<p><label>電話番号: </label><input type="text" name="phone_number" maxlength="13"></p>';
            html += '<p><label>メールアドレス: </label><input type="text" name="mail" maxlength="128"></p>';
            $('#productArea').append(html);
        },
        error: function( data ) {
            $( '#errorMsg' ).html( '<font color="red">商品の表示に失敗しました。再読み込みしてください。</font>' );
        }
    });
});
