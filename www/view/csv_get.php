<?php
// header 隱ｭ縺ｿ霎ｼ縺ｿ
include_once '../logic/common/admin_header.inc';

?>

<body>
  <div class="container">
      <h3 style="margin-top:50px">【管理用】受注データダウンロード</h3>
          <div style="margin-bottom:30px ">
            <span class="help-block">CSVで受注情報をダウンロードできます。</br>期間を「2016-01-01」の形式で記入して「CSVダウンロード」ボタンをクリックしてください</span>
          </div>


          <form  class="form-horizontal" action="csv_download.php" method="get">

           <div class="form-group">
                <label class="control-label col-xs-1" for=“from_date”>開始日</label>
                <div class="col-xs-11">
                   <input type="text" class="form-control"id="from_date" name="from_date" placeholder="2016-01-01" style="width:400px" >
                </div>
            </div>

            <div class="form-group">
                 <label class="control-label col-xs-1" for=“to_date”>終了日</label>
                 <div class="col-xs-11">
                    <input type="text" class="form-control" id="to_date" name="to_date" placeholder="2016-01-07" style="width:400px" >
                 </div>
             </div>

              <div class="form-group">
                <input class="btn btn-default  col-xs-5"  type="submit" value="CSV ダウンロード" style="margin-left:20px">
              </div>
          </form>


    </div>

      <div id = "footer" >
        <div class="row">
            <div class="col-lg-12 col-sm-12 col-xs-12 ">
                    <div class="container">

                        <p class="text-muted fh5co-no-margin-bottom text-center"><small>&copy; 2016
                        <a href="#">ORGANIC KITCHEN</a>. All rights reserved.
          </div>
          </div>
          </div>
            </div>

  </div>
</body>

</html>
