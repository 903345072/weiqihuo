<?php foreach ($data as $userCharge) :?>

<div style="white-space: nowrap;overflow: hidden;text-overflow:ellipsis;height:97px;" class="container">
	<div class="list fl">[昵称]<?= $userCharge->user->nickname ?></div>
    <div class="list fl" style="color:#000;text-align: center;white-space: nowrap;overflow: hidden;text-overflow:ellipsis;">手机号：<?= $userCharge->user->mobile ?></div>
    <div class="list fl" style="width:25%">[充值]快捷充值</div>
    <div class="list fl" style="color:#000;text-align: center;white-space: nowrap;overflow: hidden;text-overflow:ellipsis;">订单号：<?= $userCharge->trade_no ?></div>
    <div class="lisch fl"><span class="cz">充</span><b> <?= $userCharge->amount ?></b></div>

    <div class="lisch fl" style="color:#afaaaa;width:50%;text-align: center;"><?= $userCharge->created_at ?></div>
    <div class="clearfix" style="clear:both;"></div>
</div>
<?php endforeach ?>
        
        