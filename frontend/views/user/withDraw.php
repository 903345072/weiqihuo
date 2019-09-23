
<?php $this->regCss('geren.css') ?>
<?php $this->regCss('select.css') ?>

<style type="text/css">
    .withdrawal-name {
        line-height: 30px;
    }
    .withdrawal-con .control-style {
        line-height: 30px;
    }
    .boxflex1 .get-btn {
        line-height: 30px;
    }
    .withdrawal-con.yanzheng {
        width: 40%;
    }
    .boxflex1 .get-btn {
        border: 1px solid #0066FF;
        padding:0 10px;
    }
</style>
<div class="personal">
 <p class="charge-header">
        <a href="javascript:window.history.back()" style="float: left;"><img src="/images/arrow-left.png" style="width:40px;"></a><span>提现</span></p>
    <div class="boxflex boxflex1">
        <div class="img-wrap"><img class="userimage" src="<?= u()->face==''?'/test/4.jpg':u()->face ?>"></div>
        <div class="box_flex_1">
            <div class="p_zichan"><?= u()->nickname ?></div>
            <div class="cash">可提现金额：<b class="mon"><?= ($user->account - $user->blocked_account)*98/100 ?></b>元</div>
        </div>
    </div>
    <?php $form = self::beginForm(['showLabel' => false]) ?>
    <div class="boxflex1 mt10 clearfloat">
        <div class="withdrawal-name">提现金额</div>
        <div class="withdrawal-con">
            <?= $form->field($userWithdraw, 'amount')->textInput(['placeholder' => '请输入提现金额', 'class' => 'control-style']) ?>
        </div>
    </div>
    <div class="boxflex1 mt10">
        <div class="moneyhead">提现方式</div>
    </div>
    <div class="boxflex1 none none">
        <img src="/images/pay.png"  style="width: 30px;"/>
        <span>银联支付</span>
        <img src="/images/seleted.png" alt="" style="float:right;padding: 0px 0;">
    </div>
    <div class="boxflex1 none clearfloat">
        <div class="withdrawal-name">银行</div>
        <div id="dd" class="wrapper-dropdown-1" tabindex="1">
            <?= $form->field($userAccount, 'bank_name')->textInput(['placeholder' => '请输入开卡银行', 'class' => 'control-style'])  ?>
        </div>
    </div>

    <div class="boxflex1 none clearfloat">
        <div class="withdrawal-name">卡号</div>
        <div class="withdrawal-con" tabindex="1">
            <?= $form->field($userAccount, 'bank_card')->textInput(['placeholder' => '请输入卡号', 'class' => 'control-style']) ?>
        </div>
    </div>
    <div class="boxflex1 none clearfloat">
        <div class="withdrawal-name">持卡人</div>
        <div class="withdrawal-con" tabindex="1">
            <?= $form->field($userAccount, 'realname')->textInput(['placeholder' => '请输入持卡人姓名', 'class' => 'control-style']) ?>
        </div>
    </div>
    <div class="boxflex1 none clearfloat">
        <div class="withdrawal-name">开卡行详细地址</div>
        <div class="withdrawal-con" tabindex="1">
            <?= $form->field($userAccount, 'bank_address')->textInput(['placeholder' => '正确格式：xx省xx市xx支行']) ?>
        </div>
    </div>
    <div class="boxflex1 none clearfloat">
        <div class="withdrawal-name">手机号</div>
        <div class="withdrawal-con" tabindex="1">
            <input type="text" value="<?= u()->mobile ?>" class="control-style" id="mobile" placeholder='请输入手机号' />
			<!--input type="text" value="<?= u()->mobile ?>" class="control-style" id="mobile" readonly="readonly" placeholder="<!--?= substr(u()->mobile, 0, 3) . '*****' . substr(u()->mobile, -3) ?>" /-->
        </div>
    </div>
	
    
    <div class="withdrawal-tips" style="margin-top:0">
        <ul>小提示:
		<input type="hidden" id="is_moni" value="<?= u()->is_moni ?>"/>
		<input type="hidden" id="user-verifycode"  class="control-style" name="UserAccount[verifyCode]" value = '1234' >
            <li>1、提现金额每笔不低于100元</li>
            <li>2、提现金额每笔不超过50000元</li>          
			<li>3、每笔提现扣除2%手续费</li>
			<li>4、请正确填写信息，如信息错误无法到账，提现不通过。</li>
			<li>审核时间：工作日9:00-16:00，在途资金隔天到账；<br>到账时间以银行为准，周末（节假日）顺延。</li>
            <li></li>
        </ul>
    </div>

    <div class="withdrawl-btn mt10" id="submitBtn">立即提现</div>
    <?= $form->field($userAccount, 'bank_mobile')->textInput(['type' => 'hidden', 'value' => u()->mobile]) ?>
    <?php self::endForm() ?>
	
</div>
<link rel="stylesheet" href="/test/base1.css?r=20170520">
<link rel="stylesheet" href="/test/main.css?r=20170520">
<link rel="stylesheet" href="/test/main-blue.css?r=20170520">
<link href="/test/layer.css?2.0" type="text/css" rel="styleSheet" id="layermcss">
     
<script>
$(function () {
    $("#submitBtn").click(function () {
		var is_moni = $('#is_moni').val();
		if(is_moni == 1){
			$.alert('非法操作！帐号类型不支持，请联系客服！');
			return false;
		}
		/*判断提现时间*/
		var hour = "<?php echo date('H'); ?>";
		var day  = "<?php echo date('w'); ?>";
		//alert('现在时间是:'+hour+'点')
		if(16 <= hour || hour <= 6 || day == 6 || day == 0){
			$.alert('请在工作日10:00~16:00时间段内重试。');
			return false;
		}
		
		
		
        $("form").ajaxSubmit($.config('ajaxSubmit', {
            success: function (msg) {
                if (!msg.state) {
                    $.alert(msg.info);
                } else {
                    $.alert(msg.info);
                    window.location.href = '<?= url('user/index') ?>'
                }
            }
        }));
        return false;
    });
    // 验证码
    $("#verifyCodeBtn").click(function () {
        var url = $(this).data('action');
        $.post(url, {mobile: '<?= u()->mobile ?>'}, function(msg) {
                $.alert(msg.info);
        }, 'json');
    });
});
</script>


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