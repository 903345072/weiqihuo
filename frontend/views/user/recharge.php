<?php $this->regCss('geren.css') ?>

<div class="personal">
    <p class="charge-header" style="background: #094078;color: #fff;"> <a href="javascript:window.history.back()" style="float: left;"><img src="/images/arrow-left.png" style="width:40px;"></a><span>充值</span></p>
    <div class="boxflex boxflex1">
        <div class="img-wrap"><img class="userimage" src="<?= u()->face==''?'/test/4.jpg':u()->face ?>"></div>
        <div class="box_flex_1">
            <div class="p_zichan"><?= u()->nickname ?></div>
        </div>
    </div>
    <?php $form = self::beginForm(['showLabel' => false, 'action' => url('user/pay/'), 'id' => 'payform']) ?>


    <div class="boxflex1 mt10">
        <div class="moneyhead">充值金额</div>
        <div class="group_btn clearfloat">
            <div class="btn_re btn_center">
                <a class="btn_money">100</a>
            </div>
            <div class="btn_re btn_center">
                <a class="btn_money">200</a>
            </div>
            <div class="btn_re btn_center">
                <a class="btn_money">500</a>
            </div>
			  <div class="btn_re btn_center">
                <a class="btn_money">1000</a>
            </div>
            <div class="btn_re btn_center">
                <a class="btn_money">2000</a>
            </div>

            <div class="btn_re btn_center">
                <a class="btn_money">5000</a>
            </div>


		    <div class="btn_re btn_center">
                <a class="btn_money">10000</a>
            </div>
		    <div class="btn_re btn_center">
                <a class="btn_money">20000</a>
            </div>

            <div class="btn_re btn_center">
                <a class="btn_money">50000</a>
            </div>



        </div>
            充值金额：<input style="width: 75%;text-align: right;" type="number" id="amount" name="amount" value="200">元

			<input type="hidden" id="is_moni" value="<?= u()->is_moni ?>"/>

    </div>
	<style>
	.onpay{
		border: 1px solid red;
	}
	</style>

   <!--<div class="boxflex1" style="border-top:none">
       <img style="width:22px;" src="/images/alipay.png" />
       <span>支付宝(线下)</span>
       <input type="radio" name="pay" checked="checked" value="zfb" style="float:right;padding: 5px 0;"/>
    </div>-->

    <div class="boxflex1" style="border-top:none;display: none">
        <img style="width:22px;" src="/images/seleted.png" />
        <span>在线充值(快捷)</span>
        <input checked type="radio" name="pay" value="zd" style="float:right;padding: 5px 0;"/>
    </div>


    <div class="boxflex1" style="border-top:none;display:none">
        <img style="width:22px;" src="/images/alipay.png" />
        <span>支付宝(在线)</span>
        <input type="radio" checked  name="pay" value="zhifubao" style="float:right;padding: 5px 0;"/>
    </div>

    <div class="boxflex1" style="border-top:none;display:none">
        <img style="width:22px;" src="/images/weixin.png" />
        <span>微信(在线)</span>
        <input type="radio" name="pay" value="weixin" style="float:right;padding: 5px 0;"/>
    </div>

    <div class="boxflex1" style="border-top:none;display:none">
        <img style="width:22px;" src="/images/weixin.png" />
        <span>人工客服</span>
        <input type="radio" name="pay" value="wx" style="float:right;padding: 5px 0;"/>
    </div>





    <div class="boxflex1" style="border-top:none;display:none">
        <img style="width:22px;" src="/images/alipay.png" />
        <span>支付宝充值</span>
        <input type="radio" name="pay" value="zd2" style="float:right;padding: 5px 0;"/>
    </div>

   <div class="boxflex1" style="border-top:none;display:none">
        <img style="width:22px;" src="/images/arrow-left.png" />
        <span>银联支付</span>
        <input type="radio" name="pay" value="zz" style="float:right;padding: 5px 0;"/>
    </div>
<!---->
<!--    <div class="boxflex1" style="border-top:none">-->
<!--        <img style="width:22px;" src="/images/pay.png" />-->
<!--        <span>银联</span>-->
<!--        <input type="radio" name="pay" value="yl" style="float:right;padding: 5px 0;"/>-->
<!--    </div>-->

    <div class="recharge-btn mt10" id="payBtn" name="wx" value="wx">确认充值</div>
    <?php self::endForm() ?>
</div>
<?= $this->render('../site/_head') ?><!--引入公共头部-->
<?= $this->render('../site/_foot') ?><!--引入公共尾部-->


<script type="text/javascript">
    $(".btn_money").click(function(){
        $('.clearfloat .btn_money').removeClass("active");
        $('#amount').val($(this).html());
        $(this).addClass("active");
    });

    $('#payBtn').on('click', function(){
        $("#payform").submit();
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
