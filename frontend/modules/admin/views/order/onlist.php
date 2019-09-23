<?php use common\helpers\Html; ?>

<?= $html ?>

<p class="cl pd-5 mt-20">
    <span class="countProfit">盈亏统计：<?= $profit >= 0 ? Html::redSpan($profit) : Html::greenSpan($profit) ?>，</span>
    <span>交易手数：<?= $hand >= 0 ? Html::redSpan($hand, ['class' => 'countHand']) : Html::greenSpan($hand, ['class' => 'countHand']) ?>，</span>
    <span>交易额统计：<?= $amount >= 0 ? Html::redSpan($amount, ['class' => 'countAmount']) : Html::greenSpan($amount, ['class' => 'countAmount']) ?>，</span>
    <span>手续费统计：<?= $fee >= 0 ? Html::redSpan($fee, ['class' => 'countFee']) : Html::greenSpan($fee, ['class' => 'countFee']) ?></span>
</p>
<script>
$(function () {
    $(".list-container").on('click', '.sellOrder', function () {
        var $this = $(this);
        $.prompt('请输入平仓价格', function (value) {
            $.post($this.attr('href'), {price: value}, function (msg) {
                if (msg.state) {
                    location.replace(location.href);
                } else {
                    $.alert(msg.info);
                }
            }, 'json');
        });
        return false;
    });  
});
</script>