<!-- <link rel="stylesheet" type="text/css" href="/css/login.css" /> -->
<?php common\components\View::regCss('iconfont/iconfont.css') ?>
<div class="container">
    <div class="row">
        <?= $url ?>
    </div>
</div>
    
<script language="javascript">  
   // setTimeout("window.location.href='<?= $url ?>';", 500);  
</script>  
<script>
document.addEventListener('plusready', function() {
    var webview = plus.webview.currentWebview();
    plus.key.addEventListener('backbutton', function() {
        webview.canBack(function(e) {
            if(e.canBack) {
                webview.back();
            } else {
                webview.close(); //hide,quit
                //plus.runtime.quit();
            }
        })
    });
});
</script>