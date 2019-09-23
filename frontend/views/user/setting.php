<?php common\components\View::regCss('geren.css') ?>

<?= $this->render('_head') ?><!--引入公共头部-->
<!--头部导航-->
    <p class="charge-header" style="background: #094078;color: #fff;"> <a href="javascript:window.history.back()" style="float: left;"><img src="/images/arrow-left.png" style="width:40px;"></a><span>个人设置</span></p>
   
<div align="center">
             <img  alt="" src="/test/123.png" style="width:229px;height:220px;">
        </div>
<div class="forget">
    <div class="center-list-wrap">
        <ul>
            <!--li class="table bottom-wrap" data-index="0">
                <a href="<?= url(['user/password']) ?>" class="content-w">
                    <div class="content-wrap table-cell">
                        <div class="title">修改帐户密码</div>
                        <div class="title-tip">为了您的资金安全，请妥善保管您的帐户密码</div>
                    </div>
                </a>
                <div class="table-cell" style="padding-bottom: 40px;"><span class="earrow earrow-right"></span></div> 
            </li-->
            <li class="table bottom-wrap" data-index="1">
                <a href="<?= url(['user/changePhone']) ?>" class="content-w">
                    <div class="content-wrap table-cell">
                        <div class="title"><span>更换验证手机</span><span id="mobile" style="padding-left: 0.5em; color: #1d84d4; font-size: 13px;">
                        <?php if (strlen(u()->mobile) <= 10): ?>
                            您还未设置手机号码
                        <?php else : ?>
                            <?= substr(u()->mobile, 0, 3) . '*****' . substr(u()->mobile, -3) ?>
                        <?php endif ?>
                        </span></div>
                        <div class="title-tip">若您的验证手机丢失或停用，请立即更换</div>
                    </div>
                </a>
                <div class="table-cell" style="padding-bottom: 40px;"><span class="earrow earrow-right"></span></div>
            </li>
			<li class="table bottom-wrap" data-index="0">
                <a href="<?= url(['user/bankCard']) ?>" class="content-w">
                    <div class="content-wrap table-cell">
                        <div class="title">设置支付方式</div>
                        <div class="title-tip">为了您正常充值，请妥善完善您的充值信息</div>
                    </div>
                </a>
                <div class="table-cell" style="padding-bottom: 40px;"><span class="earrow earrow-right"></span></div> 
            </li>
        </ul>
    </div>
</div>
<link rel="stylesheet" href="/test/base1.css?r=20170520">
<link rel="stylesheet" href="/test/main.css?r=20170520">
<link rel="stylesheet" href="/test/main-blue.css?r=20170520">
<link href="/test/layer.css?2.0" type="text/css" rel="styleSheet" id="layermcss">
<?= $this->render('../site/_foot') ?><!--引入公共尾部--> 

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