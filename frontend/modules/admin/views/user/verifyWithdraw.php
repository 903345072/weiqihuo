<?php use common\helpers\Hui; ?>
<h2 style="text-align: center"><?= $model->user->nickname ?></h2>
<h3 style="text-align: center">余额<?= $model->user->account ?>，总充值<?= $UserChargecount ?>，总提现<?= $Withdrawtcount ?>，总盈亏<?= $plosscount ?></h3>
<?php $form = self::beginForm() ?>
<?= $form->field($model->user->userAccount, 'bank_name')->textInput(['disabled' => 'disabled']) ?>
<?= $form->field($model->user->userAccount, 'bank_card')->textInput(['disabled' => 'disabled']) ?>
<?= $form->field($model->user->userAccount, 'bank_address')->textInput(['disabled' => 'disabled']) ?>
<?= $form->field($model->user->userAccount, 'bank_user')->textInput(['disabled' => 'disabled']) ?>
<?= $form->field($model->user->userAccount, 'id_card')->textInput(['disabled' => 'disabled']) ?>
<?= $form->field($model->user->userAccount, 'bank_mobile')->textInput(['disabled' => 'disabled']) ?>
<?= $form->field($model, 'amount')->textInput(['value'=>number_format($model->amount,2,'.',''),'disabled' => 'disabled']) ?>
<?= $form->field($model, 'fee')->label('手续费')->textInput(['value'=>number_format($model->fee,2,'.',''),'disabled' => 'disabled']) ?>
<?= $form->field($model, 'op_reason')->textInput([]) ?>

<div class="row cl" style="margin: 0 auto;padding-left: 40%">
<?= Hui::successBtn('确认', null, ['id' => 'submitBtn', 'class' => 'size-L']) ?>
<?= Hui::dangerBtn('失败否决', null, ['id' => 'denyBtn', 'class' => 'size-L', 'style' => ['margin-left' => '20px']]) ?>
</div>
<?php self::endForm() ?>

<script>
$(function () {
    $("#submitBtn").click(function () {
        $.confirm('确认出金 ' + <?= number_format($model->amount-$model->fee,2,'.','') ?> + ' 元？', function () {
            $("form").ajaxSubmit($.config('ajaxSubmit', {
                data: {state: <?= $model::OP_STATE_PASS ?>},
                success: function (msg) {
                    if (msg.state) {
                        $.alert(msg.info || '操作成功', function () {
                            parent.location.reload();
                        })
                    } else {
                        $.alert(msg.info);
                    }
                }
            }));
        });
        return false;
    });

    $("#denyBtn").click(function () {
        $("form").ajaxSubmit($.config('ajaxSubmit', {
            data: {state: <?= $model::OP_STATE_DENY ?>,op_reason: $('#userwithdraw-op_reason').val()},
            success: function (msg) {
                if (msg.state) {
                    $.alert(msg.info || '操作成功', function () {
                        parent.location.reload();
                    })
                } else {
                    $.alert(msg.info);
                }
            }
        }));
    });
});
</script>