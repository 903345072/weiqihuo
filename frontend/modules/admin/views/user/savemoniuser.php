<?php $form = self::beginForm() ?>
<?= $user->title('模拟会员') ?>
<?= $form->field($user, 'username',['labelOptions'=>['label'=>'登陆帐号','class'=>'form-label col-sm-2']])->textInput(['placeholder' => '输入手机号码']) ?>
<?= $form->field($user, 'nickname',['labelOptions'=>['label'=>'客户昵称','class'=>'form-label col-sm-2']])->textInput(['placeholder' => '输入昵称或姓名']) ?>
<?= $form->field($user, 'password')->textInput(['placeholder' => $user->isNewRecord ? '' : '不填不修改，默认123456']) ?>
<?= $form->submit($user) ?>
<?php self::endForm() ?>

<script>
$(function () {
    $("#submitBtn").click(function () {
        $("form").ajaxSubmit($.config('ajaxSubmit', {
            success: function (msg) {
                if (msg.state) {
                    $.alert('操作成功', function () {
                        parent.location.reload();
                    });
                } else {
                    $.alert(msg.info);
                }
            }
        }));
        return false;
    });
});
</script>