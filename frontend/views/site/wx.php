<?= $this->render('_head') ?><!--引入公共头部-->
<body style="">
        <form>
            <ul class="index-head flex col-w">
                    <li class="le"><a onclick="JavaScript:history.back(-1);" class="col-w"><i class="iconfont"></i></a></li>
                    <li class="mid">微信/支付宝/浏览器扫码下载APP</li>
                    <li class="ri"><a class="col-w"></a></li>
            </ul>
            <div style="height: .45rem; width: 100%;"></div>
            <img src="/images/gzh.jpg" width="100%">

        <?= $this->render('_foot') ?><!--引入公共底部-->
</form></body></html>
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