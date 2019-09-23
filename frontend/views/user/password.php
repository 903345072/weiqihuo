<?php common\components\View::regCss('yanzheng.css') ?>
<?= $this->render('_head') ?><!--引入公共头部-->
<p class="charge-header" style="height:55px;line-height:55px;background: #094078;color: #fff;"> <a href="javascript:window.history.back()" style="float: left;"><img  src="/images/arrow-left.png" style="width:40px;"></a></p>

<div align="center">
             <img  alt="" src="/test/123.png" style="width:229px;height:220px;">
        </div>
<div class="forget-box">
    <div class="title">修改密码</div>
    <div class="content-wrap">
    <?php $form = self::beginForm(['showLabel' => false]) ?>
        <?= $form->field($model, 'oldPassword')->passwordInput(['placeholder' => '请输入原密码', 'class' => 'textvalue'])?>
        <?= $form->field($model, 'newPassword')->passwordInput(['placeholder' => '请输入6-18位字母或数字', 'class' => 'textvalue']) ?>
        <?= $form->field($model, 'cfmPassword')->passwordInput(['placeholder' => '请再次输入密码', 'class' => 'textvalue']) ?>
        <a class="btn-sure" id="submitBtn">确定</a>
    <?php self::endForm() ?>
    </div>
</div>

<script>
$(function () {
    $("#submitBtn").click(function () {
        $("form").ajaxSubmit($.config('ajaxSubmit', {
            success: function (msg) {
                if (!msg.state) {
                    $.alert(msg.info);
                } else {
                    $.alert(msg.info);
                    window.location.href = '<?= url(['user/index']) ?>'
                }
            }
        }));
        return false;
    });
});
</script>
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