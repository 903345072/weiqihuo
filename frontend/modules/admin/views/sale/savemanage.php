<?php $form = self::beginForm() ?>
<?= $user->title('经纪人') ?>
<?= $form->field($user, 'admin_id') ?>
<?= $form->field($user, 'username') ?>
<?= $form->field($user, 'nickname') ?>
<?= $form->field($user, 'mobile') ?>
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