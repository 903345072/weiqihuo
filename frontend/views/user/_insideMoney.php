<?php foreach ($data as $userWithdraw) :?>
<div class="container" style="white-space: nowrap;overflow: hidden;text-overflow:ellipsis;height:97px;">
	<div class="list fl">[昵称]<?= $userWithdraw->user->nickname ?></div>
    <div class="list fl" style="color:#000;text-align: center;white-space: nowrap;overflow: hidden;text-overflow:ellipsis;">手机号：<?= $userWithdraw->user->mobile ?></div>
    <div class="list fl"  style="width:25%">[提现]</div>
    <!--div class="list fl" style="color:#000;text-align: center;">状态：<?= $userWithdraw->getOpStateValue($userWithdraw->op_state) ?></div-->
    
	<?php 
		if($userWithdraw->op_state == '-1'){
	?>
	<div class="list fl" style="color:#000;    width: 30%;">状态：<?= $userWithdraw->getOpStateValue($userWithdraw->op_state) ?></div>
	<div class="list fl" style="color:#000;    width: 45%;    line-height: 15px;">理由：<?= $userWithdraw->op_reason ?></div>
	<?php 		
		}else{
	?>
	<div class="list fl" style="color:#000;    ">状态：<?= $userWithdraw->getOpStateValue($userWithdraw->op_state) ?></div>
	<?php 
		} 
	?>
    
   
    <div class="lisch fl" style="    clear: both;"><span class="cz">提</span><b> <?= $userWithdraw->amount ?></b></div>
    <div class="lisch fl"><span class="fy">费</span><?= ($userWithdraw->amount)*0.02 ?></div>
    <div class="lisch fl" style="color:#afaaaa;width:50%;text-align: center;"><?= $userWithdraw->created_at ?></div>
    <div class="clearfix" style="clear:both;"></div>
</div>
<?php endforeach ?>