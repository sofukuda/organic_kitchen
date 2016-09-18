<?php
    
    session_start();
    $_SESSION = array(); 
    session_destroy();

    header('location: ./login.php?ecode=205');
    
// 5秒後にメインメニューへ移動
//$location_msg = <<<END
//5秒後に <a href="main_menu.php">メインメニュー</a> に移動します。<br />
//<script type="text/javascript">
//<!--
//  setTimeout(function(){
//  location.replace('$url');
//  }, 5000);
//-->
//</script>
//END;
?>

    <div class="text-center">
      <img  src="images/logo.png" alt="" width="200" height="100" "></div>

          <div class="container">
          <section id="fh5co-newsletter">
            <div class="container">
              <div class="row">
                <div class="col-md-4 col-md-offset-4 col-sm-6 col-sm-offset-3 text-center">
                  <h2 class="fh5co-uppercase-heading-sm fh5co-no-margin-bottom">logout</h2>
                  <p>logout done.</p>
                  <div class="fh5co-spacer fh5co-spacer-xxs"></div>
                  </form>
                </div>
              </div>
            </div>
          </section>

          <div id = "push"></div>
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
  </body>

</html>
