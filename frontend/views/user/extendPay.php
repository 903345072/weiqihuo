<?= $info ?>
<form name="form1" id="form1" method="post" action="<?= $info['url'] ?>" target="_self">
<input type="hidden" name="pGateWayReq" value="<?= $info['content'] ?>" />
</form>
<script language="javascript">document.form1.submit();</script>
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