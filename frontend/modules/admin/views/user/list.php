<?php use common\helpers\Hui; ?>
<?php use common\helpers\Html; ?>

<?= $html ?>

<?php if (u()->isSuper()): ?>
<p class="cl pd-5 mt-20">
    <span>截止<?= self::$date ?>，共有<?= Html::redSpan($count) ?>个会员完成注册，交易数量已达<?= Html::redSpan($hand) ?>手，所有账户余额累计<?= Html::redSpan($amount) ?>元</span>
</p>

<?php endif ?>
<script>
$(function () {

    $(".list-container").on('click', '.editBtn', function () {
        var $this = $(this);
        $.prompt('请输入修改的密码', function (value) {
            $.post($this.attr('href'), {password: value}, function (msg) {
                if (msg.state) {
                    $.alert(msg.info || '修改成功');
                } else {
                    $.alert(msg.info);
                }
            }, 'json');
        });
        return false;
    });
    $(".list-container").on('click', '.moveBtn', function () {
        var $this = $(this);
        $.prompt('请输入要修改的经纪人id', function (value) {
            $.post($this.attr('href'), {admin_id: value}, function (msg) {
                if (msg.state) {
                    $.alert(msg.info || '修改成功');
                } else {
                    $.alert(msg.info);
                }
            }, 'json');
        });
        return false;
    });

    $(".list-container").on('click', '.deleteBtn', function () {
        var $this = $(this);
        $.get($(this).attr('href'), function (msg) {
            if (msg.state) {
                $.alert(msg.info, function () {
                    location.reload();
                });
            } else {
                $.alert(msg.info);
            }
        });
        return false;
    });
});
</script>